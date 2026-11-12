@php
    $cores = config('triagem.cores');
    $hex = ['laranja' => '#F28C28', 'amarelo' => '#E5B800', 'verde' => '#2E9E55', 'azul' => '#2F80ED'];
    $textoPeriodo = ['hoje' => 'hoje', '7d' => 'nos últimos 7 dias', '30d' => 'nos últimos 30 dias'][$periodo];
    $ordem = ['laranja', 'amarelo', 'verde', 'azul'];
    $fmt = fn ($v) => $v === null ? '—' : $v;
    $classeMeta = $e['meta_pct'] === null ? '' : ($e['meta_pct'] >= 90 ? 'bom' : ($e['meta_pct'] >= 70 ? 'atencao' : 'ruim'));
@endphp 

{{-- Faixa de aviso --}}
@if ($e['alertas'] > 0)
    <div class="banner-alerta critico">
        <span>{{ $e['alertas'] }} {{ $e['alertas'] === 1 ? 'paciente com alerta imediato aguardando' : 'pacientes com alerta imediato aguardando' }}.</span>
        @if (auth()->user()->perfil === 'medico')
            <a href="{{ route('painel.fila.index') }}">Ir para a fila →</a>
        @endif
    </div>
@elseif ($e['acima_limite'] > 0)
    <div class="banner-alerta aviso-amarelo">
        <span>{{ $e['acima_limite'] }} {{ $e['acima_limite'] === 1 ? 'paciente está' : 'pacientes estão' }} acima do tempo de espera recomendado.</span>
        @if (auth()->user()->perfil === 'medico')
            <a href="{{ route('painel.fila.index') }}">Ir para a fila →</a>
        @endif
    </div>
@endif

{{-- Agora --}}
<h3 class="titulo-secao" style="margin-top:0">Agora</h3>
<div class="kpis">
    <div class="kpi {{ $e['acima_limite'] > 0 ? 'atencao' : '' }}">
        <span>Na fila</span>
        <b>{{ $e['na_fila'] }}</b>
        <small>{{ $e['acima_limite'] }} acima do tempo recomendado</small>
    </div>
    <div class="kpi {{ $e['acima_limite'] > 0 ? 'ruim' : '' }}">
        <span>Maior espera</span>
        <b>{{ $e['maior_espera'] }}<small> min</small></b>
        <small>entre os que aguardam</small>
    </div>
    <div class="kpi">
        <span>Em atendimento</span>
        <b>{{ $e['em_atendimento'] }}</b>
        <small>{{ $e['meus_em_atendimento'] }} {{ $e['meus_em_atendimento'] === 1 ? 'é seu' : 'são seus' }}</small>
    </div>
    <div class="kpi {{ $e['alertas'] > 0 ? 'ruim' : '' }}">
        <span>Alertas imediatos</span>
        <b>{{ $e['alertas'] }}</b>
        <small>aguardando atendimento</small>
    </div>
</div>

{{-- Resumo do período --}}
<h3 class="titulo-secao">Resumo {{ $textoPeriodo }}</h3>
<div class="kpis">
    <div class="kpi">
        <span>Triagens realizadas</span>
        <b>{{ $e['total'] }}</b>
        <small>{{ $e['finalizadas'] }} atendimentos finalizados</small>
    </div>
    <div class="kpi">
        <span>Espera média até ser chamado</span>
        <b>{{ $fmt($e['espera_media']) }}@if ($e['espera_media'] !== null)<small> min</small>@endif</b>
        <small>pacientes já chamados</small>
    </div>
    <div class="kpi {{ $classeMeta }}">
        <span>Meta de tempo cumprida</span>
        <b>{{ $e['meta_pct'] === null ? '—' : $e['meta_pct'] . '%' }}</b>
        <small>chamados dentro do limite da cor</small>
    </div>
    <div class="kpi">
        <span>Duração média do atendimento</span>
        <b>{{ $fmt($e['duracao_media']) }}@if ($e['duracao_media'] !== null)<small> min</small>@endif</b>
        <small>do chamado à finalização</small>
    </div>
    <div class="kpi">
        <span>Meus atendimentos finalizados</span>
        <b>{{ $e['meus_finalizados'] }}</b>
        <small>{{ $textoPeriodo }}</small>
    </div>
</div>

