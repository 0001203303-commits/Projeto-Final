<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Painel') · Smart-Triage</title>
    <link rel="stylesheet" href="{{ asset('css/painel.css') }}">
</head>
<body>
@php
    $u = auth()->user();
    $rotulos = ['medico' => 'Médico', 'ti' => 'Técnico de TI', 'admin' => 'Administrador'];
@endphp
<div class="app">
    <aside class="lateral">
        <div class="marca">Smart<span>-Triage</span></div>

        <nav>
            @if ($u->perfil === 'medico')
                <a href="{{ route('painel.fila.index') }}" class="{{ request()->routeIs('painel.fila.*') ? 'ativo' : '' }}">Fila de atendimento</a>
            @endif
            @if ($u->perfil === 'admin')
                <a href="{{ route('painel.admin') }}" class="{{ request()->routeIs('painel.admin') ? 'ativo' : '' }}">Visão geral</a>
            @endif
            @if ($u->perfil === 'ti')
                <a href="{{ route('painel.ti') }}" class="{{ request()->routeIs('painel.ti') ? 'ativo' : '' }}">Status do sistema</a>
            @endif
        </nav>

        <div class="usuario">
            <div>{{ $u->name }}</div>
            <small>{{ $rotulos[$u->perfil] ?? $u->perfil }}</small>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Sair</button>
            </form>
        </div>
    </aside>

    <main class="principal">
        @if (session('ok'))
            <div class="flash ok">{{ session('ok') }}</div>
        @endif
        @error('acao')
            <div class="flash erro">{{ $message }}</div>
        @enderror

        @yield('conteudo')
    </main>
</div>
</body>
</html>