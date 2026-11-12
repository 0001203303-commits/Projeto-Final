@php
    $rotulosStatus = ['online' => 'Online', 'offline' => 'Offline', 'manutencao' => 'Manutenção'];
@endphp

{{-- ================= API dos totens ================= --}}
<h3 class="titulo-secao" style="margin-top:0">API dos totens</h3>

<div class="kpis">
    <div class="kpi"><span>Totens cadastrados</span><b>{{ $totens->count() }}</b><small>cada um com seu token</small>
    </div>
    <div class="kpi {{ $totens->count() && $totens24h === 0 ? 'ruim' : 'bom' }}">
        <span>Com sinal nas últimas 24 h</span><b>{{ $totens24h }}</b><small>de {{ $totens->count() }}</small>
    </div>
    <div class="kpi"><span>Tempo para considerar offline</span><b>{{ $timeoutTotem }}<small> s</small></b><small>sem
            sinal de vida</small></div>
</div>

<div class="grade-2">
    <div class="cartao">
        <h3>Endereço do sinal de vida</h3>
        <dl class="dl-simples">
            <dt>Método e URL</dt>
            <dd>POST {{ url('/api/totem/sinal') }}</dd>
            <dt>Autenticação</dt>
            <dd>Authorization: Bearer (token do totem)</dd>
            <dt>Limite</dt>
            <dd>60 chamadas por minuto, por IP</dd>
        </dl>

        <div class="codigo-bloco">Invoke-RestMethod -Method Post -Uri "{{ url('/api/totem/sinal') }}" `
            -Headers @{ Authorization = "Bearer SEU_TOKEN" } `
            -ContentType "application/json" -Body '{"versao":"1.0.0"}'</div>

        <p class="msg-pequena">Tokens são gerados e renovados em <a
                href="{{ route('painel.totens.index') }}">Totens</a>.</p>
    </div>

    <div class="cartao form-painel">
        <h3>Editar tempo limite</h3>
        <form method="POST" action="{{ route('painel.bancoapi.totens.salvar') }}" novalidate>
            @csrf
            @method('PUT')
            <div class="campo">
                <label for="timeout-totem">Segundos sem sinal até marcar o totem como offline</label>
                <input type="number" id="timeout-totem" name="timeout" min="30" max="3600"
                    value="{{ old('timeout', $timeoutTotem) }}">
                @error('timeout')
                <div class="erro">{{ $message }}</div>@enderror
                <div class="dica">Entre 30 e 3600. Valores muito baixos podem marcar totens como offline por pequenas
                    oscilações de rede.</div>
            </div>
            <button type="submit" class="btn">Salvar</button>
        </form>
    </div>
</div>

<div class="tabela-wrap" style="margin-top:16px">
    <table class="tabela tabela-compacta">
        <thead>
            <tr>
                <th>Totem</th>
                <th>Status</th>
                <th>Último sinal</th>
                <th>Último IP</th>
                <th>Versão</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($totens as $t)
                <tr>
                    <td>{{ $t->nome }}</td>
                    <span class="badge-status {{ $t->status ?? 'desconhecido' }}">
                        {{ $rotulosStatus[$t->status] ?? 'Desconhecido' }}
                    </span>
                    <td>{{ $t->ultimo_sinal ? $t->ultimo_sinal->format('d/m/Y, H:i:s') : 'nenhum' }}</td>
                    <td>{{ $t->ip_ultimo ?? '—' }}</td>
                    <td>{{ $t->versao ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="sem-dados">Nenhum totem cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================= Gemini ================= --}}
<h3 class="titulo-secao">Integração com a IA (Gemini)</h3>

<div class="kpis">
    <div class="kpi {{ $gemini['ativo'] ? 'bom' : 'atencao' }}">
        <span>Estado</span><b>{{ $gemini['ativo'] ? 'Ativa' : 'Desativada' }}</b>
        <small>{{ $gemini['ativo'] ? 'resumos com IA ligados' : 'só resumo local' }}</small>
    </div>
    <div class="kpi">
        <span>Chave</span><b style="font-size:1.2rem">{{ $gemini['chave'] }}</b>
        <small>origem: {{ $gemini['origem'] }}</small>
    </div>
    <div class="kpi">
        <span>Modelo</span><b style="font-size:1.2rem">{{ $gemini['modelo'] }}</b>
        <small>tempo limite {{ $gemini['timeout'] }} s</small>
    </div>
    <div
        class="kpi {{ $ia['taxa'] === null ? '' : ($ia['taxa'] >= 90 ? 'bom' : ($ia['taxa'] >= 70 ? 'atencao' : 'ruim')) }}">
        <span>Sucesso nas últimas 24 h</span>
        <b>{{ $ia['taxa'] === null ? '—' : $ia['taxa'] . '%' }}</b>
        <small>{{ $ia['sucessos'] }} de {{ $ia['tentativas'] }} · média {{ $ia['media_ms'] }} ms</small>
    </div>
