@extends('layouts.totem')

@section('content')
<div class="card-standard" style="max-width: 800px; margin: 0 auto;">
    <form id="form-triagem" action="{{ route('triagem.finalizar') }}" method="POST">
        @csrf
        
        @foreach($subcategoria->perguntas as $index => $pergunta)
            <div class="pergunta-card @if($index > 0) d-none @endif" id="step-{{ $index }}">
                <h2 class="text-center mb-5">{{ $pergunta->texto_pergunta }}</h2>
                
                <input type="hidden" name="respostas[{{ $index }}]" id="input-{{ $index }}" value="0">
                
                <div class="quiz-grid">
                    <button type="button" class="btn-main" onclick="responder({{ $index }}, {{ $pergunta->score_manchester }})">SIM</button>
                    <button type="button" class="btn-main" style="background: #94a3b8" onclick="responder({{ $index }}, 0)">NÃO</button>
                </div>
            </div>
        @endforeach
    </form>
</div>

<script>
    const totalPerguntas = {{ $subcategoria->perguntas->count() }};
    
    function responder(indexAtual, score) {
        // Grava o score obtido na pergunta atual
        document.getElementById('input-' + indexAtual).value = score;
        
        // Esconde o card atual
        document.getElementById('step-' + indexAtual).classList.add('d-none');
        
        let proximo = indexAtual + 1;
        
        if (proximo < totalPerguntas) {
            // Mostra a próxima pergunta
            document.getElementById('step-' + proximo).classList.remove('d-none');
        } else {
            // Se as perguntas acabaram, submete o formulário via POST pro Laravel processar
            document.getElementById('form-triagem').submit();
        }
    }
</script>
@endsection