@php $cores = config('triagem.cores'); @endphp

<div class="resumo-fila">
    @foreach (['laranja', 'amarelo', 'verde', 'azul'] as $c)
        <div class="contador cor-{{ $c }}">
            <b>{{ $aguardando->where('cor', $c)->count() }}</b>
            <span>{{ $cores[$c]['nome'] }}</span>
        </div>
    @endforeach
</div>

<h3 class="titulo-secao">Aguardando ({{ $aguardando->count() }})</h3>

@if ($aguardando->isEmpty())
    <p class="vazio">Nenhum paciente aguardando.</p>
@else
    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr><th>Prioridade</th><th>Ficha</th><th>Paciente</th><th>Queixa</th><th>Espera</th><th></th></tr>
            </thead>
            <tbody>
            @foreach ($aguardando as $t)
                @php
                    $limite = $cores[$t->cor]['limite_min'] ?? null;
                    $atrasado = $limite && $t->espera_minutos > $limite;
                @endphp
                <tr class="{{ $t->alerta_imediato ? 'linha-alerta' : '' }}">
                    <td>
                        <span class="cor cor-{{ $t->cor }}">{{ $cores[$t->cor]['nome'] }}</span>
                        @if ($t->alerta_imediato)<span title="Alerta imediato">⚠️</span>@endif
                    </td>
                    <td>#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $t->paciente->nome }}<small>{{ $t->paciente->idade }} anos</small></td>
                    <td>{{ $t->sintomas->pluck('nome')->implode(', ') }}</td>
                    <td class="{{ $atrasado ? 'atrasado' : '' }}">
                        {{ $t->espera_minutos }} min
                        @if ($atrasado)<small>acima do limite</small>@endif
                    </td>
                    <td><a class="btn" href="{{ route('painel.fila.mostrar', $t) }}">Abrir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<h3 class="titulo-secao">Em atendimento ({{ $emAtendimento->count() }})</h3>

@if ($emAtendimento->isEmpty())
    <p class="vazio">Nenhum atendimento em andamento.</p>
@else
    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr><th>Prioridade</th><th>Ficha</th><th>Paciente</th><th>Atendido por</th><th></th></tr>
            </thead>
            <tbody>
            @foreach ($emAtendimento as $t)
                <tr>
                    <td><span class="cor cor-{{ $t->cor }}">{{ $cores[$t->cor]['nome'] }}</span></td>
                    <td>#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $t->paciente->nome }}<small>{{ $t->paciente->idade }} anos</small></td>
                    <td>{{ $t->atendente->name ?? '-' }}</td>
                    <td><a class="btn claro" href="{{ route('painel.fila.mostrar', $t) }}">Abrir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif