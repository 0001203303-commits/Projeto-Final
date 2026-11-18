@extends('layouts.painel')

@section('titulo', 'Configurações')

@section('conteudo')
    @php
        $rotulos = ['medico' => 'Médico', 'ti' => 'Técnico de TI', 'admin' => 'Administrador'];
        $temaAtual = old('tema', $usuario->tema ?? 'auto');
    @endphp

    <div class="cabecalho-pagina">
        <div>
            <h1 class="titulo">Configurações</h1>
            <p class="sub">Gerencie os dados, a senha e a aparência da sua conta.</p>
        </div>
    </div>

    {{-- Informações da conta --}}
    <form method="POST" action="{{ route('painel.configuracoes.perfil') }}" class="cartao form-admin config-bloco"
        novalidate>
        @csrf
        @method('PUT')
        <h3>Informações da conta</h3>

        <div class="campo">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" maxlength="150"
                autocomplete="name">
            @error('name', 'perfil')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" maxlength="150"
                autocomplete="email">
            @error('email', 'perfil')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="perfil_senha">Senha atual</label>
            <input type="password" id="perfil_senha" name="senha_atual" autocomplete="current-password">
            <div class="dica">Necessária apenas para trocar o e-mail.</div>
            @error('senha_atual', 'perfil')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label>Perfil de acesso</label>
            <div class="campo-leitura">{{ $rotulos[$usuario->perfil] ?? $usuario->perfil }}</div>
            <div class="dica">Somente um administrador pode alterar o perfil.</div>
        </div>

        <div class="form-acoes">
            <button type="submit" class="btn destaque">Salvar informações</button>
        </div>
    </form>

    {{-- Senha --}}
    <form method="POST" action="{{ route('painel.configuracoes.senha') }}" class="cartao form-admin config-bloco"
        novalidate>
        @csrf
        @method('PUT')
        <h3>Alterar senha</h3>

        <div class="campo">
            <label for="senha_atual">Senha atual</label>
            <input type="password" id="senha_atual" name="senha_atual" autocomplete="current-password">
            @error('senha_atual', 'senha')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="nova_senha">Nova senha</label>
            <input type="password" id="nova_senha" name="nova_senha" autocomplete="new-password">
            <div class="dica">Mínimo de 10 caracteres, com letras maiúsculas e minúsculas e pelo menos um número.</div>
            @error('nova_senha', 'senha')
            <div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="nova_senha_confirmation">Confirmar nova senha</label>
            <input type="password" id="nova_senha_confirmation" name="nova_senha_confirmation" autocomplete="new-password">
        </div>

        <div class="form-acoes">
            <button type="submit" class="btn destaque">Alterar senha</button>
        </div>
    </form>

    {{-- Aparência --}}
    <form method="POST" action="{{ route('painel.configuracoes.aparencia') }}" class="cartao form-admin config-bloco"
        novalidate>
        @csrf
        @method('PUT')
        <h3>Aparência</h3>

        <div class="temas">
            <label class="tema-opcao">
                <input type="radio" name="tema" value="claro" @checked($temaAtual === 'claro')>
                <span>Claro<small>Fundo branco</small></span>
            </label>
            <label class="tema-opcao">
                <input type="radio" name="tema" value="escuro" @checked($temaAtual === 'escuro')>
                <span>Escuro<small>Bom para plantão noturno</small></span>
            </label>
            <label class="tema-opcao">
                <input type="radio" name="tema" value="auto" @checked($temaAtual === 'auto')>
                <span>Automático<small>Segue o sistema</small></span>
            </label>
        </div>
        <div class="dica">A prévia é imediata. Clique em salvar para manter a escolha em qualquer computador.</div>
        @error('tema')
        <div class="erro">{{ $message }}</div>@enderror

        <div class="form-acoes">
            <button type="submit" class="btn destaque">Salvar aparência</button>
        </div>
    </form>

    {{-- Excluir conta --}}
    <section class="cartao form-admin config-bloco zona-perigo">
        <h3>Excluir conta</h3>

        @if ($temHistorico)
            <p>Você já atendeu pacientes. Por isso a sua conta será <strong>desativada</strong>, e não apagada, para preservar o
                histórico de atendimentos. Um administrador pode reativá-la.</p>
        @else
            <p>Sua conta será <strong>excluída definitivamente</strong>. Esta ação não pode ser desfeita.</p>
        @endif

        @error('acao', 'excluir')
        <div class="flash erro">{{ $message }}</div>@enderror

        @if ($atendimentoAberto)
            <div class="flash erro">Você tem um atendimento em andamento. Finalize-o antes de excluir a conta.</div>
        @elseif ($ultimoAdmin)
            <div class="flash erro">Você é o único administrador ativo. Crie outro administrador antes de excluir esta conta.
            </div>
        @else
            <form method="POST" action="{{ route('painel.configuracoes.excluir') }}" novalidate
                onsubmit="return confirm('{{ $temHistorico ? 'Desativar' : 'Excluir' }} a sua conta agora?')">
                @csrf
                @method('DELETE')

                <div class="campo">
                    <label for="excluir_senha">Sua senha</label>
                    <input type="password" id="excluir_senha" name="senha_atual" autocomplete="current-password">
                    @error('senha_atual', 'excluir')
                    <div class="erro">{{ $message }}</div>@enderror
                </div>

                <div class="campo">
                    <label for="confirmacao">Digite EXCLUIR para confirmar</label>
                    <input type="text" id="confirmacao" name="confirmacao" autocomplete="off">
                    @error('confirmacao', 'excluir')
                    <div class="erro">{{ $message }}</div>@enderror
                </div>

                <div class="form-acoes">
                    <button type="submit"
                        class="btn perigo">{{ $temHistorico ? 'Desativar minha conta' : 'Excluir minha conta' }}</button>
                </div>
            </form>
        @endif
    </section>

    <script>
        // Prévia imediata do tema, antes de salvar
        document.querySelectorAll('input[name=tema]').forEach(r => r.addEventListener('change', () => {
            let t = r.value;
            if (t === 'auto') t = matchMedia('(prefers-color-scheme: dark)').matches ? 'escuro' : 'claro';
            document.documentElement.dataset.tema = t;
        }));
    </script>
@endsection