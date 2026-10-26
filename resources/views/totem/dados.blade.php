@extends('layouts.totem')

@section('titulo', 'Seus dados')

@section('conteudo')
    <div class="etapas">
        <div class="etapa ativa"></div><div class="etapa"></div><div class="etapa"></div>
    </div>

    <h1>Seus dados</h1>
    <p class="sub">Preencha as informações abaixo.</p>

    <form method="POST" action="{{ route('triagem.dados.salvar') }}" novalidate>
        @csrf

        <div class="campo">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" value="{{ old('nome') }}" autocomplete="off">
            @error('nome')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" inputmode="numeric" maxlength="14" placeholder="000.000.000-00" autocomplete="off">
            @error('cpf')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label for="data_nascimento">Data de nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" max="{{ now()->toDateString() }}">
            @error('data_nascimento')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo">
            <label>Sexo</label>
            <div class="opcoes">
                <label><input type="radio" name="sexo" value="M" @checked(old('sexo') === 'M')><span>Masculino</span></label>
                <label><input type="radio" name="sexo" value="F" @checked(old('sexo') === 'F')><span>Feminino</span></label>
                <label><input type="radio" name="sexo" value="O" @checked(old('sexo') === 'O')><span>Outro</span></label>
            </div>
            @error('sexo')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <div class="campo" id="bloco-gestante" style="display:none">
            <label class="check">
                <input type="checkbox" name="gestante" value="1" @checked(old('gestante'))>
                Estou grávida
            </label>
        </div>

        <div class="campo">
            <label for="telefone">Telefone com DDD (opcional)</label>
            <input type="tel" id="telefone" name="telefone" value="{{ old('telefone') }}" inputmode="numeric" placeholder="(27) 99999-9999" maxlength="15">
            @error('telefone')<div class="erro">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="botao">Continuar</button>
    </form>

    <script>
        const cpf = document.getElementById('cpf');
        cpf.addEventListener('input', () => {
            let v = cpf.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpf.value = v;
        });

        const tel = document.getElementById('telefone');
        tel.addEventListener('input', () => {
            let v = tel.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            tel.value = v;
        });

        // Mostra "grávida" só para o sexo feminino
        const blocoGestante = document.getElementById('bloco-gestante');
        const atualizarGestante = () => {
            const f = document.querySelector('input[name=sexo][value=F]');
            blocoGestante.style.display = f && f.checked ? 'block' : 'none';
        };
        document.querySelectorAll('input[name=sexo]').forEach(r => r.addEventListener('change', atualizarGestante));
        atualizarGestante();
    </script>
@endsection