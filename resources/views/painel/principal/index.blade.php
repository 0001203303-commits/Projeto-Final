@extends('layouts.painel')

@section('titulo', 'Painel Principal')

@section('conteudo')
@php
    $periodos = ['hoje' => 'Hoje', '7d' => '7 dias', '30d' => '30 dias'];
@endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Painel Principal</h1>
        <p class="sub">
            {{ \Illuminate\Support\Str::ucfirst(now()->translatedFormat('l, d \d\e F \d\e Y')) }}
            · <span id="atualizado">atualizado agora</span>
        </p>
    </div>

    <div class="abas">
        @foreach ($periodos as $chave => $rotulo)
            <a href="{{ route('painel.principal', ['periodo' => $chave]) }}" class="{{ $periodo === $chave ? 'ativa' : '' }}">{{ $rotulo }}</a>
        @endforeach
    </div>
</div>

<div class="offline" id="offline" hidden>
    Sem conexão com o servidor. Os números abaixo podem estar desatualizados; tentando reconectar...
</div>

<div id="conteudo">
    @include('painel.principal._conteudo')
</div>

<script>
    const URL_ATUALIZAR = @json(route('painel.principal.parcial', ['periodo' => $periodo]));
    let ultima = Date.now();
    let falhas = 0;

    async function atualizar() {
        try {
            const r = await fetch(URL_ATUALIZAR, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            if (r.redirected || r.status === 401 || r.status === 419) { location.reload(); return; }
            if (!r.ok) throw new Error(r.status);

            document.getElementById('conteudo').innerHTML = await r.text();
            ultima = Date.now();
            falhas = 0;
            document.getElementById('offline').hidden = true;
        } catch (e) {
            if (++falhas >= 2) document.getElementById('offline').hidden = false;
        }
    }

    setInterval(atualizar, 30000);

    setInterval(() => {
        const s = Math.round((Date.now() - ultima) / 1000);
        document.getElementById('atualizado').textContent = s < 5 ? 'atualizado agora' : 'atualizado há ' + s + ' s';
    }, 1000);
</script>
@endsection