<h3 class="titulo-secao">Análises</h3>
<div class="graficos">

    {{-- Gravidade --}}
    <div class="cartao">
        <h3>Gravidade das triagens {{ $textoPeriodo }}</h3>
        @php
            $total = array_sum($e['por_cor']);
            $acum = 0;
            $partes = [];
            foreach ($ordem as $c) {
                $p = $total ? $e['por_cor'][$c] / $total * 100 : 0;
                if ($p > 0) {
                    $partes[] = $hex[$c] . ' ' . round($acum, 2) . '% ' . round($acum + $p, 2) . '%';
                }
                $acum += $p;
            }
            $gradiente = $total ? implode(', ', $partes) : '#e3eded 0% 100%';
        @endphp

        <div class="rosca-wrap">
            <div class="rosca" style="background: conic-gradient({{ $gradiente }})" role="img" aria-label="Distribuição por gravidade">
                <div class="rosca-centro"><b>{{ $total }}</b><span>triagens</span></div>
            </div>

            <ul class="legenda">
                @foreach ($ordem as $c)
                    <li>
                        <i style="background: {{ $hex[$c] }}"></i>
                        <span>{{ $cores[$c]['nome'] }}: <strong>{{ $e['por_cor'][$c] }}</strong>
                        @if ($total) ({{ round($e['por_cor'][$c] / $total * 100) }}%) @endif</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Chegadas --}}
    <div class="cartao">
        <h3>{{ $e['chegadas']['titulo'] }}</h3>
        @php
            $serie = $e['chegadas']['serie'];
            $n = count($serie);
            $max = max(1, collect($serie)->max('total'));
            $slot = 480 / $n;
            $largura = round($slot * 0.7, 1);
            $passo = $n <= 12 ? 1 : (int) ceil($n / 10);
        @endphp

        @if (collect($serie)->sum('total') === 0)
            <p class="sem-dados">Nenhuma triagem neste período.</p>
        @else
            <svg viewBox="0 0 480 150" class="svg-barras" role="img" aria-label="Chegadas">
                @foreach ($serie as $i => $h)
                    @php
                        $alt = (int) round($h['total'] / $max * 100);
                        $x = round($i * $slot + ($slot - $largura) / 2, 1);
                        $cx = round($x + $largura / 2, 1);
                    @endphp
                    <rect x="{{ $x }}" y="{{ 120 - $alt }}" width="{{ $largura }}" height="{{ $alt }}" rx="3"
                          class="barra-svg {{ $i === $n - 1 ? 'atual' : '' }}"/>
                    @if ($h['total'] > 0 && $n <= 12)
                        <text x="{{ $cx }}" y="{{ 115 - $alt }}" text-anchor="middle" class="valor-svg">{{ $h['total'] }}</text>
                    @endif
                    @if ($i % $passo === 0)
                        <text x="{{ $cx }}" y="138" text-anchor="middle" class="rotulo-svg">{{ $h['rotulo'] }}</text>
                    @endif
                @endforeach
                <line x1="0" y1="120" x2="480" y2="120" class="eixo-svg"/>
            </svg>
        @endif
    </div>

    {{-- Espera por cor --}}
    <div class="cartao">
        <h3>Espera média até ser chamado, por cor</h3>
        @foreach ($ordem as $c)
            @php $d = $e['espera_por_cor'][$c]; @endphp
            <div class="linha-barra">
                <div class="linha-topo">
                    <span class="cor cor-{{ $c }}">{{ $cores[$c]['nome'] }}</span>
                    <span>
                        @if ($d['media'] === null)
                            <span class="sem-dados">nenhum chamado</span>
                        @else
                            <strong>{{ $d['media'] }} min</strong>
                            @if ($d['limite'])<small> / limite {{ $d['limite'] }} min</small>@endif
                        @endif
                    </span>
                </div>
                @if ($d['media'] !== null && $d['limite'])
                    <div class="trilho">
                        <div class="preenche {{ $d['media'] > $d['limite'] ? 'estourou' : '' }}"
                             style="width: {{ min(100, round($d['media'] / $d['limite'] * 100)) }}%"></div>
                    </div>
                @endif
            </div>
        @endforeach
        <p class="aviso">A barra mostra quanto do tempo limite da cor foi usado em média. Vermelha: média acima do limite.</p>
    </div>

    {{-- Queixas --}}
    <div class="cartao">
        <h3>Queixas mais frequentes</h3>
        @php $maxQueixa = max(1, $e['queixas']->max('total') ?? 1); @endphp

        @forelse ($e['queixas'] as $q)
            <div class="linha-barra">
                <div class="linha-topo"><span>{{ $q->nome }}</span><strong>{{ $q->total }}</strong></div>
                <div class="trilho"><div class="preenche" style="width: {{ round($q->total / $maxQueixa * 100) }}%"></div></div>
            </div>
        @empty
            <p class="sem-dados">Sem triagens neste período.</p>
        @endforelse
    </div>

    {{-- Faixa etária --}}
    <div class="cartao">
        <h3>Faixa etária dos pacientes</h3>
        @php $maxFaixa = max(1, max($e['faixas'])); @endphp

        @if (array_sum($e['faixas']) === 0)
            <p class="sem-dados">Sem triagens neste período.</p>
        @else
            @foreach ($e['faixas'] as $rotulo => $qtd)
                <div class="linha-barra">
                    <div class="linha-topo"><span>{{ $rotulo }}</span><strong>{{ $qtd }}</strong></div>
                    <div class="trilho"><div class="preenche" style="width: {{ round($qtd / $maxFaixa * 100) }}%"></div></div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Precisam de atenção --}}
    <div class="cartao">
        <h3>Precisam de atenção agora</h3>

        @forelse ($e['precisam_atencao'] as $t)
            @php $limite = $cores[$t->cor]['limite_min']; @endphp
            <div class="item-atencao">
                <span class="cor cor-{{ $t->cor }}">{{ $cores[$t->cor]['nome'] }}</span>
                <div class="info">
                    {{ $t->paciente->nome }}
                    <small class="{{ $t->alerta_imediato ? 'motivo-alerta' : '' }}">
                        @if ($t->alerta_imediato)
                            Alerta imediato
                        @else
                            {{ $t->espera_minutos }} min de espera (limite {{ $limite }} min)
                        @endif
                    </small>
                </div>
                @if (auth()->user()->perfil === 'medico')
                    <a class="btn claro" href="{{ route('painel.fila.mostrar', $t) }}">Abrir</a>
                @endif
            </div>
        @empty
            <p class="sem-dados">Nenhum paciente exigindo atenção especial. 👍</p>
        @endforelse
    </div>
</div>