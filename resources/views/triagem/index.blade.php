@extends('layouts.totem')

@section('content')
<div class="card-standard">
    <h2 class="text-center mb-4">Selecione a categoria principal do seu sintoma:</h2>
    <div class="quiz-grid">
        @foreach($categorias as $cat)
            <a href="{{ route('triagem.subs', $cat->slug) }}" class="btn-quiz {{ $cat->css_class }}">
                <i data-lucide="{{ $cat->icon }}"></i> {{ $cat->nome }}
            </a>
        @endforeach
    </div>
</div>
@endsection