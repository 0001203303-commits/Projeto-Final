<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Smart-Triage')</title>
    <link rel="stylesheet" href="{{ asset('css/totem.css') }}">
</head>

<body>
    <header class="topo">Smart<span>-Triage</span></header>
    <main class="conteudo">
        <div class="cartao">
            @yield('conteudo')
        </div>
    </main>
</body>

</html>