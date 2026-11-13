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
        $svg = 'class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
    @endphp
    <div class="app">
        <aside class="lateral">
            <div class="marca">Smart<span>-Triage</span></div>

            <nav>
                @if ($u->perfil === 'medico')
                    <a href="{{ route('painel.admin') }}" class="{{ request()->routeIs('painel.admin*') ? 'ativo' : '' }}">
                        <svg {!! $svg !!}>
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        Visão geral
                    </a>
                    <a href="{{ route('painel.fila.index') }}"
                        class="{{ request()->routeIs('painel.fila.*') ? 'ativo' : '' }}">
                        <svg {!! $svg !!}>
                            <line x1="8" y1="6" x2="21" y2="6" />
                            <line x1="8" y1="12" x2="21" y2="12" />
                            <line x1="8" y1="18" x2="21" y2="18" />
                            <line x1="3" y1="6" x2="3.01" y2="6" />
                            <line x1="3" y1="12" x2="3.01" y2="12" />
                            <line x1="3" y1="18" x2="3.01" y2="18" />
                        </svg>
                        Fila de Atendimento
                    </a>
                    <a href="{{ route('painel.historico.index') }}"
                        class="{{ request()->routeIs('painel.historico.*') ? 'ativo' : '' }}">
                        <svg {!! $svg !!}>
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        Históricos
                    </a>
                    <span class="nav-desativado" title="Em breve">
                        <svg {!! $svg !!}>
                            <line x1="4" y1="21" x2="4" y2="14" />
                            <line x1="4" y1="10" x2="4" y2="3" />
                            <line x1="12" y1="21" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12" y2="3" />
                            <line x1="20" y1="21" x2="20" y2="16" />
                            <line x1="20" y1="12" x2="20" y2="3" />
                            <line x1="1" y1="14" x2="7" y2="14" />
                            <line x1="9" y1="8" x2="15" y2="8" />
                            <line x1="17" y1="16" x2="23" y2="16" />
                        </svg>
                        Configurações <em>em breve</em>
                    </span>
                @endif

                @if ($u->perfil === 'admin')
                    <a href="{{ route('painel.admin') }}" class="{{ request()->routeIs('painel.admin') ? 'ativo' : '' }}">
                        <svg {!! $svg !!}>
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        Visão geral
                    </a>
                    <a href="{{ route('painel.usuarios.index') }}"
                        class="{{ request()->routeIs('painel.usuarios.*') ? 'ativo' : '' }}">
                        <svg {!! $svg !!}>
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Usuários
                    </a>
                    <a href="{{ route('painel.questionario.index') }}"
                        class="{{ request()->routeIs('painel.questionario.*') ? 'ativo' : '' }}">
                        <svg {!! $svg !!}>
                            <circle cx="12" cy="12" r="10" />
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        Sintomas e Perguntas
                    </a>
                    <span class="nav-desativado" title="Em breve">
                        <a href="{{ route('painel.relatorios.index') }}"
                            class="{{ request()->routeIs('painel.relatorios.*') ? 'ativo' : '' }}">
                            <svg {!! $svg !!}>
                                <line x1="18" y1="20" x2="18" y2="10" />
                                <line x1="12" y1="20" x2="12" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="14" />
                            </svg>
                            Relatórios
                        </a>
                        <span class="nav-desativado" title="Em breve">
                            <svg {!! $svg !!}>
                                <line x1="4" y1="21" x2="4" y2="14" />
                                <line x1="4" y1="10" x2="4" y2="3" />
                                <line x1="12" y1="21" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12" y2="3" />
                                <line x1="20" y1="21" x2="20" y2="16" />
                                <line x1="20" y1="12" x2="20" y2="3" />
                                <line x1="1" y1="14" x2="7" y2="14" />
                                <line x1="9" y1="8" x2="15" y2="8" />
                                <line x1="17" y1="16" x2="23" y2="16" />
                            </svg>
                            Configurações <em>em breve</em>
                        </span>
                @endif

                    @if ($u->perfil === 'ti')
                        <a href="{{ route('painel.ti') }}"
                            class="{{ request()->routeIs('painel.ti', 'painel.ti.parcial') ? 'ativo' : '' }}">
                            <svg {!! $svg !!}>
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                            Painel Principal
                        </a>
                        <a href="{{ route('painel.totens.index') }}"
                            class="{{ request()->routeIs('painel.totens.*') ? 'ativo' : '' }}">
                            <svg {!! $svg !!}>
                                <rect x="2" y="3" width="20" height="14" rx="2" />
                                <line x1="8" y1="21" x2="16" y2="21" />
                                <line x1="12" y1="17" x2="12" y2="21" />
                            </svg>
                            Totens
                        </a>
                        <a href="{{ route('painel.bancoapi.index') }}"
                            class="{{ request()->routeIs('painel.bancoapi.*') ? 'ativo' : '' }}">
                            <svg {!! $svg !!}>
                                <ellipse cx="12" cy="5" rx="9" ry="3" />
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                            </svg>
                            Banco de Dados e API
                        </a>
                        <span class="nav-desativado" title="Em breve">
                            <svg {!! $svg !!}>
                                <line x1="4" y1="21" x2="4" y2="14" />
                                <line x1="4" y1="10" x2="4" y2="3" />
                                <line x1="12" y1="21" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12" y2="3" />
                                <line x1="20" y1="21" x2="20" y2="16" />
                                <line x1="20" y1="12" x2="20" y2="3" />
                                <line x1="1" y1="14" x2="7" y2="14" />
                                <line x1="9" y1="8" x2="15" y2="8" />
                                <line x1="17" y1="16" x2="23" y2="16" />
                            </svg>
                            Configurações <em>em breve</em>
                        </span>
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