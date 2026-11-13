<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\CategoriaSintoma;
use App\Models\Pergunta;
use App\Models\Sintoma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class QuestionarioAdminController extends Controller
{
    public function index()
    {
        $categorias = CategoriaSintoma::orderBy('ordem')->orderBy('id')
            ->with(['sintomas' => fn ($q) => $q->orderBy('id')
                ->withCount('triagens')
                ->with(['perguntas' => fn ($p) => $p->orderBy('ordem')->orderBy('id')->withCount('respostas')])])
            ->get();

        return view('painel.questionario.index', compact('categorias'));
    }

    // ================= Categorias =================

    public function novaCategoria()
    {
        $ordem = (int) CategoriaSintoma::max('ordem') + 1;

        return view('painel.questionario.categoria', [
            'categoria' => new CategoriaSintoma(['ordem' => $ordem, 'ativo' => true]),
        ]);
    }

    public function criarCategoria(Request $request)
    {
        $categoria = CategoriaSintoma::create($this->validarCategoria($request) + ['ativo' => true]);
        $this->registrar('categoria criada', $categoria->id);

        return redirect()->route('painel.questionario.index')->with('ok', 'Categoria criada.');
    }

    public function editarCategoria(CategoriaSintoma $categoria)
    {
        return view('painel.questionario.categoria', compact('categoria'));
    }

    public function atualizarCategoria(Request $request, CategoriaSintoma $categoria)
    {
        $categoria->update($this->validarCategoria($request, $categoria));
        $this->registrar('categoria editada', $categoria->id);

        return redirect()->route('painel.questionario.index')->with('ok', 'Categoria atualizada.');
    }

    public function alternarCategoria(CategoriaSintoma $categoria)
    {
        $categoria->update(['ativo' => ! $categoria->ativo]);
        $this->registrar($categoria->ativo ? 'categoria reativada' : 'categoria desativada', $categoria->id);

        return back()->with('ok', $categoria->ativo ? 'Categoria reativada.' : 'Categoria desativada. Ela deixa de aparecer no totem.');
    }

    public function excluirCategoria(CategoriaSintoma $categoria)
    {
        $usada = Sintoma::where('categoria_id', $categoria->id)->whereHas('triagens')->exists();

        if ($usada) {
            return back()->withErrors(['acao' => 'Esta categoria tem sintomas usados em triagens e não pode ser excluída. Desative-a para preservar o histórico.']);
        }

        $categoria->delete(); // sintomas e perguntas (sem uso) saem por cascata
        $this->registrar('categoria excluída', $categoria->id);

        return back()->with('ok', 'Categoria excluída.');
    }

    // ================= Sintomas =================

    public function novoSintoma(Request $request)
    {
        return view('painel.questionario.sintoma', [
            'sintoma'    => new Sintoma(['categoria_id' => $request->integer('categoria') ?: null, 'pontos_base' => 1, 'ativo' => true]),
            'categorias' => CategoriaSintoma::orderBy('ordem')->get(),
        ]);
    }

    public function criarSintoma(Request $request)
    {
        $sintoma = Sintoma::create($this->validarSintoma($request) + ['ativo' => true]);
        $this->registrar('sintoma criado', $sintoma->id);

        return $this->voltarAoSintoma($sintoma->id, 'Sintoma criado.');
    }

    public function editarSintoma(Sintoma $sintoma)
    {
        return view('painel.questionario.sintoma', [
            'sintoma'    => $sintoma,
            'categorias' => CategoriaSintoma::orderBy('ordem')->get(),
        ]);
    }

    public function atualizarSintoma(Request $request, Sintoma $sintoma)
    {
        $sintoma->update($this->validarSintoma($request, $sintoma));
        $this->registrar('sintoma editado', $sintoma->id);

        return $this->voltarAoSintoma($sintoma->id, 'Sintoma atualizado.');
    }

    public function alternarSintoma(Sintoma $sintoma)
    {
        $sintoma->update(['ativo' => ! $sintoma->ativo]);
        $this->registrar($sintoma->ativo ? 'sintoma reativado' : 'sintoma desativado', $sintoma->id);

        return $this->voltarAoSintoma($sintoma->id, $sintoma->ativo ? 'Sintoma reativado.' : 'Sintoma desativado. Ele deixa de aparecer no totem.');
    }

    public function excluirSintoma(Sintoma $sintoma)
    {
        $usado = $sintoma->triagens()->exists()
            || Pergunta::where('sintoma_id', $sintoma->id)->whereHas('respostas')->exists();

        if ($usado) {
            return back()->withErrors(['acao' => 'Este sintoma já foi usado em triagens e não pode ser excluído. Desative-o para preservar o histórico.']);
        }

        $sintoma->delete();
        $this->registrar('sintoma excluído', $sintoma->id);

        return back()->with('ok', 'Sintoma excluído.');
    }

    // ================= Perguntas =================

    public function novaPergunta(Request $request)
    {
        $sintoma = Sintoma::find($request->integer('sintoma'));

        if (! $sintoma) {
            return redirect()->route('painel.questionario.index')->withErrors(['acao' => 'Escolha o sintoma ao qual a pergunta pertence.']);
        }

        $ordem = (int) Pergunta::where('sintoma_id', $sintoma->id)->max('ordem') + 1;

        return view('painel.questionario.pergunta', [
            'pergunta' => new Pergunta(['sintoma_id' => $sintoma->id, 'tipo' => 'sim_nao', 'pontos_sim' => 1, 'ordem' => $ordem, 'ativo' => true]),
            'sintoma'  => $sintoma,
        ]);
    }

    public function criarPergunta(Request $request)
    {
        $sintoma = Sintoma::findOrFail($request->integer('sintoma_id'));
        $pergunta = Pergunta::create($this->validarPergunta($request, $sintoma->id) + ['sintoma_id' => $sintoma->id, 'ativo' => true]);
        $this->registrar('pergunta criada', $pergunta->id);

        return $this->voltarAoSintoma($sintoma->id, 'Pergunta criada.');
    }

    public function editarPergunta(Pergunta $pergunta)
    {
        $pergunta->loadCount('respostas');

        return view('painel.questionario.pergunta', ['pergunta' => $pergunta, 'sintoma' => $pergunta->sintoma]);
    }

    public function atualizarPergunta(Request $request, Pergunta $pergunta)
    {
        $dados = $this->validarPergunta($request, $pergunta->sintoma_id, $pergunta);

        if ($dados['tipo'] !== $pergunta->tipo && $pergunta->respostas()->exists()) {
            return back()->withInput()->withErrors(['tipo' => 'Esta pergunta já tem respostas e não pode mudar de tipo. Crie uma nova pergunta e desative esta.']);
        }

        $pergunta->update($dados);
        $this->registrar('pergunta editada', $pergunta->id);

        return $this->voltarAoSintoma($pergunta->sintoma_id, 'Pergunta atualizada.');
    }

    public function alternarPergunta(Pergunta $pergunta)
    {
        $pergunta->update(['ativo' => ! $pergunta->ativo]);
        $this->registrar($pergunta->ativo ? 'pergunta reativada' : 'pergunta desativada', $pergunta->id);

        return $this->voltarAoSintoma($pergunta->sintoma_id, $pergunta->ativo ? 'Pergunta reativada.' : 'Pergunta desativada. Ela deixa de ser feita no totem.');
    }

    public function excluirPergunta(Pergunta $pergunta)
    {
        if ($pergunta->respostas()->exists()) {
            return back()->withErrors(['acao' => 'Esta pergunta já foi respondida em triagens e não pode ser excluída. Desative-a para preservar o histórico.']);
        }

        $pergunta->delete();
        $this->registrar('pergunta excluída', $pergunta->id);

        return $this->voltarAoSintoma($pergunta->sintoma_id, 'Pergunta excluída.');
    }

    // ================= Validação e auxiliares =================

    private function validarCategoria(Request $request, ?CategoriaSintoma $categoria = null): array
    {
        return $request->validate([
            'nome'  => ['required', 'string', 'min:2', 'max:60', Rule::unique('categorias_sintomas', 'nome')->ignore($categoria?->id)],
            'icone' => ['nullable', 'string', 'max:8'],
            'ordem' => ['required', 'integer', 'between:0,99'],
        ], [
            'nome.required' => 'Informe o nome da categoria.',
            'nome.min'      => 'O nome está muito curto.',
            'nome.unique'   => 'Já existe uma categoria com este nome.',
            'icone.max'     => 'Use um único emoji ou símbolo.',
            'ordem.*'       => 'Informe a ordem como um número de 0 a 99.',
        ]);
    }

    private function validarSintoma(Request $request, ?Sintoma $sintoma = null): array
    {
        $categoriaId = $request->integer('categoria_id');

        return $request->validate([
            'categoria_id' => ['required', 'integer', 'exists:categorias_sintomas,id'],
            'nome'         => ['required', 'string', 'min:2', 'max:80',
                               Rule::unique('sintomas', 'nome')->where('categoria_id', $categoriaId)->ignore($sintoma?->id)],
            'pontos_base'  => ['required', 'integer', 'between:0,10'],
            'cor_minima'   => ['nullable', 'in:amarelo,laranja'],
        ], [
            'categoria_id.*' => 'Escolha a categoria.',
            'nome.required'  => 'Informe o nome do sintoma.',
            'nome.min'       => 'O nome está muito curto.',
            'nome.unique'    => 'Já existe um sintoma com este nome nesta categoria.',
            'pontos_base.*'  => 'Informe pontos de 0 a 10.',
            'cor_minima.in'  => 'Cor mínima inválida.',
        ]);
    }

    private function validarPergunta(Request $request, int $sintomaId, ?Pergunta $pergunta = null): array
    {
        $dados = $request->validate([
            'texto'      => ['required', 'string', 'min:5', 'max:200',
                             Rule::unique('perguntas', 'texto')->where('sintoma_id', $sintomaId)->ignore($pergunta?->id)],
            'tipo'       => ['required', 'in:sim_nao,intensidade'],
            'pontos_sim' => [Rule::requiredIf($request->input('tipo') === 'sim_nao'), 'nullable', 'integer', 'between:0,10'],
            'cor_minima' => ['nullable', 'in:amarelo,laranja'],
            'ordem'      => ['required', 'integer', 'between:0,99'],
        ], [
            'texto.required'    => 'Escreva a pergunta.',
            'texto.min'         => 'A pergunta está muito curta.',
            'texto.unique'      => 'Este sintoma já tem uma pergunta com o mesmo texto.',
            'tipo.*'            => 'Escolha o tipo da pergunta.',
            'pontos_sim.*'      => 'Informe pontos de 0 a 10.',
            'cor_minima.in'     => 'Cor mínima inválida.',
            'ordem.*'           => 'Informe a ordem como um número de 0 a 99.',
        ]);

        $dados['texto'] = trim($dados['texto']);

        // Intensidade: os pontos vêm da resposta (1 a 3) e não há cor mínima
        if ($dados['tipo'] === 'intensidade') {
            $dados['pontos_sim'] = 0;
            $dados['cor_minima'] = null;
        } else {
            $dados['pontos_sim'] = (int) $dados['pontos_sim'];
        }

        return $dados;
    }

    private function voltarAoSintoma(int $sintomaId, string $mensagem)
    {
        return redirect()
            ->to(route('painel.questionario.index', ['abrir' => $sintomaId]) . '#sintoma-' . $sintomaId)
            ->with('ok', $mensagem);
    }

    /** Registra quem alterou o quê (só IDs, sem texto do questionário). */
    private function registrar(string $acao, int $id): void
    {
        Log::info('Questionário alterado: ' . $acao, ['usuario' => auth()->id(), 'id' => $id]);
    }
}   