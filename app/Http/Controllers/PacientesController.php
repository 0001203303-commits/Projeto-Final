<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use Illuminate\Http\Request;

class PacientesController extends Controller
{
    public function index()
    {
        $pacientes = Pacientes::get(); 
        return view('admin.pacientes', compact('pacientes'));   
    }

    public function salvar(Request $request)
    {
        try {
           
            if ($request->id) {
                $p = Pacientes::findOrFail($request->id);
            } else {
                $p = new Pacientes();
            }

            // 2. Dados básicos que seu formulário envia ou pode enviar futuramente
            $p->nome = $request->nome;
            $p->cpf = $request->cpf;
            $p->protocolo = $request->protocolo ?? uniqid('TR-'); // Gera um protocolo automático se não vier
            $p->idade = $request->idade;
            $p->telefone = $request->telefone;
            $p->status = 1;
            $p->data_nascimento = $request->data_nascimento;
            $p->sexo = $request->sexo;
            $p->tipo_sanguineo = $request->tipo_sanguineo; 
            $p->antecedentes_pessoais = $request->antecedentes_pessoais;

            // 3. Capturando os dados de triagem dinâmicos vindos do JavaScript
            // Se o seu modelo possuir colunas para os sinais vitais, descomente as linhas abaixo:
            /*
            $p->bpm = $request->bpm;
            $p->spo2 = $request->spo2;
            $p->temperatura = $request->temp;
            $p->nivel_dor = $request->nivel_dor;
            */

            if ($request->has('respostas_quiz')) {
                $respostasTexto = collect($request->respostas_quiz)
                    ->map(fn($item) => "{$item['pergunta']}: {$item['resposta']}")
                    ->implode(' | ');
                $p->sintomas = $respostasTexto;
            } else {
                $p->sintomas = $request->sintoma;
            }

      
            $score = $request->score_final ?? 0;
            $classificacao = 'Não Urgente (Azul)';
            $corHex = '#3b82f6';

            if ($score >= 11) {
                $classificacao = 'Emergência (Vermelho)';
                $corHex = '#ef4444';
            } elseif ($score >= 8) {
                $classificacao = 'Muito Urgente (Laranja)';
                $corHex = '#f97316';
            } elseif ($score >= 5) {
                $classificacao = 'Urgente (Amarelo)';
                $corHex = '#eab308';
            }

       
            $p->save();

            return response()->json([
                'success' => true,
                'message' => 'Triagem e paciente salvos com sucesso!',
                'classificacao' => $classificacao,
                'cor_hex' => $corHex
            ]);

        } catch (\Exception $e) {
           
            return response()->json([
                'success' => false,
                'redirect_url' => url('/admin/pacientes'),
                'message' => 'Erro ao salvar: ' . $e->getMessage()
            ], 500);
        }
    }
  
    public function deletar($id)
    {
        $p = Pacientes::findOrFail($id);
        if ($p) {
            $p->delete();
            return redirect("/admin/pacientes");    
        } else {
            return redirect("/admin/pacientes")->with('error', 'Paciente não encontrado.');
        }
    }
    public function editar($id)
    {
        $paciente = Pacientes::findOrFail($id);
        if ($paciente) {
            return view('admin.editar_paciente', compact('paciente'));    
        } else {
            return redirect("/admin/pacientes")->with('error', 'Paciente não encontrado.');
        }
    }
}