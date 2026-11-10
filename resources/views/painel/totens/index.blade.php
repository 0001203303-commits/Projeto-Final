@extends('layouts.painel')

@section('titulo', 'Totens')

@section('conteudo')
@php $novo = session('token_totem'); @endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Totens</h1>
        <p class="sub">
            Dispositivos de autoatendimento cadastrados. Um totem é considerado online se avisou ao sistema nos últimos
            {{ (int) round(config('triagem.totem_online_segundos', 120) / 60) }} minutos
            · <span id="atualizado">atualizado agora</span>
        </p>
    </div>

    <div class="acoes-linha">
        <button type="button" class="btn claro" id="btn-atualizar">Atualizar</button>
        <a href="{{ route('painel.totens.create') }}" class="btn destaque">+ Novo totem</a>
    </div>
</div>

@if ($novo)
    <div class="caixa-senha">
        <strong>Token do totem "{{ $novo['nome'] }}"</strong>
        <div class="token-linha">
            <code class="token" id="token-texto">{{ $novo['token'] }}</code>
            <button type="button" class="btn claro pequeno" id="btn-copiar">Copiar</button>
        </div>
        <p>Guarde agora: ele não será mostrado de novo. Configure-o no dispositivo como <em>Authorization: Bearer &lt;token&gt;</em>. Se for perdido, gere um novo.</p>
    </div>
@endif

<div class="offline" id="offline" hidden>
    Sem conexão com o servidor. A tabela abaixo pode estar desatualizada; tentando reconectar...
</div>

<div id="conteudo">
    @include('painel.totens._tabela')
</div>

<script>
    const URL_ATUALIZAR = @json(route('painel.totens.parcial'));
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
    setInterval(atualizar, 15000);

    setInterval(() => {
        const s = Math.round((Date.now() - ultima) / 1000);
        document.getElementById('atualizado').textContent = s < 5 ? 'atualizado agora' : 'atualizado há ' + s + ' s';
    }, 1000);

    const copiar = document.getElementById('btn-copiar');
    if (copiar) {
        copiar.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(document.getElementById('token-texto').textContent);
                copiar.textContent = 'Copiado!';
            } catch (e) {
                copiar.textContent = 'Selecione e copie manualmente';
            }
        });
    }
</script>
@endsection