@extends('layouts.totem')

@section('titulo', 'Resultado')

@section('conteudo')
    <div class="resultado cor-{{ $triagem->cor }}">
        <h1>Triagem concluída</h1>
        <p class="ficha">Ficha nº {{ str_pad($triagem->id, 4, '0', STR_PAD_LEFT) }}</p>

        <div class="selo">{{ strtoupper($info['nome']) }}</div>

        <p style="font-size:1.3rem;margin:0"><strong>{{ $info['descricao'] }}</strong></p>
        <p class="tempo">
            @if ($triagem->cor === 'azul')
                Encaminhamento: {{ $info['tempo'] }}
            @else
                Tempo máximo de espera sugerido: {{ $info['tempo'] }}
            @endif
        </p>

        @if ($triagem->alerta_imediato)
            <div class="alerta-imediato">Procure a equipe de enfermagem agora.</div>
        @endif

        <ul class="orientacoes">
            <li>Esta é uma classificação preliminar. A avaliação final é sempre feita pela equipe de enfermagem.</li>
            <li>Aguarde na recepção; você será chamado(a) pelo nome.</li>
            <li>Se piorar enquanto espera, avise a recepção imediatamente.</li>
        </ul>

        <form method="POST" action="{{ route('triagem.encerrar') }}" id="form-encerrar">
            @csrf
            <button type="submit" class="botao">Concluir</button>
        </form>
    </div>

    <script>
        // O totem volta sozinho para a tela inicial, protegendo os dados do paciente
        setTimeout(() => document.getElementById('form-encerrar').submit(), 60000);
    </script>
@endsection