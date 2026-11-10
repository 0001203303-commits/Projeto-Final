@extends('layouts.painel')

@section('titulo', 'Painel Principal')

@section('conteudo')
<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Painel Principal</h1>
        <p class="sub">Saúde do sistema · <span id="atualizado">atualizado agora</span></p>
    </div>
    <button type="button" class="btn claro" id="btn-atualizar">Atualizar</button>
</div>

<div class="offline" id="offline" hidden>
    Sem conexão com o servidor. Os dados abaixo podem estar desatualizados; tentando reconectar...
</div>

<div id="conteudo">
    @include('painel.ti._conteudo')
</div>

<script>
    const URL_ATUALIZAR = @json(route('painel.ti.parcial'));
    const conteudo = document.getElementById('conteudo');
    let ultima = Date.now();
    let falhas = 0;

    async function atualizar() {
        try {
            const r = await fetch(URL_ATUALIZAR, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            if (r.redirected || r.status === 401 || r.status === 419) { location.reload(); return; }
            if (!r.ok) throw new Error(r.status);

            conteudo.innerHTML = await r.text();
            ultima = Date.now();
            falhas = 0;
            document.getElementById('offline').hidden = true;
        } catch (e) {
            if (++falhas >= 2) document.getElementById('offline').hidden = false;
        }
    }

    document.getElementById('btn-atualizar').addEventListener('click', atualizar);
    setInterval(atualizar, 30000);

    setInterval(() => {
        const s = Math.round((Date.now() - ultima) / 1000);
        document.getElementById('atualizado').textContent = s < 5 ? 'atualizado agora' : 'atualizado há ' + s + ' s';
    }, 1000);
</script>
@endsection