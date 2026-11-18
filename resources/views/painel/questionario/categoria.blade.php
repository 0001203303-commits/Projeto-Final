@extends('layouts.painel')

@section('titulo', $categoria->exists ? 'Editar categoria' : 'Nova categoria')

@section('conteudo')
<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">{{ $categoria->exists ? 'Editar categoria' : 'Nova categoria' }}</h1>
        <p class="sub">As categorias organizam os sintomas na tela do totem.</p>
    </div>
</div>

<form method="POST" class="cartao form-admin" novalidate
      action="{{ $categoria->exists ? route('painel.questionario.categorias.update', $categoria) : route('painel.questionario.categorias.store') }}">
    @csrf
    @if ($categoria->exists) @method('PUT') @endif

    <div class="campo">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" value="{{ old('nome', $categoria->nome) }}" maxlength="60" autocomplete="off" autofocus>
        @error('nome')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="icone">Ícone (um emoji, opcional)</label>
        <input type="text" id="icone" name="icone" value="{{ old('icone', $categoria->icone) }}" maxlength="8" placeholder="🫁" autocomplete="off">
        @error('icone')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="ordem">Ordem de exibição</label>
        <input type="number" id="ordem" name="ordem" value="{{ old('ordem', $categoria->ordem) }}" min="0" max="99">
        <div class="dica">Menor número aparece primeiro no totem.</div>
        @error('ordem')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="form-acoes">
        <button type="submit" class="btn destaque">{{ $categoria->exists ? 'Salvar alterações' : 'Criar categoria' }}</button>
        <a href="{{ route('painel.questionario.index') }}" class="btn claro">Cancelar</a>
    </div>
</form>
@endsection