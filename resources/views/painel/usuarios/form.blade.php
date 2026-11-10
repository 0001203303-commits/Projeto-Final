@extends('layouts.painel')

@section('titulo', $usuario->exists ? 'Editar usuário' : 'Novo usuário')

@section('conteudo')
    @php $souEu = $usuario->exists && $usuario->id === auth()->id(); @endphp

    <div class="cabecalho-pagina">
        <div>
            <h1 class="titulo">{{ $usuario->exists ? 'Editar usuário' : 'Novo usuário' }}</h1>
            <p class="sub">
                {{ $usuario->exists ? 'Altere os dados de acesso.' : 'A senha temporária será gerada ao salvar.' }}</p>
        </div>
    </div>

    <form method="POST" class="cartao form-admin" novalidate
        action="{{ $usuario->exists ? route('painel.usuarios.update', $usuario) : route('painel.usuarios.store') }}">
        @csrf
        @if ($usuario->exists) @method('PUT') @endif

        <div class="campo">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" autocomplete="off" autofocus>
            @error('name')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" autocomplete="off">
            @error('email')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="perfil">Perfil</label>
            <select id="perfil" name="perfil" @disabled($souEu)>
                @foreach ($perfis as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(old('perfil', $usuario->perfil) === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
            @if ($souEu)
                <div class="dica">Você não pode alterar o seu próprio perfil.</div>
            @else
                <div class="dica">Médico vê a fila e as fichas. TI vê o status do sistema. Administrador gerencia usuários e a
                    configuração.</div>
            @endif
            @error('perfil')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="form-acoes">
            <button type="submit"
                class="btn destaque">{{ $usuario->exists ? 'Salvar alterações' : 'Criar usuário' }}</button>
            <a href="{{ route('painel.usuarios.index') }}" class="btn claro">Cancelar</a>
        </div>
    </form>
@endsection