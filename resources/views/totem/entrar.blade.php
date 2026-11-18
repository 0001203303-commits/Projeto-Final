@extends('layouts.totem')

@section('titulo', 'Entrar')

@section('conteudo')
    <h1>Que bom ver você de novo!</h1>
    <p class="sub">Informe seu CPF e sua data de nascimento para continuar.</p>

    <form method="POST" action="{{ route('triagem.entrar.enviar') }}" novalidate>
        @csrf

        <div class="campo">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" inputmode="numeric" maxlength="14" placeholder="000.000.000-00" autocomplete="off" autofocus>
            @error('cpf')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="data_nascimento">Data de nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" max="{{ now()->toDateString() }}" autocomplete="off">
            @error('data_nascimento')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="botoes">
            <a href="{{ route('triagem.inicio') }}" class="botao secundario">Voltar</a>
            <button type="submit" class="botao">Entrar</button>
        </div>

        <a href="{{ route('triagem.dados') }}" class="link-simples">Primeira vez aqui? Faça seu cadastro</a>
    </form>

    <script>
        const cpf = document.getElementById('cpf');
        cpf.addEventListener('input', () => {
            let v = cpf.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpf.value = v;
        });
    </script>
@endsection