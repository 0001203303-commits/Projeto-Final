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
            // 1. Extrai o score final enviado pelo Totem
            $scoreFinal = intval($request->input('score_final', 0));

            // 2. Processa os resumos das perguntas marcadas como 'Sim'
            $sintomasTexto = '';
            if (!empty($request->respostas_quiz) && is_array($request->respostas_quiz)) {
                $sintomasTexto = collect($request->respostas_quiz)
                    ->where('resposta', 'Sim')
                    ->pluck('pergunta') // Captura o "summary" vindo do JavaScript
                    ->implode(', ');
            }

            // Se o paciente não marcou nenhuma pergunta com 'Sim'
            if (empty($sintomasTexto)) {
                $sintomasTexto = "Nenhum sintoma de alerta selecionado no questionário.";
            }

            // 3. Define a classificação de risco com base no score (Manchester)
            if ($scoreFinal >= 11) {
                $classificacao = 'EMERGÊNCIA (Imediato)';
                $corHex = '#ef4444'; // Vermelho
                $status = 1;
            } elseif ($scoreFinal >= 8) {
                $classificacao = 'MUITO URGENTE (10 min)';
                $corHex = '#f97316'; // Laranja
                $status = 2;
            } elseif ($scoreFinal >= 5) {
                $classificacao = 'URGENTE (60 min)';
                $corHex = '#eab308'; // Amarelo
                $status = 3;
            } else {
                $classificacao = 'POUCO URGENTE (120 min)';
                $corHex = '#22c55e'; // Verde
                $status = 4;
            }

            // 4. Salva o registro do paciente no Banco de Dados
            $paciente = new Pacientes();
            $paciente->nome = $request->input('nome', 'Paciente Sem Nome');
            $paciente->cpf = $request->input('cpf');
            $paciente->protocolo = 'TR-' . Str::random(13); // Gera um protocolo único (ex: TR-6a3054b40a641)
            $paciente->status = $status;
            
            // Dados aferidos no Totem
            $paciente->sintomas = $sintomasTexto; // O resumo curto e cirúrgico entra aqui
            
            // Opcional: Se sua tabela já possuir colunas para os sinais vitais isolados, salve-os aqui
            // $paciente->bpm = $request->input('bpm');
            // $paciente->spo2 = $request->input('spo2');
            // $paciente->temp = $request->input('temp');
            // $paciente->nivel_dor = $request->input('nivel_dor');

            $paciente->save();

            // 5. Retorna o JSON limpo que o seu JavaScript precisa para renderizar a tela de sucesso
            return response()->json([
                'success' => true,
                'classificacao' => $classificacao,
                'cor_hex' => $corHex
            ]);

        } catch (\Exception $e) {
            // Em caso de erro no banco (como colunas faltando), responde como JSON prevenindo o erro '<'
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $e->getMessage()
            ], 500);
        }
    }
}
