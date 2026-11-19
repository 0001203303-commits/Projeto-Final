<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Triagem;
use App\Rules\CpfValido;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;

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

        // ================= Entrada de quem já tem cadastro =================

    public function entrar()
    {
        return view('totem.entrar');
    }

    public function autenticar(Request $request)
    {
        $dados = $request->validate([
            'cpf'             => ['required', new CpfValido],
            'data_nascimento' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after:1900-01-01'],
        ], [
            'data_nascimento.*' => 'Informe uma data de nascimento válida.',
        ]);

        $cpf = preg_replace('/\D/', '', $dados['cpf']);
        $chaves = $this->chavesLimite($request, $cpf);

        if (($min = $this->bloqueado($chaves)) !== null) {
            return back()->withInput($request->only('cpf'))->withErrors([
                'cpf' => "Muitas tentativas. Procure a recepção ou tente novamente em {$min} minuto(s).",
            ]);
        }

        $paciente = Paciente::where('cpf', $cpf)->first();

        // Mesma mensagem para "não existe" e "data errada": não revela quais CPFs estão cadastrados
        if (! $paciente || $paciente->data_nascimento->toDateString() !== $dados['data_nascimento']) {
            $this->registrarFalha($chaves);

            return back()->withInput($request->only('cpf'))->withErrors([
                'cpf' => 'Não encontramos um cadastro com estes dados. Confira o CPF e a data de nascimento, ou faça o primeiro cadastro.',
            ]);
        }

        RateLimiter::clear($chaves['ip']);

        $request->session()->regenerate();
        $request->session()->forget('triagem_id');
        $request->session()->put('paciente_id', $paciente->id);

        return redirect()->route('triagem.confirmar');
    }

    public function confirmar(Request $request)
    {
        $paciente = $this->pacienteDaSessao($request);

        if (! $paciente) {
            return redirect()->route('triagem.entrar');
        }

        return view('totem.confirmar', compact('paciente'));
    }

    public function iniciar(Request $request)
    {
        $paciente = $this->pacienteDaSessao($request);

        if (! $paciente) {
            return redirect()->route('triagem.entrar');
        }

        $dados = $request->validate([
            'telefone' => ['nullable', 'regex:/^\(?\d{2}\)?\s?9?\d{4}-?\d{4}$/'],
            'gestante' => ['nullable', 'boolean'],
        ], [
            'telefone.regex' => 'Informe um telefone válido com DDD.',
        ]);

        // Telefone em branco mantém o que já está cadastrado
        if (! empty($dados['telefone'])) {
            $paciente->telefone = $dados['telefone'];
        }

        $paciente->gestante = $paciente->sexo === 'F' && $request->boolean('gestante');
        $paciente->save();

        return $this->abrirTriagem($request, $paciente);
    }

    // ================= Auxiliares =================

    private function abrirTriagem(Request $request, Paciente $paciente)
    {
        $triagem = Triagem::create([
            'paciente_id' => $paciente->id,
            'status'      => 'aguardando',
        ]);

        $request->session()->forget('paciente_id');
        $request->session()->put('triagem_id', $triagem->id);

        return redirect()->route('triagem.vitais');
    }

    private function pacienteDaSessao(Request $request): ?Paciente
    {
        $id = $request->session()->get('paciente_id');

        return $id ? Paciente::find($id) : null;
    }

    /** Limites por CPF a partir deste aparelho (5 em 5 min) e por CPF no total (15 em 1 h). */
    private function chavesLimite(Request $request, string $cpf): array
    {
        return [
            'ip'  => 'totem-id:' . sha1($request->ip() . '|' . $cpf),
            'cpf' => 'totem-id-cpf:' . sha1($cpf),
        ];
    }

    /** Devolve os minutos de espera se estiver bloqueado, ou null se puder tentar. */
    private function bloqueado(array $chaves): ?int
    {
        if (RateLimiter::tooManyAttempts($chaves['ip'], 5) || RateLimiter::tooManyAttempts($chaves['cpf'], 15)) {
            $segundos = max(RateLimiter::availableIn($chaves['ip']), RateLimiter::availableIn($chaves['cpf']));

            return max(1, (int) ceil($segundos / 60));
        }

        return null;
    }

    private function registrarFalha(array $chaves): void
    {
        RateLimiter::hit($chaves['ip'], 300);
        RateLimiter::hit($chaves['cpf'], 3600);
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
        $nascimento = Carbon::parse($dados['data_nascimento'])->toDateString();

        $paciente = Paciente::where('cpf', $cpf)->first();

        // CPF já cadastrado: só continua quem comprova a data de nascimento
        if ($paciente) {
            $chaves = $this->chavesLimite($request, $cpf);

            if (($min = $this->bloqueado($chaves)) !== null) {
                return back()->withInput()->withErrors(['cpf' => "Muitas tentativas. Procure a recepção ou tente novamente em {$min} minuto(s)."]);
            }

            if ($paciente->data_nascimento->toDateString() !== $nascimento) {
                $this->registrarFalha($chaves);

                return back()->withInput()->withErrors([
                    'cpf' => 'Não foi possível concluir o cadastro com estes dados. Se você já tem cadastro, use "Já tenho cadastro".',
                ]);
            }
        }

        $paciente ??= new Paciente();
        $paciente->fill([
            'nome'            => $dados['nome'],
            'cpf'             => $cpf,
            'data_nascimento' => $nascimento,
            'sexo'            => $dados['sexo'],
            'telefone'        => $dados['telefone'] ?? null,
            'gestante'        => $dados['sexo'] === 'F' && $request->boolean('gestante'),
        ])->save();

        $request->session()->regenerate();

        return $this->abrirTriagem($request, $paciente);
    }
          public function vitais(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem) {
            return redirect()->route('triagem.inicio');
        }

        $modo = config('triagem.vitais_modo', 'simulado');

        // Na simulação, quem já foi "medido" não refaz as 4 etapas (o botão Voltar do navegador, por exemplo)
        if ($modo === 'simulado' && $triagem->bpm !== null) {
            return redirect()->route('triagem.sintomas');
        }

        return view($modo === 'manual' ? 'totem.vitais_manual' : 'totem.vitais', compact('triagem'));
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