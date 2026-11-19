@extends('layouts.totem')

@section('titulo', 'Sinais vitais')

@section('conteudo')
<div class="medicao" id="medicao" data-estado="instrucao">

    <ol class="medicao-passos" id="passos" aria-hidden="true"></ol>

    <div class="medicao-palco" aria-live="polite">
        <div class="medicao-icone" id="icone"></div>
        <h1 id="titulo"></h1>
        <p class="sub" id="texto"></p>

        <div class="medicao-barra" id="barra-wrap" hidden>
            <div class="medicao-barra-fill" id="barra"></div>
        </div>

        <div class="medicao-valor" id="valor" hidden>
            <span id="valor-numero"></span>
            <small id="valor-unidade"></small>
        </div>
    </div>

    <p class="aviso">Siga as instruções na tela. Não é preciso tocar em nada.</p>
</div>

{{-- Enviado automaticamente ao fim das 4 medições --}}
<form id="form-vitais" method="POST" action="{{ route('triagem.vitais.salvar') }}" hidden>
    @csrf
    <input type="hidden" name="temperatura" id="in-temperatura">
    <input type="hidden" name="spo2" id="in-spo2">
    <input type="hidden" name="bpm" id="in-bpm">
    <input type="hidden" name="pa_sistolica" id="in-pa_sistolica">
    <input type="hidden" name="pa_diastolica" id="in-pa_diastolica">
</form>

<script>
(function () {
    const T_INSTRUCAO = 5000, T_MEDICAO = 5000, T_CONFIRMA = 3000;
    const CRIANCA = @json($triagem->paciente->idade < config('triagem.idade_pediatrica'));

    const sorteio = (min, max) => Math.round(Math.random() * (max - min) + min);

    const svg = (conteudo) =>
        '<svg viewBox="0 0 64 64" width="96" height="96" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + conteudo + '</svg>';

    // Valores sempre dentro da faixa saudável (pontuação zero na classificação)
    const ETAPAS = [
        {
            nome: 'Temperatura',
            titulo: 'Medindo a temperatura',
            instrucao: 'Aproxime a testa do sensor de temperatura, a cerca de 3 cm, e fique parado.',
            medindo: 'Medindo a temperatura... fique parado.',
            icone: svg('<path d="M26 8a6 6 0 0 1 12 0v28a12 12 0 1 1-12 0z"/><line x1="32" y1="22" x2="32" y2="44"/>'),
            gerar: () => ({ temperatura: (sorteio(361, 372) / 10).toFixed(1) }),
            mostrar: v => ({ numero: v.temperatura.replace('.', ','), unidade: '°C' }),
        },
        {
            nome: 'Oximetria',
            titulo: 'Medindo o oxigênio no sangue',
            instrucao: 'Coloque o dedo indicador dentro do sensor de oximetria e mantenha a mão parada.',
            medindo: 'Medindo a oxigenação... mantenha o dedo no sensor.',
            icone: svg('<path d="M22 56V30a4 4 0 0 1 8 0v-8a4 4 0 0 1 8 0v8a4 4 0 0 1 8 0v14c0 8-6 12-12 12H22z"/><circle cx="14" cy="14" r="4"/><line x1="14" y1="20" x2="14" y2="26"/>'),
            gerar: () => ({ spo2: sorteio(96, 99) }),
            mostrar: v => ({ numero: v.spo2, unidade: '% SpO₂' }),
        },
        {
            nome: 'Batimento cardíaco',
            titulo: 'Medindo os batimentos do coração',
            instrucao: 'Mantenha o dedo no sensor e respire normalmente.',
            medindo: 'Medindo os batimentos... respire normalmente.',
            icone: svg('<path d="M32 54S8 40 8 24a12 12 0 0 1 24-4 12 12 0 0 1 24 4c0 16-24 30-24 30z"/><polyline points="14 30 24 30 28 22 34 38 38 30 50 30"/>'),
            gerar: () => ({ bpm: CRIANCA ? sorteio(75, 105) : sorteio(60, 90) }),
            mostrar: v => ({ numero: v.bpm, unidade: 'bpm' }),
        },
        {
            nome: 'Pressão arterial',
            titulo: 'Medindo a pressão arterial',
            instrucao: 'Coloque o braço no apoio, com a braçadeira na altura do coração. Fique em silêncio.',
            medindo: 'Medindo a pressão... não se mexa nem fale.',
            icone: svg('<rect x="10" y="22" width="44" height="20" rx="6"/><path d="M54 32h4a4 4 0 0 1 0 8h-4"/><line x1="22" y1="22" x2="22" y2="42"/><line x1="32" y1="22" x2="32" y2="42"/>'),
            gerar: () => ({ pa_sistolica: sorteio(112, 128), pa_diastolica: sorteio(68, 82) }),
            mostrar: v => ({ numero: v.pa_sistolica + '/' + v.pa_diastolica, unidade: 'mmHg' }),
        },
    ];

    const $ = id => document.getElementById(id);
    const raiz = $('medicao');
    const medidos = {};
    let enviado = false;

    // Indicador de etapas (só visual)
    ETAPAS.forEach(e => {
        const li = document.createElement('li');
        li.textContent = e.nome;
        $('passos').appendChild(li);
    });
    const marcadores = [...$('passos').children];

    function estado(nome) {
        raiz.dataset.estado = nome;
        $('barra-wrap').hidden = nome !== 'medindo';
        $('valor').hidden = nome !== 'confirmacao';
    }

    function animarBarra(ms) {
        const b = $('barra');
        b.style.transition = 'none';
        b.style.width = '0%';
        void b.offsetWidth; // reinicia a animação
        b.style.transition = 'width ' + ms + 'ms linear';
        b.style.width = '100%';
    }

    function etapa(i) {
        if (i >= ETAPAS.length) return concluir();

        const e = ETAPAS[i];
        marcadores.forEach((m, k) => m.className = k < i ? 'feito' : (k === i ? 'atual' : ''));

        // 1) Instrução
        $('icone').innerHTML = e.icone;
        $('titulo').textContent = e.nome;
        $('texto').textContent = e.instrucao;
        estado('instrucao');

        setTimeout(() => {
            // 2) Medição simulada
            $('titulo').textContent = e.titulo;
            $('texto').textContent = e.medindo;
            estado('medindo');
            animarBarra(T_MEDICAO);

            setTimeout(() => {
                // 3) Confirmação
                const v = e.gerar();
                Object.assign(medidos, v);
                const m = e.mostrar(v);

                $('titulo').textContent = e.nome + ' medida';
                $('texto').textContent = 'Medição concluída.';
                $('valor-numero').textContent = m.numero;
                $('valor-unidade').textContent = m.unidade;
                estado('confirmacao');

                setTimeout(() => etapa(i + 1), T_CONFIRMA);
            }, T_MEDICAO);
        }, T_INSTRUCAO);
    }

    function concluir() {
        if (enviado) return;
        enviado = true;

        marcadores.forEach(m => m.className = 'feito');
        $('icone').innerHTML = '<span style="font-size:4.5rem;line-height:1">✓</span>';
        $('titulo').textContent = 'Medições concluídas!';
        $('texto').textContent = 'Agora vamos fazer algumas perguntas sobre como você está se sentindo.';
        estado('fim');

        ['temperatura', 'spo2', 'bpm', 'pa_sistolica', 'pa_diastolica'].forEach(k => $('in-' + k).value = medidos[k]);
        setTimeout(() => $('form-vitais').submit(), 1500);
    }

    etapa(0);
})();
</script>
@endsection