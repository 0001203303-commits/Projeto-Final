@extends('layouts.painel')

@section('titulo', 'Relatórios')

@section('conteudo')
@php
    $cores = config('triagem.cores');
    $ordem = ['laranja', 'amarelo', 'verde', 'azul'];
    $periodos = ['7d' => 'Últimos 7 dias', '30d' => 'Últimos 30 dias', '90d' => 'Últimos 90 dias', 'tudo' => 'Todo o período', 'personalizado' => 'Personalizado'];
    $fmt = fn ($v, $suf = '') => $v === null ? '—' : $v . $suf;
    $classeMeta = $r['meta_pct'] === null ? '' : ($r['meta_pct'] >= 90 ? 'bom' : ($r['meta_pct'] >= 70 ? 'atencao' : 'ruim'));
    $taxaAlerta = $r['total'] > 0 ? (int) round($r['alertas'] / $r['total'] * 100) : null;
    $maxQueixa = max(1, $r['queixas']->max('total') ?? 1);
    $maxCor = max(1, $r['por_cor']->max() ?? 1);
    $volumeDesc = array_reverse($r['volume']);
@endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Relatórios</h1>
        <p class="sub">Números agregados por período. Dados de pacientes não aparecem neste perfil.</p>
    </div>
</div>

<form method="GET" action="{{ route('painel.relatorios.index') }}" id="form-filtros" class="cartao filtros-historico">
    <div class="filtros-linha">
        <div class="campo-filtro">
            <label for="periodo">Período</label>
            <select name="periodo" id="periodo">
                @foreach ($periodos as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected($f['periodo'] === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
        </div>

        <div class="datas-par" id="datas" @if ($f['periodo'] !== 'personalizado') hidden @endif>
            <div class="campo-filtro">
                <label for="de">De</label>
                <input type="date" id="de" name="de" value="{{ $f['de'] }}" max="{{ now()->toDateString() }}">
            </div>
            <div class="campo-filtro">
                <label for="ate">Até</label>
                <input type="date" id="ate" name="ate" value="{{ $f['ate'] }}" max="{{ now()->toDateString() }}">
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="flash erro">{{ $errors->first() }}</div>
    @endif

    <div class="filtros-rodape">
        <span class="periodo-info" style="margin:0">
            {{ $r['de']->format('d/m/Y') }} a {{ $r['ate']->format('d/m/Y') }}
        </span>

        <div class="acoes">
            <button type="submit" class="btn">Filtrar</button>
            <a href="{{ route('painel.relatorios.exportar', array_merge(['tipo' => 'volume'], request()->query())) }}" class="btn claro">Exportar volume (CSV)</a>
            <a href="{{ route('painel.relatorios.exportar', array_merge(['tipo' => 'espera'], request()->query())) }}" class="btn claro">Exportar espera (CSV)</a>
        </div>
    </div>
</form>

<div class="kpis">
    <div class="kpi">
        <span>Triagens no período</span>
        <b>{{ $r['total'] }}</b>
        <small>{{ $r['de']->format('d/m') }} a {{ $r['ate']->format('d/m') }}</small>
    </div>
    <div class="kpi">
        <span>Espera média até ser chamado</span>
        <b>{{ $fmt($r['espera_media']) }}@if ($r['espera_media'] !== null)<small> min</small>@endif</b>
        <small>pacientes já chamados</small>
    </div>
    <div class="kpi {{ $classeMeta }}">
        <span>Meta de tempo cumprida</span>
        <b>{{ $fmt($r['meta_pct'], '%') }}</b>
        <small>chamados dentro do limite da cor</small>
    </div>
    <div class="kpi {{ $taxaAlerta !== null && $taxaAlerta >= 20 ? 'atencao' : '' }}">
        <span>Alertas imediatos</span>
        <b>{{ $r['alertas'] }}</b>
        <small>{{ $taxaAlerta === null ? 'sem triagens' : $taxaAlerta . '% das triagens' }}</small>
    </div>
</div>

<div class="graficos">

    <div class="cartao">
        <h3>Volume {{ $r['por_mes'] ? 'por mês' : 'por dia' }}</h3>
        @include('painel.admin._barras', ['serie' => $r['serie'], 'destacarMax' => true, 'descricao' => 'Triagens no período'])
        <p class="aviso">A barra em destaque é {{ $r['por_mes'] ? 'o mês' : 'o dia' }} mais movimentado.</p>
    </div>

    <div class="cartao">
        <h3>Chegadas por dia da semana</h3>
        @include('painel.admin._barras', ['serie' => $r['semana'], 'destacarMax' => true, 'descricao' => 'Chegadas por dia da semana'])
        <p class="aviso">Útil para dimensionar a escala da equipe.</p>
    </div>

    <div class="cartao">
        <h3>Gravidade</h3>
        @foreach ($ordem as $c)
            @php $qtd = $r['por_cor'][$c] ?? 0; @endphp
            <div class="linha-barra">
                <div class="linha-topo">
                    <span class="cor cor-{{ $c }}">{{ $cores[$c]['nome'] }}</span>
                    <span><strong>{{ $qtd }}</strong>@if ($r['total']) <small>({{ round($qtd / $r['total'] * 100) }}%)</small>@endif</span>
                </div>
                <div class="trilho"><div class="preenche" style="width: {{ round($qtd / $maxCor * 100) }}%"></div></div>
            </div>
        @endforeach
    </div>

    <div class="cartao">
        <h3>Queixas mais frequentes</h3>
        @forelse ($r['queixas'] as $q)
            <div class="linha-barra">
                <div class="linha-topo"><span>{{ $q->nome }}</span><strong>{{ $q->total }}</strong></div>
                <div class="trilho"><div class="preenche" style="width: {{ round($q->total / $maxQueixa * 100) }}%"></div></div>
            </div>
        @empty
            <p class="sem-dados">Sem triagens neste período.</p>
        @endforelse
    </div>
</div>

<h3 class="titulo-secao">Espera e atendimento por cor</h3>
<div class="tabela-wrap">
    <table class="tabela">
        <thead>
            <tr>
                <th>Cor</th><th class="num">Chamados</th><th class="num">Espera média</th><th class="num">Espera máxima</th>
                <th class="num">Limite</th><th class="num">Dentro do limite</th><th class="num">Duração média</th><th class="num">Ainda aguardando</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($ordem as $c)
            @php $e = $r['espera'][$c]; @endphp
            <tr>
                <td><span class="cor cor-{{ $c }}">{{ $cores[$c]['nome'] }}</span></td>
                <td class="num">{{ $e['n'] }}</td>
                <td class="num">{{ $fmt($e['media'], ' min') }}</td>
                <td class="num">{{ $fmt($e['maximo'], ' min') }}</td>
                <td class="num">{{ $fmt($e['limite'], ' min') }}</td>
                <td class="num">{{ $fmt($e['dentro_pct'], '%') }}</td>
                <td class="num">{{ $fmt($e['duracao'], ' min') }}</td>
                <td class="num">{{ $e['aguardando'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<p class="aviso">Espera é o tempo entre a chegada e o chamado. Duração é o tempo entre o chamado e a finalização. Cores sem limite definido não entram na meta.</p>

<h3 class="titulo-secao">Volume {{ $r['por_mes'] ? 'mensal' : 'diário' }} por gravidade</h3>
<div class="tabela-wrap rolagem">
    <table class="tabela compacta">
        <thead>
            <tr><th>{{ $r['por_mes'] ? 'Mês' : 'Data' }}</th><th class="num">Laranja</th><th class="num">Amarelo</th><th class="num">Verde</th><th class="num">Azul</th><th class="num">Total</th></tr>
        </thead>
        <tbody>
        @foreach ($volumeDesc as $linha)
            <tr>
                <td>{{ $linha['rotulo'] }}</td>
                @foreach ($ordem as $c)<td class="num">{{ $linha['cores'][$c] }}</td>@endforeach
                <td class="num"><strong>{{ $linha['total'] }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    const form = document.getElementById('form-filtros');
    const periodo = document.getElementById('periodo');
    const datas = document.getElementById('datas');

    form.addEventListener('change', () => {
        const personalizado = periodo.value === 'personalizado';
        datas.hidden = !personalizado;
        if (personalizado && !(document.getElementById('de').value || document.getElementById('ate').value)) return;
        form.submit();
    });
</script>
@endsection