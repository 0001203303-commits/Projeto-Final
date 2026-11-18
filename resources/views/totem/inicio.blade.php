@extends('layouts.totem')

@section('conteudo')
    <h1>Bem-vindo(a)!</h1>
    <p class="sub">Vamos fazer uma triagem rápida, leva menos de 2 minutos. Um profissional de saúde sempre fará a avaliação final do seu atendimento.</p>

    <div class="acoes-inicio">
        <a href="{{ route('triagem.entrar') }}" class="botao">Já tenho cadastro</a>
        <a href="{{ route('triagem.dados') }}" class="botao secundario">Primeira vez aqui</a>
    </div>

    <p class="aviso">Seus dados são protegidos conforme a LGPD.</p>
@endsection