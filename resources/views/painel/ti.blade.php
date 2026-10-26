@extends('layouts.painel')

@section('titulo', 'Status do sistema')

@section('conteudo')
<h1 class="titulo">Status do sistema</h1>
<p class="sub">Saúde dos componentes. Dados de pacientes não aparecem neste perfil.</p>

<div class="cartao">
    <div class="status-linha">
        <span><i class="bolinha {{ $banco ? 'ok' : 'falha' }}"></i>Banco de dados</span>
        <strong>{{ $banco ? 'Conectado' : 'Sem conexão' }}</strong>
    </div>
    <div class="status-linha">
        <span><i class="bolinha {{ $iaConfigurada ? 'ok' : 'falha' }}"></i>IA (Gemini)</span>
        <strong>{{ $iaConfigurada ? 'Configurada · ' . $modelo : 'Chave não configurada' }}</strong>
    </div>
    <div class="status-linha">
        <span>Resumos com IA nas últimas 24 h</span>
        <strong>{{ $comIa24h }} de {{ $triagens24h }}</strong>
    </div>
    <div class="status-linha"><span>Laravel / PHP</span><strong>{{ $laravel }} / {{ $php }}</strong></div>
    <div class="status-linha"><span>Ambiente</span><strong>{{ $ambiente }}</strong></div>
</div>

<p class="aviso">Se a quantidade de resumos com IA ficar muito abaixo do total de triagens, verifique a chave, a cota e a conexão com a internet. Os resumos locais continuam funcionando.</p>
@endsection