@extends('layouts.painel')

@section('titulo', 'Sintomas e Perguntas')

@section('conteudo')
@php
    $abrir = (int) request('abrir');
    $limite = (int) config('triagem.limite_por_sintoma');
    $corRotulo = ['amarelo' => 'Amarelo', 'laranja' => 'Laranja'];
    $todosSintomas = $categorias->flatMap->sintomas;
    $todasPerguntas = $todosSintomas->flatMap->perguntas;
@endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">Sintomas e Perguntas</h1>
        <p class="sub">Questionário usado no totem e pontuação que alimenta a classificação de risco.</p>
    </div>
    <a href="{{ route('painel.questionario.categorias.create') }}" class="btn destaque">+ Nova categoria</a>
</div>

<div class="banner-alerta aviso-amarelo">
    <span>Estes valores definem a cor de risco do paciente. Altere com validação de um profissional de saúde. As mudanças valem para as próximas triagens, e as já feitas não são recalculadas.</span>
</div>

<div class="kpis">
    <div class="kpi"><span>Categorias ativas</span><b>{{ $categorias->where('ativo', true)->count() }}</b><small>de {{ $categorias->count() }}</small></div>
    <div class="kpi"><span>Sintomas ativos</span><b>{{ $todosSintomas->where('ativo', true)->count() }}</b><small>de {{ $todosSintomas->count() }}</small></div>
    <div class="kpi"><span>Perguntas ativas</span><b>{{ $todasPerguntas->where('ativo', true)->count() }}</b><small>de {{ $todasPerguntas->count() }}</small></div>
    <div class="kpi"><span>Com cor mínima</span><b>{{ $todosSintomas->whereNotNull('cor_minima')->count() + $todasPerguntas->whereNotNull('cor_minima')->count() }}</b><small>sintomas e perguntas</small></div>
</div>

