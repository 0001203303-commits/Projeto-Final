@extends('layouts.painel')

@section('titulo', 'Visão geral')

@section('conteudo')
@php $cores = config('triagem.cores'); @endphp

<h1 class="titulo">Visão geral</h1>
<p class="sub">Números agregados de hoje. Dados de pacientes não aparecem neste perfil.</p>

<div class="grade">
    <div class="metrica"><b>{{ $totalHoje }}</b><span>Triagens hoje</span></div>
    <div class="metrica"><b>{{ $aguardando }}</b><span>Aguardando atendimento</span></div>
    <div class="metrica"><b>{{ $emAtendimento }}</b><span>Em atendimento</span></div>
    <div class="metrica"><b>{{ $usuarios }}</b><span>Usuários ativos</span></div>
</div>

<h3 class="titulo-secao">Triagens de hoje por gravidade</h3>
<div class="resumo-fila">
    @foreach (['laranja', 'amarelo', 'verde', 'azul'] as $c)
        <div class="contador cor-{{ $c }}">
            <b>{{ $porCor[$c] ?? 0 }}</b>
            <span>{{ $cores[$c]['nome'] }}</span>
        </div>
    @endforeach
</div>
@endsection