</div>

<div class="grade-2">
    <div class="cartao form-painel">
        <h3>Editar configuração</h3>

        <form method="POST" action="{{ route('painel.bancoapi.gemini.salvar') }}" novalidate>
            @csrf
            @method('PUT')

            <label class="interruptor">
                <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $gemini['ativo']))>
                Gerar resumos com IA
            </label>

            <div class="campo">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" value="{{ old('modelo', $gemini['modelo']) }}"
                    list="lista-modelos" autocomplete="off">
                <datalist id="lista-modelos"></datalist>
                @error('modelo')
                <div class="erro">{{ $message }}</div>@enderror
                <button type="button" class="btn claro" id="buscar-modelos" style="margin-top:8px">Buscar modelos
                    disponíveis</button>
                <div class="msg-pequena" id="msg-modelos"></div>
            </div>

            <div class="campo">
                <label for="timeout">Tempo limite (segundos)</label>
                <input type="number" id="timeout" name="timeout" min="5" max="60"
                    value="{{ old('timeout', $gemini['timeout']) }}">
                @error('timeout')
                <div class="erro">{{ $message }}</div>@enderror
            </div>

            <div class="campo">
                <label for="chave">Nova chave de API</label>
                <input type="password" id="chave" name="chave" autocomplete="new-password"
                    placeholder="Deixe em branco para manter a atual">
                @error('chave')
                <div class="erro">{{ $message }}</div>@enderror
                <div class="dica">A chave é guardada criptografada e nunca é exibida de novo, apenas mascarada.</div>
            </div>

            @if ($gemini['origem'] === 'painel')
                <label class="interruptor" style="font-weight:400">
                    <input type="checkbox" name="remover_chave" value="1">
                    Remover a chave salva no painel (volta a usar a do .env)
                </label>
            @endif

            <button type="submit" class="btn">Salvar configuração</button>
        </form>
    </div>

    <div>
        <div class="cartao">
            <h3>Testar conexão</h3>
            <p class="aviso">Envia um pedido mínimo com a configuração salva e mostra o resultado e o tempo de resposta.
            </p>
            <form method="POST" action="{{ route('painel.bancoapi.gemini.testar') }}">
                @csrf
                <button type="submit" class="btn destaque">Testar agora</button>
            </form>
        </div>

        <div class="cartao">
            <h3>Falhas recentes</h3>
            @if ($ia['falhas']->isEmpty())
                <p class="sem-dados">Nenhuma falha registrada nos últimos 30 dias. 👍</p>
            @else
                <div class="tabela-wrap">
                    <table class="tabela tabela-compacta">
                        <thead>
                            <tr>
                                <th>Quando</th>
                                <th>HTTP</th>
                                <th>Mensagem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ia['falhas'] as $f)
                                <tr>
                                    <td>{{ $f->created_at->format('d/m H:i') }}</td>
                                    <td>{{ $f->status_http ?? '—' }}</td>
                                    <td>{{ $f->mensagem ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('buscar-modelos')?.addEventListener('click', async e => {
        const botao = e.target;
        const msg = document.getElementById('msg-modelos');
        const original = botao.textContent;

        botao.disabled = true;
        botao.textContent = 'Buscando...';

        try {
            const r = await fetch(@json(route('painel.bancoapi.gemini.modelos')), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const d = await r.json();
            const lista = document.getElementById('lista-modelos');

            lista.innerHTML = '';
            (d.modelos || []).forEach(m => {
                const o = document.createElement('option');
                o.value = m;
                lista.appendChild(o);
            });

            msg.textContent = d.ok
                ? d.modelos.length + ' modelos encontrados. Clique no campo Modelo para escolher.'
                : d.mensagem;
        } catch (err) {
            msg.textContent = 'Não foi possível buscar os modelos.';
        }

        botao.disabled = false;
        botao.textContent = original;
    });
</script>