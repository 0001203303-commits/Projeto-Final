@extends('layouts.painel')

@section('titulo', 'Ficha ' . str_pad($triagem->id, 4, '0', STR_PAD_LEFT))

@section('conteudo')
@php
    $r = $triagem->resumo_ia;
    $cores = config('triagem.cores');
    $p = $triagem->paciente;
    $sexo = ['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro'][$p->sexo] ?? '-';
    $respostasPorSintoma = $triagem->respostas->groupBy(fn ($x) => $x->pergunta->sintoma_id);
@endphp

<div class="ficha-topo">
    <div>
        <h1 class="titulo">
            Ficha #{{ str_pad($triagem->id, 4, '0', STR_PAD_LEFT) }}
            <span class="cor cor-{{ $triagem->cor }}">{{ $cores[$triagem->cor]['nome'] }}</span>
        </h1>
        <p class="sub">
            {{ $cores[$triagem->cor]['descricao'] }} · {{ $cores[$triagem->cor]['tempo'] }}
            · Espera: {{ $triagem->espera_minutos }} min
        </p>
    </div>

    <div>
        <a href="{{ route('painel.fila.index') }}" class="btn claro">Voltar à fila</a>

        @if ($triagem->status === 'aguardando')
            <form method="POST" action="{{ route('painel.fila.chamar', $triagem) }}" style="display:inline">
                @csrf
                <button type="submit" class="btn destaque">Chamar paciente</button>
            </form>
        @elseif ($triagem->status === 'em_atendimento' && $triagem->atendido_por === auth()->id())
            <form method="POST" action="{{ route('painel.fila.finalizar', $triagem) }}" style="display:inline"
                  onsubmit="return confirm('Finalizar este atendimento?')">
                @csrf
                <button type="submit" class="btn">Finalizar atendimento</button>
            </form>
        @endif
    </div>
</div>

@if ($triagem->status === 'em_atendimento' && $triagem->atendido_por !== auth()->id())
    <div class="flash erro">Em atendimento por {{ $triagem->atendente->name ?? 'outro profissional' }}.</div>
@elseif ($triagem->status === 'finalizada')
    <div class="flash ok">Atendimento finalizado.</div>
@endif

@if ($triagem->alerta_imediato)
    <div class="alerta-imediato">Alerta imediato: valor crítico nos sinais vitais. Avaliar este paciente agora.</div>
@endif

<div class="ficha-grade">
    <div>
        <div class="cartao">
            <h3>Resumo para o médico</h3>
            @if ($r)
                <div class="ia-caixa">{{ $r['resumo'] }}</div>
                <p class="origem">
                    @if (($r['origem'] ?? '') === 'gemini')
                        Gerado por IA ({{ $r['modelo'] }}).
                    @else
                        Resumo automático local (a versão com IA pode estar sendo gerada ou indisponível).
                    @endif
                </p>

                @if (! empty($r['hipoteses']))
                    <h3 style="margin-top:16px">Hipóteses a investigar</h3>
                    <ul>@foreach ($r['hipoteses'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                @endif

                @if (! empty($r['inconsistencias']))
                    <h3 style="margin-top:16px">Inconsistências entre relato e sinais vitais</h3>
                    <ul>@foreach ($r['inconsistencias'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                @endif

                @if (! empty($r['pontos_atencao']))
                    <h3 style="margin-top:16px">Pontos de atenção</h3>
                    <ul>@foreach ($r['pontos_atencao'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                @endif
            @else
                <p class="aviso">Resumo ainda não gerado.</p>
            @endif
        </div>

        <div class="cartao">
            <h3>Por que esta cor?</h3>
            <ul>@foreach ($triagem->motivos ?? [] as $motivo)<li>{{ $motivo }}</li>@endforeach</ul>
        </div>

        <div class="cartao">
            <h3>Respostas do paciente</h3>
            @foreach ($triagem->sintomas as $sintoma)
                <p style="margin:12px 0 4px"><strong>{{ $sintoma->nome }}</strong> <span class="aviso">({{ $sintoma->categoria->nome }})</span></p>
                <ul>
                    @forelse ($respostasPorSintoma[$sintoma->id] ?? [] as $resp)
                        <li>{{ $resp->pergunta->texto }} <strong>{{ $resp->rotulo }}</strong></li>
                    @empty
                        <li class="aviso">Sem perguntas adicionais.</li>
                    @endforelse
                </ul>
            @endforeach
        </div>

        <p class="aviso">
            Apoio à decisão gerado automaticamente a partir de uma pré-triagem. Não constitui diagnóstico.
            A avaliação e a conduta são sempre do profissional de saúde.
        </p>
    </div>

    <div>
        <div class="cartao">
            <h3>Sinais vitais</h3>
            <div class="vitais">
                <div><b>{{ $triagem->bpm }}</b><span>bpm</span></div>
                <div><b>{{ $triagem->spo2 }}%</b><span>SpO₂</span></div>
                <div><b>{{ $triagem->pa_sistolica }}/{{ $triagem->pa_diastolica }}</b><span>mmHg</span></div>
                <div><b>{{ number_format((float) $triagem->temperatura, 1, ',', '.') }}</b><span>°C</span></div>
            </div>
        </div>

        <div class="cartao">
            <h3>Paciente</h3>
            <dl class="dados">
                <dt>Nome</dt><dd>{{ $p->nome }}</dd>
                <dt>Idade / sexo</dt><dd>{{ $p->idade }} anos · {{ $sexo }}@if ($p->gestante) · gestante @endif</dd>
                <dt>CPF</dt><dd>{{ $p->cpf_formatado }}</dd>
                @if ($p->telefone)<dt>Telefone</dt><dd>{{ $p->telefone }}</dd>@endif
            </dl>
        </div>

        <div class="cartao">
            <h3>Pontuação</h3>
            <dl class="dados">
                <dt>Sintomas</dt><dd>{{ $triagem->score_sintomas }}</dd>
                <dt>Sinais vitais</dt><dd>{{ $triagem->score_vitais }}</dd>
                <dt>Total</dt><dd>{{ $triagem->score_total }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection