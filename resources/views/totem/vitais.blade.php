@extends('layouts.totem')

@section('titulo', 'Sinais vitais')

@section('conteudo')
    <div class="etapas">
        <div class="etapa ativa"></div><div class="etapa ativa"></div><div class="etapa"></div>
    </div>

    <h1>Sinais vitais</h1>
    <p class="sub">Olá, {{ \Illuminate\Support\Str::before($triagem->paciente->nome, ' ') }}! Coloque o dedo no oxímetro e aguarde a medição.</p>

    <div class="simulador-aviso">
        <strong>Modo simulação:</strong> sem os sensores físicos, ajuste os valores abaixo ou escolha um cenário pronto.
    </div>

    <div class="cenarios">
        <button type="button" class="cenario" data-v="80,98,120,80,36.5">Normal</button>
        <button type="button" class="cenario" data-v="100,97,118,76,38.6">Febre</button>
        <button type="button" class="cenario" data-v="110,88,130,85,36.8">Oxigenação baixa</button>
        <button type="button" class="cenario" data-v="90,97,180,110,36.6">Pressão alta</button>
        <button type="button" class="cenario" data-v="105,95,85,55,36.2">Pressão baixa</button>
        <button type="button" class="cenario" data-v="135,96,125,80,36.7">Coração acelerado</button>
    </div>

    <form method="POST" action="{{ route('triagem.vitais.salvar') }}" novalidate>
        @csrf

        <div class="vital">
            <div class="vital-topo">
                <span class="vital-nome">❤️ Batimentos cardíacos</span>
                <span class="vital-valor"><span id="out-bpm">{{ old('bpm', 80) }}</span><small>bpm</small></span>
            </div>
            <input type="range" id="bpm" name="bpm" min="30" max="200" step="1" value="{{ old('bpm', 80) }}">
            @error('bpm')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="vital">
            <div class="vital-topo">
                <span class="vital-nome">🫁 Saturação de oxigênio (SpO₂)</span>
                <span class="vital-valor"><span id="out-spo2">{{ old('spo2', 98) }}</span><small>%</small></span>
            </div>
            <input type="range" id="spo2" name="spo2" min="70" max="100" step="1" value="{{ old('spo2', 98) }}">
            @error('spo2')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="grupo-pa">
            <div class="vital">
                <div class="vital-topo">
                    <span class="vital-nome">🩺 Pressão (máxima)</span>
                    <span class="vital-valor"><span id="out-pa_sistolica">{{ old('pa_sistolica', 120) }}</span><small>mmHg</small></span>
                </div>
                <input type="range" id="pa_sistolica" name="pa_sistolica" min="60" max="240" step="1" value="{{ old('pa_sistolica', 120) }}">
                @error('pa_sistolica')<div class="erro">{{ $message }}</div>@enderror
            </div>

            <div class="vital">
                <div class="vital-topo">
                    <span class="vital-nome">🩺 Pressão (mínima)</span>
                    <span class="vital-valor"><span id="out-pa_diastolica">{{ old('pa_diastolica', 80) }}</span><small>mmHg</small></span>
                </div>
                <input type="range" id="pa_diastolica" name="pa_diastolica" min="40" max="140" step="1" value="{{ old('pa_diastolica', 80) }}">
                @error('pa_diastolica')<div class="erro">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="vital">
            <div class="vital-topo">
                <span class="vital-nome">🌡️ Temperatura</span>
                <span class="vital-valor"><span id="out-temperatura">{{ number_format((float) old('temperatura', 36.5), 1, '.', '') }}</span><small>°C</small></span>
            </div>
            <input type="range" id="temperatura" name="temperatura" min="34" max="42" step="0.1" value="{{ old('temperatura', 36.5) }}">
            @error('temperatura')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="botoes">
            <a href="{{ route('triagem.dados') }}" class="botao secundario">Voltar</a>
            <button type="submit" class="botao">Continuar</button>
        </div>
    </form>

    <script>
        const campos = ['bpm', 'spo2', 'pa_sistolica', 'pa_diastolica', 'temperatura'];

        function atualizar(id) {
            const el = document.getElementById(id);
            const out = document.getElementById('out-' + id);
            out.textContent = id === 'temperatura' ? parseFloat(el.value).toFixed(1) : el.value;
        }

        campos.forEach(id => {
            document.getElementById(id).addEventListener('input', () => atualizar(id));
        });

        document.querySelectorAll('.cenario').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.dataset.v.split(',').forEach((valor, i) => {
                    document.getElementById(campos[i]).value = valor;
                    atualizar(campos[i]);
                });
            });
        });
    </script>
@endsection