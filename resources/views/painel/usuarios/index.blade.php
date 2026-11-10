@extends('layouts.painel')

@section('titulo', 'Usuários')

@section('conteudo')
@php
    $senha = session('senha_temporaria');
    $euId = auth()->id();
@endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Usuários</h1>
        <p class="sub">Gerencie quem acessa o painel e com qual perfil.</p>
    </div>
    <a href="{{ route('painel.usuarios.create') }}" class="btn destaque">+ Novo usuário</a>
</div>

@if ($senha)
    <div class="caixa-senha">
        <strong>Senha temporária de {{ $senha['nome'] }}</strong>
        <div>E-mail: {{ $senha['email'] }}</div>
        <code>{{ $senha['senha'] }}</code>
        <p>Anote agora: ela não será mostrada de novo. Entregue-a ao usuário por um canal seguro.</p>
    </div>
@endif

<div class="kpis">
    <div class="kpi"><span>Contas ativas</span><b>{{ $usuarios->where('ativo', true)->count() }}</b><small>{{ $usuarios->where('ativo', false)->count() }} desativadas</small></div>
    <div class="kpi"><span>Médicos</span><b>{{ $usuarios->where('ativo', true)->where('perfil', 'medico')->count() }}</b><small>ativos</small></div>
    <div class="kpi"><span>Técnicos de TI</span><b>{{ $usuarios->where('ativo', true)->where('perfil', 'ti')->count() }}</b><small>ativos</small></div>
    <div class="kpi"><span>Administradores</span><b>{{ $usuarios->where('ativo', true)->where('perfil', 'admin')->count() }}</b><small>ativos</small></div>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead>
            <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Situação</th><th>Atendimentos</th><th>Ações</th></tr>
        </thead>
        <tbody>
        @foreach ($usuarios as $u)
            @php $souEu = $u->id === $euId; @endphp
            <tr class="{{ $u->ativo ? '' : 'linha-inativa' }}">
                <td>{{ $u->name }} @if ($souEu)<small>você</small>@endif</td>
                <td>{{ $u->email }}</td>
                <td><span class="status-tag status-aguardando">{{ $perfis[$u->perfil] ?? $u->perfil }}</span></td>
                <td>
                    @if ($u->ativo)
                        <span class="status-tag status-finalizada">Ativa</span>
                    @else
                        <span class="status-tag tag-inativo">Desativada</span>
                    @endif
                </td>
                <td>{{ $u->triagens_atendidas_count }}</td>
                <td>
                    <div class="acoes-linha">
                        <a href="{{ route('painel.usuarios.edit', $u) }}" class="btn claro pequeno">Editar</a>

                        <form method="POST" action="{{ route('painel.usuarios.senha', $u) }}"
                              onsubmit="return confirm('Gerar uma nova senha temporária para {{ e($u->name) }}? A senha atual deixará de funcionar.')">
                            @csrf
                            <button type="submit" class="btn claro pequeno">Redefinir senha</button>
                        </form>

                        @unless ($souEu)
                            <form method="POST" action="{{ route('painel.usuarios.alternar', $u) }}"
                                  onsubmit="return confirm('{{ $u->ativo ? 'Desativar' : 'Reativar' }} a conta de {{ e($u->name) }}?')">
                                @csrf
                                <button type="submit" class="btn claro pequeno">{{ $u->ativo ? 'Desativar' : 'Reativar' }}</button>
                            </form>

                            @if ($u->triagens_atendidas_count === 0)
                                <form method="POST" action="{{ route('painel.usuarios.destroy', $u) }}"
                                      onsubmit="return confirm('Excluir {{ e($u->name) }} definitivamente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn perigo pequeno">Excluir</button>
                                </form>
                            @endif
                        @endunless
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<p class="aviso" style="margin-top:12px">
    Quem já atendeu pacientes não pode ser excluído, apenas desativado, para o histórico continuar mostrando quem atendeu.
</p>
@endsection