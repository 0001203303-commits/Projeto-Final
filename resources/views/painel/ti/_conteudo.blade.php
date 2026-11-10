@php
    $b = $s['banco'];
    $ia = $s['ia'];
    $t = $s['totens'];
    $at = $s['atividade'];
    $sis = $s['sistema'];
    $erros = $s['erros'];

    $bytes = function ($v) {
        if ($v === null) return '—';
        $un = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($v >= 1024 && $i < 4) { $v /= 1024; $i++; }
        return number_format($v, $i ? 1 : 0, ',', '.') . ' ' . $un[$i];
    };

    $classeIa = $ia['taxa'] === null ? '' : ($ia['taxa'] >= 80 ? 'bom' : ($ia['taxa'] >= 50 ? 'atencao' : 'ruim'));
    $classeBanco = ! $b['ok'] ? 'ruim' : (($b['ms'] ?? 0) > 200 ? 'atencao' : 'bom');
    $ultimaTriagem = $at['ultima'] ? \Illuminate\Support\Carbon::parse($at['ultima']) : null;
@endphp

@if (count($s['alertas']))
    <div class="banners">
        @foreach ($s['alertas'] as $a)
            <div class="banner-alerta {{ $a['nivel'] === 'critico' ? 'critico' : 'aviso-amarelo' }}">
                <span>{{ $a['texto'] }}</span>
                @if ($a['link'])<a href="{{ $a['link']['url'] }}">{{ $a['link']['rotulo'] }}</a>@endif
            </div>
        @endforeach
    </div>
@endif

<div class="kpis">
    <div class="kpi {{ $classeBanco }}">
        <span>Banco de dados</span>
        <b>{{ $b['ok'] ? 'Conectado' : 'Sem conexão' }}</b>
        <small>{{ $b['ok'] ? 'resposta em ' . $b['ms'] . ' ms' : 'verifique o MySQL' }}</small>
    </div>
    <div class="kpi {{ $classeIa }}">
        <span>Resumos feitos pela IA (24 h)</span>
        <b>{{ $ia['taxa'] === null ? '—' : $ia['taxa'] . '%' }}</b>
        <small>{{ $ia['com_ia'] }} de {{ $ia['total'] }} triagens</small>
    </div>
    <div class="kpi {{ $t['offline'] > 0 ? 'ruim' : ($t['online'] > 0 ? 'bom' : '') }}">
        <span>Totens online</span>
        <b>{{ $t['online'] }}<small> de {{ $t['ativos'] }}</small></b>
        <small>{{ $t['offline'] }} offline · {{ $t['nunca'] }} nunca conectaram</small>
    </div>
    <div class="kpi">
        <span>Triagens nas últimas 24 h</span>
        <b>{{ $at['triagens_24h'] }}</b>
        <small>{{ $ultimaTriagem ? 'última ' . $ultimaTriagem->diffForHumans() : 'nenhuma ainda' }}</small>
    </div>
</div>

