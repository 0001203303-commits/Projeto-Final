@extends('layouts.totem')

@section('conteudo')
    <h1>Bem-vindo(a)!</h1>
    <p class="sub">Vamos fazer uma triagem rápida, leva menos de 2 minutos. Um profissional de saúde sempre fará a avaliação final do seu atendimento.</p>
    <a href="{{ route('triagem.dados') }}" class="botao">Iniciar triagem</a>
    <p class="aviso">Seus dados são protegidos conforme a LGPD.</p>
@endsection