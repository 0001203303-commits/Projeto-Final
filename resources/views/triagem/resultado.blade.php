@extends('layouts.totem')

@section('content')
<div class="card-standard" style="max-width: 640px; margin: 0 auto;">
    <h2 class="text-center mb-4">Resultado da Triagem</h2>

    <div class="resultado-card" style="border-top: 6px solid {{ $resultado['hex'] }}; padding: 1.5rem;">
        <p class="text-center mb-2">Classificação: <strong>{{ $resultado['cor'] }}</strong></p>
        <p class="text-center mb-2">Urgência: <strong>{{ $resultado['urgencia'] }}</strong></p>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('triagem.index') }}" class="btn-main">Voltar ao início</a>
    </div>
</div>
@endsection
