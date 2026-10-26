@extends('layouts.painel')

@section('titulo', 'Fila de atendimento')

@section('conteudo')
    <h1 class="titulo">Fila de atendimento</h1>
    <p class="sub">Ordenada por gravidade e, dentro de cada cor, por ordem de chegada. Atualizada às <span id="hora">{{ now()->format('H:i:s') }}</span>.</p>

    <div id="lista">
        @include('painel.fila._lista')
    </div>

    <script>
        const urlLista = @json(route('painel.fila.parcial'));

        async function atualizar() {
            try {
                const r = await fetch(urlLista, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

                // Sessão expirada: o Laravel redireciona para o login
                if (r.redirected) { location.reload(); return; }

                if (r.ok) {
                    document.getElementById('lista').innerHTML = await r.text();
                    document.getElementById('hora').textContent = new Date().toLocaleTimeString('pt-BR');
                }
            } catch (e) { /* mantém a lista atual se a rede falhar */ }
        }

        setInterval(atualizar, 10000);
    </script>
@endsection