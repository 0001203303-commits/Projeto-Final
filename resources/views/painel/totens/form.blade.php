@extends('layouts.painel')

@section('titulo', $totem->exists ? 'Editar totem' : 'Novo totem')

@section('conteudo')
<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">{{ $totem->exists ? 'Editar totem' : 'Novo totem' }}</h1>
        <p class="sub">{{ $totem->exists ? 'Altere as informações de identificação.' : 'O token de acesso será gerado ao salvar.' }}</p>
    </div>
</div>

<form method="POST" class="cartao form-admin" novalidate
      action="{{ $totem->exists ? route('painel.totens.update', $totem) : route('painel.totens.store') }}">
    @csrf
    @if ($totem->exists) @method('PUT') @endif

    <div class="campo">
        <label for="nome">Nome do totem</label>
        <input type="text" id="nome" name="nome" value="{{ old('nome', $totem->nome) }}" maxlength="100" placeholder="Ex.: Totem Recepção 1" autocomplete="off" autofocus>
        @error('nome')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="local">Local de instalação (opcional)</label>
        <input type="text" id="local" name="local" value="{{ old('local', $totem->local) }}" maxlength="150" placeholder="Ex.: Entrada principal, térreo" autocomplete="off">
        @error('local')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="observacoes">Observações (opcional)</label>
        <textarea id="observacoes" name="observacoes" rows="3" maxlength="500" placeholder="Modelo do equipamento, responsável, anotações de manutenção...">{{ old('observacoes', $totem->observacoes) }}</textarea>
        @error('observacoes')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="form-acoes">
        <button type="submit" class="btn destaque">{{ $totem->exists ? 'Salvar alterações' : 'Cadastrar totem' }}</button>
        <a href="{{ route('painel.totens.index') }}" class="btn claro">Cancelar</a>
    </div>
</form>
@endsection