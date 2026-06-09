@extends('layouts.totem')

@section('content')
<div class="card-standard">
    <h2 class="text-center mb-4">Subcategorias de {{ $categoriaPai->nome }}</h2>

    @if($subcategorias->count())
        <div class="quiz-grid">
            @foreach($subcategorias as $sub)
                <a href="{{ route('triagem.quiz', $sub->slug) }}" class="btn-quiz {{ $sub->css_class }}">
                    <i data-lucide="{{ $sub->icon }}"></i> {{ $sub->nome }}
                </a>
            @endforeach
        </div>
    @else
        <p class="text-center">Nenhuma subcategoria encontrada para esta categoria.</p>
        <div class="text-center mt-4">
            <a href="{{ route('triagem.index') }}" class="btn-main">Voltar</a>
        </div>
    @endif
</div>
@endsection
