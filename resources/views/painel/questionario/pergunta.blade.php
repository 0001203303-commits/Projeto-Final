@extends('layouts.painel')

@section('titulo', $pergunta->exists ? 'Editar pergunta' : 'Nova pergunta')

@section('conteudo')
@php $tipoAtual = old('tipo', $pergunta->tipo); @endphp

<div class="cabecalho-pagina">
    <div>
        <h1 class="titulo">{{ $pergunta->exists ? 'Editar pergunta' : 'Nova pergunta' }}</h1>
        <p class="sub">Sintoma: <strong>{{ $sintoma->nome }}</strong></p>
    </div>
</div>

<form method="POST" class="cartao form-admin" novalidate
      action="{{ $pergunta->exists ? route('painel.questionario.perguntas.update', $pergunta) : route('painel.questionario.perguntas.store') }}">
    @csrf
    @if ($pergunta->exists) @method('PUT') @else <input type="hidden" name="sintoma_id" value="{{ $sintoma->id }}"> @endif

    <div class="campo">
        <label for="texto">Pergunta</label>
        <input type="text" id="texto" name="texto" value="{{ old('texto', $pergunta->texto) }}" maxlength="200" autocomplete="off" autofocus>
        <div class="dica">Frase curta e direta, que o paciente responde com um toque.</div>
        @error('texto')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="campo">
        <label for="tipo">Tipo de resposta</label>
        <select id="tipo" name="tipo">
            <option value="sim_nao" @selected($tipoAtual === 'sim_nao')>Sim / Não</option>
            <option value="intensidade" @selected($tipoAtual === 'intensidade')>Intensidade (Leve, Moderada, Forte)</option>
        </select>
        <div class="dica">Em Intensidade, os pontos são 1, 2 ou 3 conforme a resposta. Depois que houver respostas, o tipo não pode mais mudar.</div>
        @error('tipo')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div id="bloco-sim">
        <div class="campo">
            <label for="pontos_sim">Pontos se a resposta for "Sim" (0 a 10)</label>
            <input type="number" id="pontos_sim" name="pontos_sim" value="{{ old('pontos_sim', $pergunta->pontos_sim) }}" min="0" max="10">
            @error('pontos_sim')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="cor_minima">Cor mínima se a resposta for "Sim"</label>
            <select id="cor_minima" name="cor_minima">
                <option value="">Nenhuma</option>
                <option value="amarelo" @selected(old('cor_minima', $pergunta->cor_minima) === 'amarelo')>Amarelo (urgente)</option>
                <option value="laranja" @selected(old('cor_minima', $pergunta->cor_minima) === 'laranja')>Laranja (muito urgente)</option>
            </select>
            <div class="dica">Reserve para sinais de alerta, como dor que se espalha para o braço.</div>
            @error('cor_minima')<div class="erro">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="campo">
        <label for="ordem">Ordem dentro do sintoma</label>
        <input type="number" id="ordem" name="ordem" value="{{ old('ordem', $pergunta->ordem) }}" min="0" max="99">
        @error('ordem')<div class="erro">{{ $message }}</div>@enderror
    </div>

    <div class="form-acoes">
        <button type="submit" class="btn destaque">{{ $pergunta->exists ? 'Salvar alterações' : 'Criar pergunta' }}</button>
        <a href="{{ route('painel.questionario.index', ['abrir' => $sintoma->id]) }}#sintoma-{{ $sintoma->id }}" class="btn claro">Cancelar</a>
    </div>
</form>

<script>
    const tipo = document.getElementById('tipo');
    const blocoSim = document.getElementById('bloco-sim');
    const atualizar = () => blocoSim.hidden = tipo.value === 'intensidade';
    tipo.addEventListener('change', atualizar);
    atualizar();
</script>
@endsection