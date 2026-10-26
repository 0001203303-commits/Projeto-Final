@extends('layouts.totem')

@section('titulo', 'Sintomas')

@section('conteudo')
    <div class="etapas">
        <div class="etapa ativa"></div><div class="etapa ativa"></div><div class="etapa ativa"></div>
    </div>

    <h1>O que está sentindo?</h1>
    <p class="sub">Abra quantas categorias precisar e marque todos os sintomas. Você pode escolher sintomas de várias categorias ao mesmo tempo.</p>

    @error('sintomas')
        <div class="erro-geral">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('triagem.sintomas.salvar') }}" novalidate>
        @csrf

        @foreach ($categorias as $categoria)
            @php $temMarcado = $categoria->sintomas->pluck('id')->intersect($marcados)->isNotEmpty(); @endphp
            <details class="categoria" @if($temMarcado) open @endif>
                <summary>
                    <span>{{ $categoria->icone }}</span> {{ $categoria->nome }}
                    <span class="contagem"></span>
                </summary>
                <div class="sintomas-lista">
                    @foreach ($categoria->sintomas as $sintoma)
                        <label class="chip">
                            <input type="checkbox" name="sintomas[]" value="{{ $sintoma->id }}" @checked(in_array($sintoma->id, $marcados))>
                            <span>{{ $sintoma->nome }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        @endforeach

        <div class="botoes">
            <a href="{{ route('triagem.vitais') }}" class="botao secundario">Voltar</a>
            <button type="submit" class="botao">Continuar</button>
        </div>
    </form>

    <script>
        function atualizarContagens() {
            document.querySelectorAll('.categoria').forEach(cat => {
                const n = cat.querySelectorAll('input:checked').length;
                const badge = cat.querySelector('.contagem');
                badge.textContent = n;
                badge.classList.toggle('visivel', n > 0);
            });
        }
        document.querySelectorAll('.chip input').forEach(i => i.addEventListener('change', atualizarContagens));
        atualizarContagens();
    </script>
@endsection