<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PacientesController extends Controller
{
   
    public function index($id = null)
    {
        $Pacientes = Pacientes::get();
        if($id!= null){
            $paciente = Pacientes::findOrFail($id);
        }else{
            $paciente = null;
        }
        return view('admin.pacientes', compact('Pacientes', 'paciente'));
    }

    public function salvar(Request $request)
    {
        if ($request->id) {
            $paciente = Pacientes::findOrFail($request->id);
        } 
        elseif ($request->cpf && $pacienteExistente = Pacientes::where('cpf', $request->cpf)->first()) {
            $paciente = $pacienteExistente;
        } 
        else {
            $paciente = new Pacientes();
        }

        // Dados básicos identificados
        $paciente->nome = $request->nome ?? $paciente->nome;
        $paciente->cpf = $request->cpf ?? $paciente->cpf;
        $paciente->idade = $request->idade ?? $paciente->idade;
        $paciente->horario = $request->horario ?? $paciente->horario ?? now()->format('H:i');
        //$paciente->status = $paciente->status ?? 1;
        
        $paciente->tipo_sanguineo = $request->tipo_sanguineo ?? $paciente->tipo_sanguineo;
        $paciente->data_nascimento = $request->data_nascimento ?? $paciente->data_nascimento;
        $paciente->sexo = $request->sexo ?? $paciente->sexo;
        $paciente->telefone = $request->telefone ?? $paciente->telefone;
        $paciente->endereco = $request->endereco ?? $paciente->endereco;
        $paciente->email = $request->email ?? $paciente->email;
        $paciente->antecedentes_pessoais = $request->antecedentes_pessoais ?? $paciente->antecedentes_pessoais;
        
        // Captura as respostas de triagem de forma segura com acentuação correta
        if ($request->has('respostas_quiz')) {
                $respostasConfirmadas = array_values(array_filter(
                    $request->respostas_quiz,
                    fn ($resposta) => strtolower(trim($resposta['resposta'] ?? '')) === 'sim'
                ));

                $paciente->sintomas = json_encode($respostasConfirmadas, JSON_UNESCAPED_UNICODE);
        } else {
            $paciente->sintomas = $request->sintoma ?? $request->sintomas ?? $paciente->sintomas ?? null;
        }

        // Calcula a urgência (Protocolo de Manchester) com base no score enviado pelo JS
        $score = $request->input('score_final');
        if ($score !== null) {
            $classificacao = "NÃO URGENTE (AZUL)";
            $corHex = "#3b82f6";

            if ($score >= 11) {
                $classificacao = "EMERGÊNCIA (VERMELHO)";
                $corHex = "#ef4444";
            } elseif ($score >= 8) {
                $classificacao = "MUITO URGENTE (LARANJA)";
                $corHex = "#f97316";
            } elseif ($score >= 5) {
                $classificacao = "URGENTE (AMARELO)";
                $corHex = "#eab308";
            } elseif ($score >= 3) {
                $classificacao = "POUCO URGENTE (VERDE)";
                $corHex = "#10b981";
            }

            $paciente->urgencia = $classificacao;
        } else {
            $paciente->urgencia = $request->urgencia ?? $paciente->urgencia;
        }

        if ($request->protocolo) {
            $paciente->protocolo = $request->protocolo;
        }

        if (!$paciente->protocolo) {
            $paciente->protocolo = 'TR-' . Str::upper(Str::random(13));
        }

        $paciente->save();

        if ($request->expectsJson() || $request->isJson()|| $request->ajax()) {
            return response()->json([
                'success' => true,
                'classificacao' => $paciente->urgencia,
                'cor_hex' => $corHex ?? '#3b82f6',
                'protocolo' => $paciente->protocolo,
                'message' => 'Paciente processado com sucesso!'
            ]);
        }

        return redirect("/admin/pacientes")->with('success', 'Paciente processado com sucesso!');
    }

    public function atualizar(Request $request, $id)
    {
        $paciente = Pacientes::findOrFail($id);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:255'],
            'protocolo' => ['required', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:255'],
            'data_nascimento' => ['nullable', 'string', 'max:255'],
            'idade' => ['nullable', 'string', 'max:255'],
            'sexo' => ['nullable', 'string', 'max:255'],
            'tipo_sanguineo' => ['nullable', 'string', 'max:255'],
            'urgencia' => ['nullable', 'string', 'max:255'],
            'sintoma' => ['nullable', 'string'],
            'antecedentes_pessoais' => ['nullable', 'string'],
        ]);

        $paciente->fill([
            'nome' => $dados['nome'],
            'cpf' => $dados['cpf'],
            'protocolo' => $dados['protocolo'],
            'telefone' => $dados['telefone'] ?? null,
            'data_nascimento' => $dados['data_nascimento'] ?? null,
            'idade' => $dados['idade'] ?? null,
            'sexo' => $dados['sexo'] ?? null,
            'tipo_sanguineo' => $dados['tipo_sanguineo'] ?? null,
            'urgencia' => $dados['urgencia'] ?? null,
            'sintomas' => $dados['sintoma'] ?? null,
            'antecedentes_pessoais' => $dados['antecedentes_pessoais'] ?? null,
        ]);
        $paciente->save();

        return redirect('/admin/pacientes')->with('success', 'Paciente atualizado com sucesso.');
    }
    
    public function deletar($id)
    {
        $paciente = Pacientes::findOrFail($id);
        if ($paciente) {
            $paciente->delete();
            return redirect("/admin/pacientes")->with('success', 'Paciente removido com sucesso.');    
        } else {
            return redirect("/admin/pacientes")->with('error', 'Paciente não encontrado.');
        }
    }

    public function getPaciente($id)
    {
        $paciente = Pacientes::findOrFail($id);
        return response()->json($paciente);
    }
    public function editar($id)
    {
        $paciente = Pacientes::findOrFail($id);
        return view('admin.pacientes', compact('paciente'));
    }
}