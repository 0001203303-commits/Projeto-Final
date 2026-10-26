<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar · Smart-Triage</title>
    <link rel="stylesheet" href="{{ asset('css/painel.css') }}">
</head>
<body class="login">
    <div class="login-caixa">
        <h1>Smart<span>-Triage</span></h1>
        <p class="sub">Acesso da equipe</p>

        <form method="POST" action="{{ route('login.entrar') }}" novalidate>
            @csrf

            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="username" autofocus>
                @error('email')<div class="erro">{{ $message }}</div>@enderror
            </div>

            <div class="campo">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" autocomplete="current-password">
                @error('password')<div class="erro">{{ $message }}</div>@enderror
            </div>

            <label class="lembrar"><input type="checkbox" name="lembrar" value="1"> Manter conectado</label>

            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
</body>
</html>