@forelse ($categorias as $c)
    @php $categoriaUsada = $c->sintomas->contains(fn ($s) => $s->triagens_count > 0); @endphp

    <section class="cartao cat-bloco">
        <div class="cat-topo">
            <h3>
                <span>{{ $c->icone }}</span> {{ $c->nome }}
                <span class="status-tag {{ $c->ativo ? 'status-finalizada' : 'tag-inativo' }}">{{ $c->ativo ? 'Ativa' : 'Desativada' }}</span>
                <span class="meta">ordem {{ $c->ordem }}</span>
            </h3>

            <div class="acoes-linha">
                <a href="{{ route('painel.questionario.sintomas.create', ['categoria' => $c->id]) }}" class="btn destaque pequeno">+ Sintoma</a>
                <a href="{{ route('painel.questionario.categorias.edit', $c) }}" class="btn claro pequeno">Editar</a>

                <form method="POST" action="{{ route('painel.questionario.categorias.alternar', $c) }}"
                      onsubmit="return confirm('{{ $c->ativo ? 'Desativar' : 'Reativar' }} a categoria {{ e($c->nome) }}?')">
                    @csrf
                    <button type="submit" class="btn claro pequeno">{{ $c->ativo ? 'Desativar' : 'Reativar' }}</button>
                </form>

                @unless ($categoriaUsada)
                    <form method="POST" action="{{ route('painel.questionario.categorias.destroy', $c) }}"
                          onsubmit="return confirm('Excluir a categoria {{ e($c->nome) }} e todos os seus sintomas e perguntas?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn perigo pequeno">Excluir</button>
                    </form>
                @endunless
            </div>
        </div>

        @forelse ($c->sintomas as $s)
            @php
                $ativas = $s->perguntas->where('ativo', true);
                $maximo = $s->pontos_base + $ativas->sum(fn ($p) => $p->tipo === 'intensidade' ? 3 : $p->pontos_sim);
                $sintomaUsado = $s->triagens_count > 0 || $s->perguntas->sum('respostas_count') > 0;
            @endphp

            <details class="sintoma-item {{ $s->ativo ? '' : 'inativo' }}" id="sintoma-{{ $s->id }}" @if ($abrir === $s->id) open @endif>
                <summary>
                    <span class="nome">{{ $s->nome }}</span>
                    <span class="badge">base {{ $s->pontos_base }}</span>
                    @if ($s->cor_minima)<span class="cor cor-{{ $s->cor_minima }}">mín. {{ $corRotulo[$s->cor_minima] }}</span>@endif
                    <span class="badge">{{ $ativas->count() }} {{ $ativas->count() === 1 ? 'pergunta' : 'perguntas' }}</span>
                    <span class="badge {{ $maximo > $limite ? 'aviso' : '' }}" title="Base mais o máximo das perguntas ativas. O teto por sintoma é {{ $limite }}.">
                        máx. {{ min($maximo, $limite) }} pts{{ $maximo > $limite ? ' (teto)' : '' }}
                    </span>
                    @unless ($s->ativo)<span class="status-tag tag-inativo">Desativado</span>@endunless
                    @if ($s->triagens_count > 0)<span class="badge">usado em {{ $s->triagens_count }}</span>@endif
                </summary>

                <div class="sintoma-corpo">
                    <div class="acoes-linha">
                        <a href="{{ route('painel.questionario.perguntas.create', ['sintoma' => $s->id]) }}" class="btn destaque pequeno">+ Pergunta</a>
                        <a href="{{ route('painel.questionario.sintomas.edit', $s) }}" class="btn claro pequeno">Editar sintoma</a>

                        <form method="POST" action="{{ route('painel.questionario.sintomas.alternar', $s) }}"
                              onsubmit="return confirm('{{ $s->ativo ? 'Desativar' : 'Reativar' }} o sintoma {{ e($s->nome) }}?')">
                            @csrf
                            <button type="submit" class="btn claro pequeno">{{ $s->ativo ? 'Desativar' : 'Reativar' }}</button>
                        </form>

                        @unless ($sintomaUsado)
                            <form method="POST" action="{{ route('painel.questionario.sintomas.destroy', $s) }}"
                                  onsubmit="return confirm('Excluir o sintoma {{ e($s->nome) }} e suas perguntas?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn perigo pequeno">Excluir</button>
                            </form>
                        @endunless
                    </div>

                    @if ($s->perguntas->isEmpty())
                        <p class="sem-dados">Este sintoma ainda não tem perguntas.</p>
                    @else
                        <div class="tabela-wrap">
                            <table class="tabela compacta">
                                <thead><tr><th>Ordem</th><th>Pergunta</th><th>Tipo</th><th>Pontos</th><th>Cor mínima</th><th>Situação</th><th>Ações</th></tr></thead>
                                <tbody>
                                @foreach ($s->perguntas as $p)
                                    <tr class="{{ $p->ativo ? '' : 'linha-inativa' }}">
                                        <td>{{ $p->ordem }}</td>
                                        <td>{{ $p->texto }}@if ($p->respostas_count > 0)<small>respondida {{ $p->respostas_count }}×</small>@endif</td>
                                        <td>{{ $p->tipo === 'intensidade' ? 'Intensidade' : 'Sim / Não' }}</td>
                                        <td>{{ $p->tipo === 'intensidade' ? '1 a 3' : $p->pontos_sim . ' se Sim' }}</td>
                                        <td>
                                            @if ($p->cor_minima)<span class="cor cor-{{ $p->cor_minima }}">{{ $corRotulo[$p->cor_minima] }}</span>@else — @endif
                                        </td>
                                        <td><span class="status-tag {{ $p->ativo ? 'status-finalizada' : 'tag-inativo' }}">{{ $p->ativo ? 'Ativa' : 'Desativada' }}</span></td>
                                        <td>
                                            <div class="acoes-linha">
                                                <a href="{{ route('painel.questionario.perguntas.edit', $p) }}" class="btn claro pequeno">Editar</a>

                                                <form method="POST" action="{{ route('painel.questionario.perguntas.alternar', $p) }}"
                                                      onsubmit="return confirm('{{ $p->ativo ? 'Desativar' : 'Reativar' }} esta pergunta?')">
                                                    @csrf
                                                    <button type="submit" class="btn claro pequeno">{{ $p->ativo ? 'Desativar' : 'Reativar' }}</button>
                                                </form>

                                                @if ($p->respostas_count === 0)
                                                    <form method="POST" action="{{ route('painel.questionario.perguntas.destroy', $p) }}"
                                                          onsubmit="return confirm('Excluir esta pergunta definitivamente?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn perigo pequeno">Excluir</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </details>
        @empty
            <p class="sem-dados">Esta categoria ainda não tem sintomas.</p>
        @endforelse
    </section>
@empty
    <p class="vazio">Nenhuma categoria cadastrada. Clique em <strong>+ Nova categoria</strong> ou rode o <code>QuestionarioSeeder</code>.</p>
@endforelse

<p class="aviso">
    Itens já usados em triagens não podem ser excluídos, apenas desativados, para o histórico continuar legível.
    O indicador "máx." soma a pontuação base e o máximo das perguntas ativas, respeitando o teto de {{ $limite }} pontos por sintoma.
</p>
@endsection