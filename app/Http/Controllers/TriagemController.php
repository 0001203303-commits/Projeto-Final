<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Triagem;
use App\Rules\CpfValido;
use Illuminate\Http\Request;

class TriagemController extends Controller
{
    public function inicio()
    {
        return view('totem.inicio');
    }

    public function dados()
    {
        return view('totem.dados');
    }

    public function salvarDados(Request $request)
    {
        $dados = $request->validate([
            'nome'            => ['required', 'string', 'min:5', 'max:150'],
            'cpf'             => ['required', new CpfValido],
            'data_nascimento' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'sexo'            => ['required', 'in:M,F,O'],
            'telefone'        => ['nullable', 'regex:/^\(?\d{2}\)?\s?9?\d{4}-?\d{4}$/'],
            'gestante'        => ['nullable', 'boolean'],
        ], [
            'nome.min'            => 'Digite o nome completo.',
            'data_nascimento.*'   => 'Informe uma data de nascimento válida.',
            'telefone.regex'      => 'Informe um telefone válido com DDD.',
        ]);

        $cpf = preg_replace('/\D/', '', $dados['cpf']);

        // Paciente que já veio antes é reaproveitado e atualizado
        $paciente = Paciente::firstOrNew(['cpf' => $cpf]);
        $paciente->fill([
            'nome'            => $dados['nome'],
            'cpf'             => $cpf,
            'data_nascimento' => $dados['data_nascimento'],
            'sexo'            => $dados['sexo'],
            'telefone'        => $dados['telefone'] ?? null,
            'gestante'        => $dados['sexo'] === 'F' && $request->boolean('gestante'),
        ])->save();

        $triagem = Triagem::create([
            'paciente_id' => $paciente->id,
            'status'      => 'aguardando',
        ]);

        // O totem guarda a triagem em sessão, em vez de expor o ID na URL
        $request->session()->put('triagem_id', $triagem->id);

        return redirect()->route('triagem.vitais');
    }

        public function vitais(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem) {
            return redirect()->route('triagem.inicio');
        }

        return view('totem.vitais', compact('triagem'));
    }

    public function salvarVitais(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem) {
            return redirect()->route('triagem.inicio');
        }

        $dados = $request->validate([
            'bpm'            => ['required', 'integer', 'between:30,220'],
            'spo2'           => ['required', 'integer', 'between:50,100'],
            'pa_sistolica'   => ['required', 'integer', 'between:50,260'],
            'pa_diastolica'  => ['required', 'integer', 'between:30,160', 'lt:pa_sistolica'],
            'temperatura'    => ['required', 'numeric', 'between:33,43'],
        ], [
            'pa_diastolica.lt' => 'A pressão diastólica deve ser menor que a sistólica.',
            '*.required'       => 'Este valor é obrigatório.',
            '*.between'        => 'Valor fora da faixa permitida.',
        ]);

        $triagem->update($dados);

        return redirect()->route('triagem.sintomas');
    }



    private function triagemAtual(Request $request): ?Triagem
    {
        $id = $request->session()->get('triagem_id');

        return $id ? Triagem::with('paciente')->find($id) : null;
    }
}
?>