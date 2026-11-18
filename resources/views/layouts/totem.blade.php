<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Smart-Triage')</title>
    <link rel="stylesheet" href="{{ asset('css/totem.css') }}">
</head>

    @unless (request()->routeIs('triagem.inicio'))
        <form id="form-inativo" method="POST" action="{{ route('triagem.encerrar') }}" hidden>@csrf</form>
        <script>
            // Sem toque por 2 minutos: encerra a sessão e volta ao início
            (function () {
                var tempo;
                function reiniciar() {
                    clearTimeout(tempo);
                    tempo = setTimeout(function () { document.getElementById('form-inativo').submit(); }, 120000);
                }
                ['click', 'keydown', 'pointerdown', 'touchstart', 'input'].forEach(function (ev) {
                    document.addEventListener(ev, reiniciar, { passive: true });
                });
                reiniciar();
            })();
        </script>
    @endunless

<body>
    <header class="topo">Smart<span>-Triage</span></header>
    <main class="conteudo">
        <div class="cartao">
            @yield('conteudo')
        </div>
    </main>
</body>

</html>