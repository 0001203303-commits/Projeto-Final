<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;

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
        $paciente->status = $paciente->status ?? 1;
        
        $paciente->tipo_sanguineo = $request->tipo_sanguineo ?? $paciente->tipo_sanguineo;
        $paciente->data_nascimento = $request->data_nascimento ?? $paciente->data_nascimento;
        $paciente->sexo = $request->sexo ?? $paciente->sexo;
        $paciente->telefone = $request->telefone ?? $paciente->telefone;
        $paciente->endereco = $request->endereco ?? $paciente->endereco;
        $paciente->email = $request->email ?? $paciente->email;
        $paciente->antecedentes_pessoais = $request->antecedentes_pessoais ?? $paciente->antecedentes_pessoais;
        
        // 1. Captura as respostas de triagem vindas do Totem (se houver)
        $paciente->sintomas = $request->sintoma ?? $request->sintomas ?? json_encode($request->respostas_quiz) ?? $paciente->sintomas;

        // 2. Calcula a urgência (Protocolo de Manchester) com base no score enviado pelo JS
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
            // Caso venha de um formulário tradicional
            $paciente->urgencia = $request->urgencia ?? $paciente->urgencia;
        }

        if ($request->protocolo) {
            $paciente->protocolo = $request->protocolo;
        }

        // Salva opcionalmente sinais vitais se as colunas existirem no seu banco:
        // $paciente->bpm = $request->bpm;
        // $paciente->spo2 = $request->spo2;
        // $paciente->temperatura = $request->temp;

        $paciente->save();

        // 3. A MÁGICA: Se a requisição pedir JSON (AJAX/Fetch), retorna JSON. Se for formulário, redireciona!
        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'classificacao' => $paciente->urgencia,
                'cor_hex' => $corHex ?? '#3b82f6',
                'message' => 'Paciente processado com sucesso!'
            ]);
        }

        return redirect("/admin/pacientes")->with('success', 'Paciente processado com sucesso!');
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