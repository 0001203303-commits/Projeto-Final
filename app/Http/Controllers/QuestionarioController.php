<?php

namespace App\Http\Controllers;

use App\Jobs\GerarResumoIa;
use App\Services\ResumoIa;
use App\Models\CategoriaSintoma;
use App\Models\Pergunta;
use App\Models\Triagem;
use App\Models\TriagemResposta;
use App\Services\ClassificadorTriagem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionarioController extends Controller
{
    public function sintomas(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem || $triagem->bpm === null) {
            return redirect()->route('triagem.inicio');
        }

        $categorias = CategoriaSintoma::where('ativo', true)
            ->orderBy('ordem')
            ->with(['sintomas' => fn ($q) => $q->where('ativo', true)->orderBy('id')])
            ->get();

        $marcados = array_map('intval', old('sintomas', $triagem->sintomas()->pluck('sintomas.id')->all()));

        return view('totem.sintomas', compact('triagem', 'categorias', 'marcados'));
    }

    public function salvarSintomas(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem) {
            return redirect()->route('triagem.inicio');
        }

        $dados = $request->validate([
            'sintomas' => ['required', 'array', 'min:1', 'max:8'],
            'sintomas.*' => ['integer', Rule::exists('sintomas', 'id')->where('ativo', true)],
        ], [
            'sintomas.required' => 'Marque pelo menos um sintoma para continuar.',
            'sintomas.min' => 'Marque pelo menos um sintoma para continuar.',
            'sintomas.max' => 'Marque no máximo 8 sintomas, os que mais incomodam.',
        ]);

        $ids = array_map('intval', $dados['sintomas']);

        $triagem->sintomas()->sync($ids);

        // Se o paciente voltou e trocou os sintomas, descarta respostas de sintomas removidos
        $triagem->respostas()
            ->whereHas('pergunta', fn ($q) => $q->whereNotIn('sintoma_id', $ids))
            ->delete();

        return redirect()->route('triagem.perguntas');
    }

    public function perguntas(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem || $triagem->bpm === null) {
            return redirect()->route('triagem.inicio');
        }

        $perguntas = $this->perguntasDaTriagem($triagem);

        if ($perguntas->isEmpty()) {
            return $this->finalizar($triagem);
        }

        $respondidas = $triagem->respostas()->pluck('valor', 'pergunta_id')->toArray();

        return view('totem.perguntas', compact('triagem', 'perguntas', 'respondidas'));
    }

    public function salvarPerguntas(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem) {
            return redirect()->route('triagem.inicio');
        }

        $respostas = $request->input('respostas', []);

        $itens = $this->perguntasDaTriagem($triagem)->map(function ($p) use ($respostas) {
            $valor = $respostas[$p['id']] ?? null;
            $valido = $p['tipo'] === 'intensidade'
                ? in_array($valor, ['1', '2', '3', 1, 2, 3], true)
                : in_array($valor, ['0', '1', 0, 1], true);

            return $valido ? [$p, (int) $valor] : null;
        });

        if ($itens->contains(null)) {
            return redirect()->route('triagem.perguntas')
                ->withErrors(['respostas' => 'Responda todas as perguntas para continuar.']);
        }

        foreach ($itens as [$p, $valor]) {
            $pontos = $p['tipo'] === 'intensidade'
                ? $valor
                : ($valor === 1 ? $p['pontos_sim'] : 0);

            TriagemResposta::updateOrCreate(
                ['triagem_id' => $triagem->id, 'pergunta_id' => $p['id']],
                ['valor' => $valor, 'pontos' => $pontos]
            );
        }

        return $this->finalizar($triagem);
    }

    public function resultado(Request $request)
    {
        $triagem = $this->triagemAtual($request);

        if (! $triagem) {
            return redirect()->route('triagem.inicio');
        }

        if (! $triagem->cor) {
            return redirect()->route('triagem.sintomas');
        }

        $info = config('triagem.cores.'.$triagem->cor);

        return view('totem.resultado', compact('triagem', 'info'));
    }

    public function encerrar(Request $request)
    {
        $request->session()->forget('triagem_id');

        return redirect()->route('triagem.inicio');
    }

    private function finalizar(Triagem $triagem)
            {
                $triagem->update(app(ClassificadorTriagem::class)->classificar($triagem));

                // 1) resumo local imediato (funciona offline)
                app(ResumoIa::class)->gerarLocal($triagem);

                // 2) Gemini em segundo plano, depois que o paciente já recebeu a tela
                GerarResumoIa::dispatchAfterResponse($triagem->id);

                return redirect()->route('triagem.resultado');
            }

    private function perguntasDaTriagem(Triagem $triagem)
    {
        return $triagem->sintomas()
            ->with(['perguntas' => fn ($q) => $q->where('ativo', true)->orderBy('ordem')])
            ->orderBy('sintomas.id')
            ->get()
            ->flatMap(fn ($s) => $s->perguntas->map(fn (Pergunta $p) => [
                'id' => $p->id,
                'sintoma' => $s->nome,
                'texto' => $p->texto,
                'tipo' => $p->tipo,
                'pontos_sim' => (int) $p->pontos_sim,
            ]))
            ->values();
    }

    private function triagemAtual(Request $request): ?Triagem
    {
        $id = $request->session()->get('triagem_id');

        return $id ? Triagem::with('paciente')->find($id) : null;
    }
}