<div class="graficos">

    {{-- Totens --}}
    <div class="cartao">
        <h3>Totens</h3>

        @if ($t['total'] === 0)
            <p class="sem-dados">Nenhum totem cadastrado.</p>
        @else
            <div class="status-linha"><span><i class="bolinha ok"></i>Online</span><strong>{{ $t['online'] }}</strong></div>
            <div class="status-linha"><span><i class="bolinha falha"></i>Offline</span><strong>{{ $t['offline'] }}</strong></div>
            <div class="status-linha"><span>Nunca conectaram</span><strong>{{ $t['nunca'] }}</strong></div>
            <div class="status-linha"><span>Desativados</span><strong>{{ $t['inativos'] }}</strong></div>

            @if ($t['offline_lista']->isNotEmpty())
                <h3 style="margin-top:16px">Sem contato recente</h3>
                @foreach ($t['offline_lista'] as $totem)
                    <div class="item-log">
                        {{ $totem->nome }}
                        <small>{{ $totem->local ?: 'Local não informado' }} · último contato {{ $totem->ultimo_contato_em?->diffForHumans() ?? '—' }}</small>
                    </div>
                @endforeach
            @endif
        @endif

        <a class="mini-link" href="{{ route('painel.totens.index') }}">Gerenciar totens →</a>
    </div>

    {{-- IA --}}
    <div class="cartao">
        <h3>IA (Gemini)</h3>

        <div class="status-linha">
            <span><i class="bolinha {{ $ia['configurada'] ? 'ok' : 'falha' }}"></i>Configuração</span>
            <strong>{{ $ia['configurada'] ? 'Chave definida' : 'Chave não configurada' }}</strong>
        </div>
        <div class="status-linha"><span>Modelo</span><strong>{{ $ia['modelo'] ?: '—' }}</strong></div>
        <div class="status-linha">
            <span>Última geração com sucesso</span>
            <strong>{{ $ia['ultima'] ? $ia['ultima']->diffForHumans() : '—' }}</strong>
        </div>
        <div class="status-linha"><span>Resumos com IA (24 h)</span><strong>{{ $ia['com_ia'] }}</strong></div>
        <div class="status-linha"><span>Resumos só locais (24 h)</span><strong>{{ $ia['locais'] }}</strong></div>

        @if ($ia['taxa'] !== null)
            <div class="trilho" style="margin-top:12px">
                <div class="preenche {{ $ia['taxa'] < 50 ? 'estourou' : '' }}" style="width: {{ $ia['taxa'] }}%"></div>
            </div>
        @endif

        <p class="aviso" style="margin-top:10px">
            Resumo só local significa que a IA falhou ou não rodou, e o médico recebeu o resumo automático local.
            Triagens dos últimos 2 minutos não entram na conta.
        </p>
    </div>

    {{-- Erros --}}
    <div class="cartao">
        <h3>Erros recentes (24 h)</h3>

        @forelse ($erros['itens'] as $e)
            <div class="item-log">
                <span class="nivel {{ $e['nivel'] }}">{{ $e['nivel'] }}</span>{{ $e['texto'] }}
                <small>{{ $e['quando']->format('d/m/Y H:i:s') }} · {{ $e['quando']->diffForHumans() }}</small>
            </div>
        @empty
            <p class="sem-dados">Nenhum erro ou aviso registrado nas últimas 24 horas. 👍</p>
        @endforelse

        @if ($erros['total_24h'] > 0)
            <p class="aviso" style="margin-top:10px">
                {{ $erros['total_24h'] }} registro(s) no período, {{ $erros['gemini_24h'] }} do Gemini.
                Para proteger dados de pacientes, só os avisos do Gemini mostram o texto.
            </p>
        @endif
    </div>

    {{-- Servidor --}}
    <div class="cartao">
        <h3>Servidor</h3>

        <div class="status-linha"><span>Laravel / PHP</span><strong>{{ $sis['laravel'] }} / {{ $sis['php'] }}</strong></div>
        <div class="status-linha"><span>Banco (versão)</span><strong>{{ $b['versao'] ?? '—' }}</strong></div>
        <div class="status-linha">
            <span>Ambiente</span>
            <strong>{{ $sis['ambiente'] }}@if ($sis['debug']) · debug ligado @endif</strong>
        </div>
        <div class="status-linha"><span>Hora do servidor</span><strong>{{ $sis['hora']->format('d/m/Y H:i:s') }} ({{ $sis['timezone'] }})</strong></div>
        <div class="status-linha"><span>Disco livre</span><strong>{{ $bytes($sis['disco_livre']) }} de {{ $bytes($sis['disco_total']) }}</strong></div>
        <div class="status-linha"><span>Arquivo de log</span><strong>{{ $bytes($sis['log_bytes']) }}</strong></div>
        <div class="status-linha"><span>Cache / sessão</span><strong>{{ $sis['cache'] }} / {{ $sis['sessao'] }}</strong></div>
    </div>
</div>