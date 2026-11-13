@php
    $cores = config('triagem.cores');
    $hex = ['laranja' => '#F28C28', 'amarelo' => '#E5B800', 'verde' => '#2E9E55', 'azul' => '#2F80ED'];
    $ordem = ['laranja', 'amarelo', 'verde', 'azul'];
    $textoPeriodo = ['hoje' => 'hoje', '7d' => 'nos últimos 7 dias', '30d' => 'nos últimos 30 dias'][$periodo];
    $anterior = $periodo === 'hoje' ? 'ontem' : 'período anterior';
    $fmt = fn ($v) => $v === null ? '—' : $v;

    $classeMeta = $e['meta_pct'] === null ? '' : ($e['meta_pct'] >= 90 ? 'bom' : ($e['meta_pct'] >= 70 ? 'atencao' : 'ruim'));
    $classeIa = $e['ia_taxa'] === null ? '' : ($e['ia_taxa'] >= 80 ? 'bom' : ($e['ia_taxa'] >= 50 ? 'atencao' : 'ruim'));

    $delta = $e['total_anterior'] > 0
        ? (int) round(($e['total'] - $e['total_anterior']) / $e['total_anterior'] * 100)
        : null;

    $taxaAlerta = $e['total'] > 0 ? (int) round($e['alertas_periodo'] / $e['total'] * 100) : null;
    $perfis = ['medico' => 'Médicos', 'ti' => 'Técnicos de TI', 'admin' => 'Administradores'];
@endphp

{{-- Faixa de aviso (só contagens, sem nomes) --}}
@if ($e['alertas_agora'] > 0)
    <div class="banner-alerta critico">
        <span>{{ $e['alertas_agora'] }} {{ $e['alertas_agora'] === 1 ? 'paciente com alerta imediato aguardando' : 'pacientes com alerta imediato aguardando' }} atendimento.</span>
    </div>
@elseif ($e['acima_limite'] > 0)
    <div class="banner-alerta aviso-amarelo">
        <span>{{ $e['acima_limite'] }} {{ $e['acima_limite'] === 1 ? 'paciente está' : 'pacientes estão' }} acima do tempo de espera recomendado.</span>
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
        <small>neste momento</small>
    </div>
    <div class="kpi {{ $e['alertas_agora'] > 0 ? 'ruim' : '' }}">
        <span>Alertas imediatos</span>
        <b>{{ $e['alertas_agora'] }}</b>
        <small>aguardando atendimento</small>
    </div>
</div>

{{-- Período --}}
<h3 class="titulo-secao">Resumo {{ $textoPeriodo }}</h3>
<div class="kpis">
    <div class="kpi">
        <span>Triagens realizadas</span>
        <b>{{ $e['total'] }}</b>
        <small>
            {{ $e['finalizadas'] }} finalizadas ·
            @if ($delta === null)
                <span class="delta">sem {{ $anterior }} para comparar</span>
            @else
                <span class="delta">{{ $delta > 0 ? '↑' : ($delta < 0 ? '↓' : '=') }} {{ abs($delta) }}% vs. {{ $anterior }}</span>
            @endif
        </small>
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
    <div class="kpi {{ $taxaAlerta !== null && $taxaAlerta >= 20 ? 'atencao' : '' }}">
        <span>Taxa de alerta imediato</span>
        <b>{{ $taxaAlerta === null ? '—' : $taxaAlerta . '%' }}</b>
        <small>{{ $e['alertas_periodo'] }} de {{ $e['total'] }} triagens</small>
    </div>
    <div class="kpi {{ $classeIa }}">
        <span>Resumos feitos pela IA</span>
        <b>{{ $e['ia_taxa'] === null ? '—' : $e['ia_taxa'] . '%' }}</b>
        <small>os demais usaram o resumo local</small>
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
        @include('painel.admin._barras', ['serie' => $e['chegadas']['serie'], 'descricao' => 'Chegadas'])
    </div>

    {{-- Horário de pico --}}
    <div class="cartao">
        <h3>Horário de pico {{ $textoPeriodo }}</h3>
        @include('painel.admin._barras', ['serie' => $e['picos'], 'destacarMax' => true, 'descricao' => 'Chegadas por hora do dia'])
        <p class="aviso">Soma das chegadas de cada hora do dia no período. A barra em destaque é o horário mais movimentado.</p>
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

    {{-- Médicos --}}
    <div class="cartao">
        <h3>Atendimentos finalizados por médico</h3>
        @php $maxMedico = max(1, $e['medicos']->max('total') ?? 1); @endphp

        @forelse ($e['medicos'] as $m)
            <div class="linha-barra">
                <div class="linha-topo">
                    <span>{{ $m['nome'] }}@if ($m['duracao'] !== null)<span class="detalhe">média {{ $m['duracao'] }} min</span>@endif</span>
                    <strong>{{ $m['total'] }}</strong>
                </div>
                <div class="trilho"><div class="preenche" style="width: {{ round($m['total'] / $maxMedico * 100) }}%"></div></div>
            </div>
        @empty
            <p class="sem-dados">Nenhum atendimento finalizado neste período.</p>
        @endforelse
    </div>

    {{-- Equipe e infraestrutura --}}
    <div class="cartao">
        <h3>Equipe e infraestrutura</h3>

        @foreach ($perfis as $chave => $rotulo)
            <div class="status-linha"><span>{{ $rotulo }} ativos</span><strong>{{ $e['equipe'][$chave] ?? 0 }}</strong></div>
        @endforeach

        <div class="status-linha">
            <span><i class="bolinha {{ $e['totens_ativos'] > 0 && $e['totens_online'] === $e['totens_ativos'] ? 'ok' : 'falha' }}"></i>Totens online</span>
            <strong>{{ $e['totens_online'] }} de {{ $e['totens_ativos'] }}</strong>
        </div>

        <div class="status-linha">
            <span>Resumos feitos pela IA</span>
            <strong>{{ $e['ia_taxa'] === null ? '—' : $e['ia_taxa'] . '%' }}</strong>
        </div>

        <p class="aviso" style="margin-top:10px">Detalhes técnicos ficam com o perfil de TI.</p>
    </div>
</div>