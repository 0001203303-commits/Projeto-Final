<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ficha {{ str_pad($triagem->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #eef4f4; margin: 0; padding: 24px; color: #0F1A1C; }
        .card { max-width: 760px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 6px 24px rgba(0,0,0,.1); }
        h2 { margin: 0 0 4px; color: #0B4F55; }
        h3 { color: #0B4F55; margin: 22px 0 6px; font-size: 1.05rem; }
        .cor { display: inline-block; padding: 3px 12px; border-radius: 999px; color: #fff; font-size: .85rem; font-weight: 700; vertical-align: middle; }
        .cor-laranja { background: #F28C28; } .cor-amarelo { background: #E5B800; color: #3b2f00; }
        .cor-verde { background: #2E9E55; } .cor-azul { background: #2F80ED; }
        .meta { color: #4a5a5c; font-size: .95rem; }
        .vitais { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }
        .vitais div { background: #F2F8F8; border-radius: 10px; padding: 8px 14px; }
        .vitais b { display: block; font-size: 1.2rem; }
        ul { margin: 6px 0; padding-left: 20px; } li { margin-bottom: 4px; }
        .alerta { background: #FDECEA; color: #C0392B; font-weight: 700; padding: 10px 14px; border-radius: 10px; margin: 14px 0; }
        .ia { background: #F2F8F8; border-left: 4px solid #14888A; padding: 12px 16px; border-radius: 8px; }
        .aviso { font-size: .82rem; color: #6a7a7c; margin-top: 18px; }
        .origem { font-size: .8rem; color: #6a7a7c; }
    </style>
</head>
<body>
@php $r = $triagem->resumo_ia; @endphp
<div class="card">
    <h2>
        Ficha nº {{ str_pad($triagem->id, 4, '0', STR_PAD_LEFT) }}
        @if ($triagem->cor)
            <span class="cor cor-{{ $triagem->cor }}">{{ strtoupper($triagem->cor) }}</span>
        @endif
    </h2>
    <p class="meta">
        {{ $triagem->paciente->nome }} · {{ $triagem->paciente->idade }} anos
        · Pontuação {{ $triagem->score_total }}
        (sintomas {{ $triagem->score_sintomas }}, vitais {{ $triagem->score_vitais }})
    </p>

    @if ($triagem->alerta_imediato)
        <div class="alerta">Alerta imediato: avaliar este paciente agora.</div>
    @endif

    <h3>Sinais vitais</h3>
    <div class="vitais">
        <div><b>{{ $triagem->bpm }}</b>bpm</div>
        <div><b>{{ $triagem->spo2 }}%</b>SpO₂</div>
        <div><b>{{ $triagem->pa_sistolica }}/{{ $triagem->pa_diastolica }}</b>mmHg</div>
        <div><b>{{ number_format((float) $triagem->temperatura, 1, ',', '.') }}</b>°C</div>
    </div>

    <h3>Por que esta cor?</h3>
    <ul>
        @foreach ($triagem->motivos ?? [] as $motivo)
            <li>{{ $motivo }}</li>
        @endforeach
    </ul>

    <h3>Resumo para o médico</h3>
    @if ($r)
        <div class="ia">{{ $r['resumo'] }}</div>
        <p class="origem">
            @if (($r['origem'] ?? '') === 'gemini')
                Gerado por IA ({{ $r['modelo'] }}).
            @else
                Resumo automático local. A versão com IA pode estar sendo gerada ou estar indisponível. <a href="">Atualizar</a>
            @endif
        </p>

        @if (! empty($r['hipoteses']))
            <h3>Hipóteses a investigar</h3>
            <ul>@foreach ($r['hipoteses'] as $item)<li>{{ $item }}</li>@endforeach</ul>
        @endif

        @if (! empty($r['inconsistencias']))
            <h3>Inconsistências entre relato e sinais vitais</h3>
            <ul>@foreach ($r['inconsistencias'] as $item)<li>{{ $item }}</li>@endforeach</ul>
        @endif

        @if (! empty($r['pontos_atencao']))
            <h3>Pontos de atenção</h3>
            <ul>@foreach ($r['pontos_atencao'] as $item)<li>{{ $item }}</li>@endforeach</ul>
        @endif
    @else
        <p class="meta">Resumo ainda não gerado.</p>
    @endif

    <p class="aviso">
        Apoio à decisão gerado automaticamente a partir de uma pré-triagem. Não constitui diagnóstico.
        A avaliação e a conduta são sempre do profissional de saúde.
    </p>
</div>
</body>
</html>