@extends('layouts.painel')

@section('titulo', 'Históricos')

@section('conteudo')
@php
    $cores = config('triagem.cores');
    $sexos = ['M' => 'Masc.', 'F' => 'Fem.', 'O' => 'Outro'];
    $rotulosStatus = ['aguardando' => 'Aguardando', 'em_atendimento' => 'Em atendimento', 'finalizada' => 'Atendido'];
    $periodos = ['hoje' => 'Hoje', '7d' => 'Últimos 7 dias', '30d' => 'Últimos 30 dias', 'tudo' => 'Todo o período', 'personalizado' => 'Personalizado'];
    $filtrosAtivos = $f['periodo'] !== '30d' || $f['cor'] || $f['status'] !== 'todos' || $f['busca'] !== '' || $f['meus'];
    $fmt = fn ($v) => $v === null ? '—' : $v;
@endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Históricos</h1>
        <p class="sub">Consulte atendimentos anteriores, filtre por período e gravidade e abra a ficha completa.</p>
    </div>
</div>

<form method="GET" action="{{ route('painel.historico.index') }}" id="form-filtros" class="cartao filtros-historico">
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

        <div class="campo-filtro">
            <label for="status">Situação</label>
            <select name="status" id="status">
                <option value="todos" @selected($f['status'] === 'todos')>Todas</option>
                <option value="finalizada" @selected($f['status'] === 'finalizada')>Atendidos</option>
                <option value="em_atendimento" @selected($f['status'] === 'em_atendimento')>Em atendimento</option>
                <option value="aguardando" @selected($f['status'] === 'aguardando')>Aguardando</option>
            </select>
        </div>

        <div class="campo-filtro grande">
            <label for="busca">Buscar</label>
            <input type="search" id="busca" name="busca" value="{{ $f['busca'] }}" placeholder="Nome, queixa ou nº da ficha..." autocomplete="off">
        </div>
    </div>

    @if ($errors->any())
        <div class="flash erro">{{ $errors->first() }}</div>
    @endif

    <div class="filtro-gravidade">
        <span class="rotulo">Gravidade:</span>

        <label class="opcao-cor">
            <input type="radio" name="cor" value="" @checked(! $f['cor'])>
            <span class="selo-cor tom-todas"><i></i>Todas <span class="n">{{ $contagemCores->sum() }}</span></span>
        </label>

        @foreach (['laranja', 'amarelo', 'verde', 'azul'] as $c)
            <label class="opcao-cor">
                <input type="radio" name="cor" value="{{ $c }}" @checked($f['cor'] === $c)>
                <span class="selo-cor tom-{{ $c }}"><i></i>{{ $cores[$c]['descricao'] }} <span class="n">{{ $contagemCores[$c] ?? 0 }}</span></span>
            </label>
        @endforeach
    </div>

    <div class="filtros-rodape">
        <label class="check-simples">
            <input type="checkbox" name="meus" value="1" @checked($f['meus'])> Só meus atendimentos
        </label>

        <div class="acoes">
            <button type="submit" class="btn">Filtrar</button>
            @if ($filtrosAtivos)
                <a href="{{ route('painel.historico.index') }}" class="btn claro">Limpar filtros</a>
            @endif
            <a href="{{ route('painel.historico.exportar', request()->query()) }}" class="btn claro">Exportar CSV</a>
        </div>
    </div>
</form>

<div class="kpis">
    <div class="kpi">
        <span>Atendimentos no filtro</span>
        <b>{{ $resumo['total'] }}</b>
        <small>triagens encontradas</small>
    </div>
    <div class="kpi">
        <span>Espera média até ser chamado</span>
        <b>{{ $fmt($resumo['espera']) }}@if ($resumo['espera'] !== null)<small> min</small>@endif</b>
        <small>pacientes já chamados</small>
    </div>
    <div class="kpi">
        <span>Duração média do atendimento</span>
        <b>{{ $fmt($resumo['duracao']) }}@if ($resumo['duracao'] !== null)<small> min</small>@endif</b>
        <small>do chamado à finalização</small>
    </div>
    <div class="kpi {{ $resumo['alertas'] > 0 ? 'ruim' : '' }}">
        <span>Alertas imediatos</span>
        <b>{{ $resumo['alertas'] }}</b>
        <small>no período filtrado</small>
    </div>
</div>

@if ($triagens->isEmpty())
    <p class="vazio">
        Nenhum atendimento encontrado com os filtros atuais.
        @if ($filtrosAtivos)<a href="{{ route('painel.historico.index') }}">Limpar filtros</a>@endif
    </p>
@else
    <p class="resultado-info">
        Mostrando {{ $triagens->firstItem() }} a {{ $triagens->lastItem() }} de {{ $triagens->total() }}
        · página {{ $triagens->currentPage() }} de {{ $triagens->lastPage() }}
    </p>

    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr>
                    <th>Chegada</th><th>Ficha</th><th>Paciente</th><th>Gravidade</th>
                    <th>Queixas</th><th>Situação</th><th>Tempos</th><th>Atendido por</th><th></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($triagens as $t)
                @php $queixas = $t->sintomas->pluck('nome')->implode(', '); @endphp
                <tr>
                    <td>{{ $t->created_at->format('d/m/Y') }}<small>{{ $t->created_at->format('H:i') }}</small></td>
                    <td>#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $t->paciente->nome }}<small>{{ $t->paciente->idade }} anos · {{ $sexos[$t->paciente->sexo] ?? '-' }}</small></td>
                    <td>
                        <span class="selo-cor tom-{{ $t->cor }}"><i></i>{{ $cores[$t->cor]['nome'] }}</span>
                        @if ($t->alerta_imediato)<span title="Alerta imediato">⚠️</span>@endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($queixas, 50) ?: '—' }}</td>
                    <td>
                        <span class="status-tag status-{{ $t->status }}">{{ $rotulosStatus[$t->status] }}</span>
                        @if ($t->comentarios_count > 0)<small>💬 {{ $t->comentarios_count }}</small>@endif
                    </td>
                    <td>
                        @if ($t->espera_ate_chamado !== null)
                            Espera {{ $t->espera_ate_chamado }} min
                        @else
                            —
                        @endif
                        @if ($t->duracao_atendimento !== null)
                            <small>Atendimento {{ $t->duracao_atendimento }} min</small>
                        @endif
                    </td>
                    <td>{{ $t->atendente->name ?? '—' }}</td>
                    <td><a class="btn claro" href="{{ route('painel.fila.mostrar', $t) }}">Ver ficha</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $triagens->links('vendor.pagination.painel') }}
@endif

<script>
    const form = document.getElementById('form-filtros');
    const periodo = document.getElementById('periodo');
    const datas = document.getElementById('datas');

    // Mudou um filtro: aplica na hora (a busca por texto só envia com Enter ou "Filtrar")
    form.addEventListener('change', e => {
        if (e.target.name === 'busca') return;

        if (e.target === periodo) {
            const personalizado = periodo.value === 'personalizado';
            datas.hidden = !personalizado;
            if (personalizado) return; // espera o médico escolher as datas
        }

        form.submit();
    });
</script>
@endsection