@extends('layouts.painel')

@section('titulo', 'Banco de Dados e API')

@section('conteudo')
<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Banco de Dados e API</h1>
        <p class="sub">Monitoramento, manutenção e configuração da infraestrutura. Conteúdo de pacientes nunca é exibido aqui.</p>
    </div>

    <div class="topo-acoes">
        <div class="abas">
            <a href="{{ route('painel.bancoapi.index', ['aba' => 'banco']) }}" class="{{ $aba === 'banco' ? 'ativa' : '' }}">Banco de dados</a>
            <a href="{{ route('painel.bancoapi.index', ['aba' => 'api']) }}" class="{{ $aba === 'api' ? 'ativa' : '' }}">API</a>
        </div>

        <a href="{{ route('painel.bancoapi.index', ['aba' => $aba]) }}" class="btn claro btn-com-icone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Atualizar
        </a>
    </div>
</div>

@php $teste = session('teste'); @endphp
@if ($teste)
    <div class="flash {{ $teste['ok'] ? 'ok' : 'erro' }}">
        {{ $teste['mensagem'] }}@if (! empty($teste['ms'])) ({{ $teste['ms'] }} ms)@endif
    </div>
@endif

@include($aba === 'banco' ? 'painel.bancoapi._banco' : 'painel.bancoapi._api')

<script>
    // Confirmação genérica: qualquer formulário com data-confirmar
    document.addEventListener('submit', e => {
        const msg = e.target.dataset.confirmar;
        if (msg && !confirm(msg)) e.preventDefault();
    });
</script>
@endsection