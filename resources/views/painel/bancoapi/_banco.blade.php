@php $fmt = fn ($b) => \App\Services\GerenciadorBanco::bytes($b); @endphp

@if (! $status['ok'])
    <div class="flash erro">Sem conexão com o banco de dados: {{ $status['erro'] }}</div>
@else
    <div class="kpis">
        <div class="kpi bom">
            <span>Conexão</span>
            <b>Conectada</b>
            <small>{{ $status['latencia_ms'] }} ms de resposta</small>
        </div>
        <div class="kpi">
            <span>Versão do servidor</span>
            <b style="font-size:1.3rem">{{ $status['versao'] }}</b>
            <small>{{ $status['banco'] }}</small>
        </div>
        <div class="kpi">
            <span>Tamanho do banco</span>
            <b>{{ $fmt($status['tamanho']) }}</b>
            <small>{{ count($tabelas) }} tabelas</small>
        </div>
        <div class="kpi {{ $migrations['pendentes'] > 0 ? 'atencao' : 'bom' }}">
            <span>Migrations</span>
            <b>{{ $migrations['pendentes'] > 0 ? $migrations['pendentes'] . ' pendente(s)' : 'Em dia' }}</b>
            <small>{{ $migrations['aplicadas'] }} aplicadas</small>
        </div>
    </div>

    <div class="grade-2">
        <div>
            <div class="cartao">
                <h3>Conexão (somente leitura)</h3>
                <dl class="dl-simples">
                    <dt>Banco</dt><dd>{{ $status['banco'] }}</dd>
                    <dt>Servidor</dt><dd>{{ $status['servidor'] }}</dd>
                    <dt>Usuário</dt><dd>{{ $status['usuario'] }}</dd>
                    <dt>Charset</dt><dd>{{ $status['charset'] }}</dd>
                </dl>
                <p class="msg-pequena">Os dados de conexão ficam no arquivo <code>.env</code> do servidor. Eles não são editáveis por aqui para evitar que um erro derrube o próprio painel.</p>
            </div>

            <div class="cartao">
                <h3>Migrations</h3>

                @if ($migrations['pendentes'] > 0)
                    <div class="aviso-token">
                        Há {{ $migrations['pendentes'] }} migration(s) pendente(s). Crie um backup antes de aplicar.
                    </div>
                    <form method="POST" action="{{ route('painel.bancoapi.migrations') }}"
                          data-confirmar="Aplicar as migrations pendentes agora? Recomenda-se ter um backup recente.">
                        @csrf
                        <button type="submit" class="btn">Aplicar migrations pendentes</button>
                    </form>
                @else
                    <p class="aviso">Todas as migrations estão aplicadas.</p>
                @endif

                <details style="margin-top:14px">
                    <summary>Ver lista ({{ count($migrations['lista']) }})</summary>
                    <div class="tabela-wrap">
                        <table class="tabela tabela-compacta">
                            <thead><tr><th>Migration</th><th>Situação</th></tr></thead>
                            <tbody>
                            @foreach ($migrations['lista'] as $m)
                                <tr>
                                    <td>{{ $m['nome'] }}</td>
                                    <td>{{ $m['batch'] === null ? 'Pendente' : 'Aplicada (lote ' . $m['batch'] . ')' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>
        </div>

        <div class="cartao">
            <h3>Backups</h3>

            <form method="POST" action="{{ route('painel.bancoapi.backup.criar') }}">
                @csrf
                <button type="submit" class="btn destaque">Criar backup agora</button>
            </form>

            <p class="msg-pequena">Os backups contêm dados de pacientes (LGPD): guarde em local seguro e exclua os que não forem mais necessários. A restauração é feita manualmente, por segurança.</p>

            @if (empty($backups))
                <p class="sem-dados" style="margin-top:12px">Nenhum backup criado ainda.</p>
            @else
                <div class="tabela-wrap" style="margin-top:12px">
                    <table class="tabela tabela-compacta">
                        <thead><tr><th>Arquivo</th><th>Tamanho</th><th></th></tr></thead>
                        <tbody>
                        @foreach ($backups as $b)
                            <tr>
                                <td>{{ $b['nome'] }}<small>{{ $b['data']->format('d/m/Y, H:i') }}</small></td>
                                <td>{{ $fmt($b['tamanho']) }}</td>
                                <td>
                                    <div class="acoes-icone">
                                        <a href="{{ route('painel.bancoapi.backup.baixar', $b['nome']) }}" class="btn-icone" title="Baixar" aria-label="Baixar {{ $b['nome'] }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('painel.bancoapi.backup.excluir', $b['nome']) }}"
                                              data-confirmar="Excluir o backup {{ $b['nome'] }}? Esta ação não pode ser desfeita.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icone perigo" title="Excluir" aria-label="Excluir {{ $b['nome'] }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <h3 class="titulo-secao">Tabelas</h3>
    <div class="tabela-wrap">
        <table class="tabela tabela-compacta">
            <thead><tr><th>Tabela</th><th>Registros</th><th>Tamanho</th></tr></thead>
            <tbody>
            @foreach ($tabelas as $t)
                <tr>
                    <td>
                        {{ $t['nome'] }}
                        @if ($t['sensivel'])<span class="tag-sens">dados de pacientes</span>@endif
                    </td>
                    <td>{{ $t['linhas'] ?? '—' }}</td>
                    <td>{{ $fmt($t['tamanho']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif