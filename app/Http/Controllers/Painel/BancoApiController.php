<?php

/*namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\IaEvento;
use App\Models\Totem;
use App\Services\ConfiguracaoGemini;
use App\Services\GerenciadorBanco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BancoApiController extends Controller
{
    public function index(Request $request, GerenciadorBanco $banco, ConfiguracaoGemini $gemini)
    {
        $aba = $request->query('aba') === 'api' ? 'api' : 'banco';

        if ($aba === 'banco') {
            return view('painel.bancoapi.index', [
                'aba' => 'banco',
                'status' => $banco->status(),
                'tabelas' => $banco->tabelas(),
                'migrations' => $banco->migrations(),
                'backups' => $banco->backups(),
            ]);
        }

        // Mantém só 30 dias de histórico das chamadas à IA
        IaEvento::where('created_at', '<', now()->subDays(30))->delete();

        return view('painel.bancoapi.index', ['aba' => 'api'] + $this->dadosApi($gemini));
    }

    // ------------------------------------------------------------------
    // Banco de dados
    // ------------------------------------------------------------------
    public function aplicarMigrations(Request $request, GerenciadorBanco $banco)
    {
        $resultado = $banco->aplicarMigrations();

        Log::warning('TI aplicou migrations pelo painel', ['usuario' => $request->user()->id, 'ok' => $resultado['ok']]);

        if (! $resultado['ok']) {
            return back()->withErrors(['acao' => 'Falha ao aplicar as migrations: '.Str::limit($resultado['saida'], 400)]);
        }

        return redirect()->to('/painel/bancoapi?aba=banco')->with('ok', 'Migrations aplicadas com sucesso.');
    }

    public function criarBackup(Request $request, GerenciadorBanco $banco)
    {
        $resultado = $banco->criarBackup();

        Log::warning('TI criou um backup do banco', ['usuario' => $request->user()->id, 'ok' => $resultado['ok']]);

        if (! $resultado['ok']) {
            return back()->withErrors(['acao' => 'Não foi possível criar o backup: '.Str::limit($resultado['erro'], 400)]);
        }

        return redirect()->to('/painel/bancoapi?aba=banco')->with('ok', "Backup criado: {$resultado['arquivo']}");
    }

    public function baixarBackup(Request $request, GerenciadorBanco $banco, string $arquivo)
    {
        $caminho = $banco->caminhoDoBackup($arquivo);

        abort_if($caminho === null, 404);

        Log::warning('TI baixou um backup do banco', ['usuario' => $request->user()->id, 'arquivo' => $arquivo]);

        return response()->download($caminho, $arquivo);
    }

    public function excluirBackup(Request $request, GerenciadorBanco $banco, string $arquivo)
    {
        abort_unless($banco->excluirBackup($arquivo), 404);

        Log::warning('TI excluiu um backup do banco', ['usuario' => $request->user()->id, 'arquivo' => $arquivo]);

        return redirect()->route('painel.bancoapi.index')->with('ok', 'Backup excluído.');
    }

    // ------------------------------------------------------------------
    // API: Gemini
    // ------------------------------------------------------------------
    public function salvarGemini(Request $request)
    {
        $dados = $request->validate([
            'modelo' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9._\-]+$/'],
            'timeout' => ['required', 'integer', 'between:5,60'],
            'chave' => ['nullable', 'string', 'min:20', 'max:300'],
        ], [
            'modelo.required' => 'Informe o modelo.',
            'modelo.regex' => 'O nome do modelo só pode ter letras, números, ponto, hífen e sublinhado.',
            'timeout.*' => 'O tempo limite deve estar entre 5 e 60 segundos.',
            'chave.min' => 'A chave parece curta demais. Confira se copiou inteira.',
        ]);

        Configuracao::definir('gemini.ativo', $request->boolean('ativo') ? '1' : '0');
        Configuracao::definir('gemini.modelo', $dados['modelo']);
        Configuracao::definir('gemini.timeout', (string) $dados['timeout']);

        if (filled($dados['chave'] ?? null)) {
            Configuracao::definirCriptografado('gemini.chave', trim($dados['chave']));
        } elseif ($request->boolean('remover_chave')) {
            Configuracao::definir('gemini.chave', null);
        }

        Log::info('TI alterou a configuração do Gemini', ['usuario' => $request->user()->id]);

        return redirect()->route('painel.bancoapi.index', ['aba' => 'api'])->with('ok', 'Configuração do Gemini salva.');
    }

    public function testarGemini(ConfiguracaoGemini $gemini)
    {
        return redirect()
            ->route('painel.bancoapi.index', ['aba' => 'api'])
            ->with('teste', $gemini->testar());
    }

    public function modelosGemini(ConfiguracaoGemini $gemini)
    {
        return response()->json($gemini->modelos());
    }

    // ------------------------------------------------------------------
    // API: totens
    // ------------------------------------------------------------------
    public function salvarTimeoutTotem(Request $request)
    {
        $dados = $request->validate([
            'timeout' => ['required', 'integer', 'between:30,3600'],
        ], [
            'timeout.*' => 'Informe um valor entre 30 e 3600 segundos.',
        ]);

        Configuracao::definir('totem.timeout', (string) $dados['timeout']);

        return redirect()->route('painel.bancoapi.index', ['aba' => 'api'])->with('ok', 'Tempo limite dos totens salvo.');
    }

    // ------------------------------------------------------------------
    private function dadosApi(ConfiguracaoGemini $g): array
    {
        $eventos = fn () => IaEvento::where('created_at', '>=', now()->subDay());

        $tentativas = $eventos()->count();
        $sucessos = $eventos()->where('sucesso', true)->count();
        $mediaMs = (int) round($eventos()->where('sucesso', true)->avg('duracao_ms') ?? 0);

        $totens = Totem::orderBy('nome')->get();

        return [
            'gemini' => [
                'ativo' => $g->ativo(),
                'modelo' => $g->modelo(),
                'timeout' => $g->timeout(),
                'chave' => $g->chaveMascarada(),
                'origem' => $g->origemChave(),
            ],
            'ia' => [
                'tentativas' => $tentativas,
                'sucessos' => $sucessos,
                'taxa' => $tentativas ? (int) round($sucessos / $tentativas * 100) : null,
                'media_ms' => $mediaMs,
                'falhas' => IaEvento::where('sucesso', false)->latest('created_at')->limit(5)->get(),
            ],
            'totens' => $totens,
            'totens24h' => $totens->filter(fn ($t) => $t->ultimo_sinal && $t->ultimo_sinal->gt(now()->subDay()))->count(),
            'timeoutTotem' => (int) Configuracao::obter('totem.timeout', (string) config('triagem.totem_timeout', 120)),
        ];
    }
}
*/
