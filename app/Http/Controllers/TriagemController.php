<?php

namespace App\Http\Controllers;

use App\Models\TriagemCategoria;
use App\Models\Pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TriagemController extends Controller
{
    public function index()
    {
        return view('triagem.index');
    }

    public function showSubcategorias($slug)
    {
        $categoriaPai = TriagemCategoria::where('slug', $slug)->firstOrFail();
        $subcategorias = $categoriaPai->subcategorias;

        return view('triagem.subcategorias', compact('categoriaPai', 'subcategorias'));
    }

    public function startQuiz($slug)
    {
        $subcategoria = TriagemCategoria::where('slug', $slug)->with('perguntas')->firstOrFail();
        return view('triagem.quiz', compact('subcategoria'));
    }

    public function finalizar(Request $request)
    {
        try {
            $paciente = new Pacientes();

            $score = $request->input('score_final');
            
            $classificacao = "NÃO URGENTE (AZUL)";
            $corHex = "#3b82f6"; 

            if ($score !== null) {
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
                $paciente->urgencia = $request->input('urgencia', $classificacao);
            }

            $paciente->nome = $request->input('nome') ?? $request->input('dadosTriagem.nome') ?? 'Paciente Sem Nome';
            $paciente->cpf = $request->input('cpf') ?? $request->input('dadosTriagem.cpf');
            $paciente->protocolo = 'TR-' . Str::random(13);
            
            $paciente->status = $request->input('status', 1); 

            if ($request->has('respostas_quiz')) {
                $paciente->sintomas = json_encode($request->input('respostas_quiz'), JSON_UNESCAPED_UNICODE);
            } else {
                $paciente->sintomas = $request->input('sintomas') ?? $request->input('sintoma') ?? 'Nenhum sintoma relatado';
            }
            
            $paciente->horario = now()->format('H:i');
            $paciente->idade = $request->input('idade', 0);

            $paciente->save();

            return response()->json([
                'success' => true,
                'classificacao' => $classificacao,
                'cor_hex' => $corHex
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}