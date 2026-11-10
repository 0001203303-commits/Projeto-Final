@php
    $rotulos = ['online' => 'Online', 'offline' => 'Offline', 'nunca' => 'Nunca conectou', 'inativo' => 'Desativado'];
    $estados = $totens->groupBy('status_conexao');
@endphp

<div class="kpis">
    <div class="kpi"><span>Totens cadastrados</span><b>{{ $totens->count() }}</b><small>{{ $totens->where('ativo', false)->count() }} desativados</small></div>
    <div class="kpi bom"><span>Online</span><b>{{ $estados->get('online', collect())->count() }}</b><small>comunicando agora</small></div>
    <div class="kpi {{ $estados->get('offline', collect())->count() > 0 ? 'ruim' : '' }}"><span>Offline</span><b>{{ $estados->get('offline', collect())->count() }}</b><small>sem contato recente</small></div>
    <div class="kpi"><span>Nunca conectaram</span><b>{{ $estados->get('nunca', collect())->count() }}</b><small>aguardando configuração</small></div>
</div>

@if ($totens->isEmpty())
    <p class="vazio">Nenhum totem cadastrado. Clique em <strong>+ Novo totem</strong> para começar.</p>
@else
    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr><th>Status</th><th>Totem</th><th>Local</th><th>Token</th><th>Último contato</th><th>Versão / IP</th><th>Ações</th></tr>
            </thead>
            <tbody>
            @foreach ($totens as $t)
                @php $st = $t->status_conexao; @endphp
                <tr class="{{ $t->ativo ? '' : 'linha-inativa' }}">
                    <td><span class="conexao {{ $st }}"><i></i>{{ $rotulos[$st] }}</span></td>
                    <td>{{ $t->nome }}@if ($t->observacoes)<small>{{ \Illuminate\Support\Str::limit($t->observacoes, 60) }}</small>@endif</td>
                    <td>{{ $t->local ?: '—' }}</td>
                    <td class="mono">{{ $t->token_prefixo }}…</td>
                    <td>
                        @if ($t->ultimo_contato_em)
                            {{ $t->ultimo_contato_em->diffForHumans() }}
                            <small>{{ $t->ultimo_contato_em->format('d/m/Y H:i:s') }}</small>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $t->versao ?: '—' }}<small>{{ $t->ultimo_ip ?: '—' }}</small></td>
                    <td>
                        <div class="acoes-linha">
                            <a href="{{ route('painel.totens.edit', $t) }}" class="btn claro pequeno">Editar</a>

                            <form method="POST" action="{{ route('painel.totens.token', $t) }}"
                                  onsubmit="return confirm('Gerar um novo token para {{ e($t->nome) }}? O token atual deixará de funcionar imediatamente.')">
                                @csrf
                                <button type="submit" class="btn claro pequeno">Novo token</button>
                            </form>

                            <form method="POST" action="{{ route('painel.totens.alternar', $t) }}"
                                  onsubmit="return confirm('{{ $t->ativo ? 'Desativar' : 'Reativar' }} o totem {{ e($t->nome) }}?')">
                                @csrf
                                <button type="submit" class="btn claro pequeno">{{ $t->ativo ? 'Desativar' : 'Reativar' }}</button>
                            </form>

                            <form method="POST" action="{{ route('painel.totens.destroy', $t) }}"
                                  onsubmit="return confirm('Excluir o totem {{ e($t->nome) }} definitivamente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn perigo pequeno">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif