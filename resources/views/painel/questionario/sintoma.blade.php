@extends('layouts.painel')

@section('titulo', $sintoma->exists ? 'Editar sintoma' : 'Novo sintoma')

@section('conteudo')
<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">{{ $sintoma->exists ? 'Editar sintoma' : 'Novo sintoma' }}</h1>
        <p class="sub">A pontuação base entra no cálculo sempre que o paciente marca este sintoma.</p>
    </div>
</div>

<form method="POST" class="cartao form-admin" novalidate
      action="{{ $sintoma->exists ? route('painel.questionario.sintomas.update', $sintoma) : route('painel.questionario.sintomas.store') }}">
    @csrf
    @if ($sintoma->exists) @method('PUT') @endif

    <div class="campo">
        <label for="categoria_id">Categoria</label>
        <select id="categoria_id" name="categoria_id">
            <option value="">Selecione...</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected((int) old('categoria_id', $sintoma->categoria_id) === $c->id)>{{ $c->icone }} {{ $c->nome }}</option>
            @endforeach
        </select>
        @error('categoria_id')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="nome">Nome do sintoma</label>
        <input type="text" id="nome" name="nome" value="{{ old('nome', $sintoma->nome) }}" maxlength="80" autocomplete="off" autofocus>
        @error('nome')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="pontos_base">Pontuação base (0 a 10)</label>
        <input type="number" id="pontos_base" name="pontos_base" value="{{ old('pontos_base', $sintoma->pontos_base) }}" min="0" max="10">
        <div class="dica">Sintomas de maior risco têm base mais alta (ex.: desconforto no peito = 4).</div>
        @error('pontos_base')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="cor_minima">Cor mínima ao marcar este sintoma</label>
        <select id="cor_minima" name="cor_minima">
            <option value="">Nenhuma</option>
            <option value="amarelo" @selected(old('cor_minima', $sintoma->cor_minima) === 'amarelo')>Amarelo (urgente)</option>
            <option value="laranja" @selected(old('cor_minima', $sintoma->cor_minima) === 'laranja')>Laranja (muito urgente)</option>
        </select>
        <div class="dica">Só eleva a classificação: o paciente nunca fica abaixo desta cor, mesmo com pontuação baixa.</div>
        @error('cor_minima')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="form-acoes">
        <button type="submit" class="btn destaque">{{ $sintoma->exists ? 'Salvar alterações' : 'Criar sintoma' }}</button>
        <a href="{{ route('painel.questionario.index') }}" class="btn claro">Cancelar</a>
    </div>
</form>
@endsection