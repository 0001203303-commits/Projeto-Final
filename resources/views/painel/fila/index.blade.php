@extends('layouts.painel')

@section('titulo', 'Fila de Atendimento')

@section('conteudo')
@php $cores = config('triagem.cores'); @endphp

<h1 class="titulo">Fila de Atendimento</h1>
<p class="sub">Ordenada por gravidade e, dentro de cada cor, por ordem de chegada.</p>

<div class="fila-barra">
    <div class="abas-status">
        <button type="button" class="aba-status ativa" data-status="todas">Todas <span class="n">0</span></button>
        <button type="button" class="aba-status" data-status="aguardando">Aguardando <span class="n">0</span></button>
        <button type="button" class="aba-status" data-status="em_atendimento">Em atendimento <span class="n">0</span></button>
        <button type="button" class="aba-status" data-status="finalizada">Atendidos <span class="n">0</span></button>
    </div>

    <div class="campo-busca">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" id="busca" placeholder="Buscar paciente ou queixa..." autocomplete="off">
    </div>
</div>

<div class="filtro-gravidade">
    <span class="rotulo">Gravidade:</span>
    @foreach (['laranja', 'amarelo', 'verde', 'azul'] as $c)
        <button type="button" class="selo-cor chip-cor tom-{{ $c }}" data-cor="{{ $c }}" title="Filtrar por {{ $cores[$c]['descricao'] }}">
            <i></i>{{ $cores[$c]['descricao'] }} <span class="n">0</span>
        </button>
    @endforeach

    <div class="direita">
        <label class="check-simples"><input type="checkbox" id="meus"> Só meus atendimentos</label>
        <span class="atualizado" id="atualizado">atualizado agora</span>
    </div>
</div>

<div class="offline" id="offline" hidden>
    Sem conexão com o servidor. A lista abaixo pode estar desatualizada; tentando reconectar...
</div>

<div id="conteudo">
    @include('painel.fila._lista')
</div>

<script>
    const URL_ATUALIZAR = @json(route('painel.fila.parcial'));
    const MEU_ID = @json((string) auth()->id());

    const conteudo = document.getElementById('conteudo');
    const estado = { status: 'todas', cor: null, busca: '', meus: false };
    let ultima = Date.now();
    let falhas = 0;

    try {
        const salvo = localStorage.getItem('statusFila');
        if (salvo) estado.status = salvo;
    } catch (e) {}

    const normalizar = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();

    // "ignorar" permite contar cada aba/chip sem aplicar o próprio filtro
    function passa(card, ignorar) {
        if (ignorar !== 'status' && estado.status !== 'todas' && card.dataset.status !== estado.status) return false;
        if (ignorar !== 'cor' && estado.cor && card.dataset.cor !== estado.cor) return false;
        if (estado.busca && !card.dataset.busca.includes(estado.busca)) return false;
        if (estado.meus && card.dataset.atendente && card.dataset.atendente !== MEU_ID) return false;
        return true;
    }

    function aplicarFiltros() {
        const cards = [...conteudo.querySelectorAll('.card-paciente')];
        let visiveis = 0;

        cards.forEach(c => {
            const ok = passa(c);
            c.hidden = !ok;
            if (ok) visiveis++;
        });

        document.querySelectorAll('.aba-status').forEach(b => {
            const st = b.dataset.status;
            b.classList.toggle('ativa', st === estado.status);
            b.querySelector('.n').textContent = cards.filter(c =>
                passa(c, 'status') && (st === 'todas' || c.dataset.status === st)).length;
        });

        document.querySelectorAll('.chip-cor').forEach(b => {
            b.classList.toggle('ativo', b.dataset.cor === estado.cor);
            b.querySelector('.n').textContent = cards.filter(c =>
                passa(c, 'cor') && c.dataset.cor === b.dataset.cor).length;
        });

        const aviso = document.getElementById('sem-resultado');
        if (aviso) aviso.hidden = visiveis > 0 || cards.length === 0;
    }

    document.querySelectorAll('.aba-status').forEach(b => b.addEventListener('click', () => {
        estado.status = b.dataset.status;
        try { localStorage.setItem('statusFila', estado.status); } catch (e) {}
        aplicarFiltros();
    }));

    // Clicar de novo no chip limpa o filtro
    document.querySelectorAll('.chip-cor').forEach(b => b.addEventListener('click', () => {
        estado.cor = estado.cor === b.dataset.cor ? null : b.dataset.cor;
        aplicarFiltros();
    }));

    document.getElementById('busca').addEventListener('input', e => {
        estado.busca = normalizar(e.target.value);
        aplicarFiltros();
    });

    document.getElementById('meus').addEventListener('change', e => {
        estado.meus = e.target.checked;
        aplicarFiltros();
    });

    async function atualizar() {
        try {
            const r = await fetch(URL_ATUALIZAR, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            if (r.redirected || r.status === 401 || r.status === 419) { location.reload(); return; }
            if (!r.ok) throw new Error(r.status);

            conteudo.innerHTML = await r.text();
            aplicarFiltros();
            ultima = Date.now();
            falhas = 0;
            document.getElementById('offline').hidden = true;
        } catch (e) {
            if (++falhas >= 2) document.getElementById('offline').hidden = false;
        }
    }

    setInterval(atualizar, 10000);

    setInterval(() => {
        const s = Math.round((Date.now() - ultima) / 1000);
        document.getElementById('atualizado').textContent = s < 5 ? 'atualizado agora' : 'atualizado há ' + s + ' s';
    }, 1000);

    aplicarFiltros();
</script>
@endsection