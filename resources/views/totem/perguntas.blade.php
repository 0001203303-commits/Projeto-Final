@extends('layouts.totem')

@section('titulo', 'Perguntas')

@section('conteudo')
    <div class="etapas">
        <div class="etapa ativa"></div>
        <div class="etapa ativa"></div>
        <div class="etapa ativa"></div>
    </div>

    @error('respostas')
        <div class="erro-geral">{{ $message }}</div>
    @enderror

    <div class="progresso">
        <div id="barra"></div>
    </div>
    <div class="contador" id="contador"></div>

    <span class="tag-sintoma" id="sintoma"></span>
    <h1 id="texto"></h1>

    <div class="respostas-grandes" id="opcoes"></div>

    <div class="botoes">
        <button type="button" class="botao secundario" id="voltar">Voltar</button>
    </div>

    <form id="form" method="POST" action="{{ route('triagem.perguntas.salvar') }}">
        @csrf
        <div id="campos"></div>
    </form>

    <script>
        const perguntas = @json($perguntas);
        const respostas = Object.assign({}, @json($respondidas));
        const urlSintomas = @json(route('triagem.sintomas'));
        let atual = 0;

        const opcoesPorTipo = {
            sim_nao: [{ rotulo: 'Sim', valor: 1 }, { rotulo: 'Não', valor: 0 }],
            intensidade: [
                { rotulo: 'Leve', valor: 1 },
                { rotulo: 'Moderada', valor: 2 },
                { rotulo: 'Forte', valor: 3 },
            ],
        };

        function mostrar() {
            const p = perguntas[atual];
            document.getElementById('sintoma').textContent = p.sintoma;
            document.getElementById('texto').textContent = p.texto;
            document.getElementById('contador').textContent = 'Pergunta ' + (atual + 1) + ' de ' + perguntas.length;
            document.getElementById('barra').style.width = (atual / perguntas.length * 100) + '%';

            const box = document.getElementById('opcoes');
            box.innerHTML = '';
            opcoesPorTipo[p.tipo].forEach(op => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'resposta' + (respostas[p.id] === op.valor ? ' escolhida' : '');
                b.textContent = op.rotulo;
                b.addEventListener('click', () => responder(p.id, op.valor));
                box.appendChild(b);
            });
        }

        function responder(id, valor) {
            respostas[id] = valor;
            if (atual === perguntas.length - 1) {
                enviar();
            } else {
                atual++;
                mostrar();
            }
        }

        function enviar() {
            const campos = document.getElementById('campos');
            campos.innerHTML = '';
            perguntas.forEach(p => {
                const i = document.createElement('input');
                i.type = 'hidden';
                i.name = 'respostas[' + p.id + ']';
                i.value = respostas[p.id];
                campos.appendChild(i);
            });
            document.getElementById('form').submit();
        }

        document.getElementById('voltar').addEventListener('click', () => {
            if (atual === 0) { window.location.href = urlSintomas; return; }
            atual--;
            mostrar();
        });

        mostrar();
    </script>
@endsection