@extends('layouts.totem')

@section('titulo', 'Confirmar')

@section('conteudo')
    <h1>Olá, {{ \Illuminate\Support\Str::before($paciente->nome, ' ') }}!</h1>
    <p class="sub">Só mais duas confirmações antes de medir os sinais vitais.</p>

    <form method="POST" action="{{ route('triagem.confirmar.iniciar') }}" novalidate>
        @csrf

        @if ($paciente->sexo === 'F')
            <div class="campo">
                <label class="check">
                    <input type="checkbox" name="gestante" value="1" @checked(old('gestante'))>
                    Estou grávida
                </label>
            </div>
        @endif

        <div class="campo">
            <label for="telefone">Atualizar telefone com DDD (opcional)</label>
            <input type="tel" id="telefone" name="telefone" value="{{ old('telefone') }}" inputmode="numeric" placeholder="(27) 99999-9999" maxlength="15" autocomplete="off">
            <div class="aviso" style="text-align:left">Deixe em branco para manter o telefone que já temos.</div>
            @error('telefone')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="botao">Iniciar triagem</button>
    </form>

    {{-- Formulário separado: "Não sou eu" encerra a sessão --}}
    <form method="POST" action="{{ route('triagem.encerrar') }}" class="espaco-topo">
        @csrf
        <button type="submit" class="botao secundario">Não sou eu</button>
    </form>

    <script>
        const tel = document.getElementById('telefone');
        tel.addEventListener('input', () => {
            let v = tel.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            tel.value = v;
        });
    </script>
@endsection