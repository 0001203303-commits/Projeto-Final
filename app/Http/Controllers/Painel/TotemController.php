<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Totem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TotemController extends Controller
{
    private const STATUS = ['online', 'offline', 'manutencao'];

    public function index()
    {
        $this->marcarOffline();

        return view('painel.totens.index', $this->dados());
    }

    /** Só a lista, para a atualização automática. */
    public function parcial()
    {
        $this->marcarOffline();

        return view('painel.totens._lista', $this->dados());
    }

    public function create()
    {
        return view('painel.totens.form', ['totem' => new Totem(['status' => 'offline'])]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate($this->regras(), $this->mensagens());

        $token = Totem::gerarToken();

        $totem = new Totem($dados);
        $totem->token_hash = Totem::hashDoToken($token);

        if ($totem->status === 'online') {
            $totem->ultimo_sinal = now();
        }

        $totem->save();

        return redirect()
            ->route('painel.totens.edit', $totem)
            ->with('ok', 'Totem cadastrado com sucesso.')
            ->with('novo_token', $token);
    }

    public function edit(Totem $totem)
    {
        return view('painel.totens.form', compact('totem'));
    }

    public function update(Request $request, Totem $totem)
    {
        $dados = $request->validate($this->regras($totem), $this->mensagens());

        $totem->fill($dados);

        // Mesma regra do seletor: colocar manualmente em "online" registra um sinal
        if ($totem->isDirty('status') && $totem->status === 'online') {
            $totem->ultimo_sinal = now();
        }

        $totem->save();

        return redirect()->route('painel.totens.index')->with('ok', 'Totem atualizado.');
    }

    /** Seletor de status do cartão. */
    public function atualizarStatus(Request $request, Totem $totem)
    {
        $dados = $request->validate([
            'status' => ['required', Rule::in(self::STATUS)],
        ]);

        $totem->status = $dados['status'];

        if ($dados['status'] === 'online') {
            $totem->ultimo_sinal = now();
        }

        $totem->save();

        return back()->with('ok', "Status de “{$totem->nome}” alterado.");
    }

    public function regenerarToken(Totem $totem)
    {
        $token = Totem::gerarToken();

        $totem->token_hash = Totem::hashDoToken($token);
        $totem->save();

        return redirect()
            ->route('painel.totens.edit', $totem)
            ->with('ok', 'Novo token gerado. O anterior deixou de funcionar.')
            ->with('novo_token', $token);
    }

    public function destroy(Totem $totem)
    {
        $nome = $totem->nome;
        $totem->delete();

        return redirect()->route('painel.totens.index')->with('ok', "Totem “{$nome}” excluído.");
    }

    // ------------------------------------------------------------------

    private function dados(): array
    {
        $totens = Totem::orderBy('nome')->get();

        $contagem = [
            'online' => $totens->where('status', 'online')->count(),
            'offline' => $totens->where('status', 'offline')->count(),
            'manutencao' => $totens->where('status', 'manutencao')->count(),
        ];

        return compact('totens', 'contagem');
    }

    /** Totens "online" sem sinal recente passam a "offline" (sem precisar de agendador). */
    private function marcarOffline(): void
    {
        Totem::where('status', 'online')
            ->where(function ($q) {
                $q->whereNull('ultimo_sinal')
                    ->orWhere('ultimo_sinal', '<', now()->subSeconds(config('triagem.totem_timeout', 120)));
            })
            ->update(['status' => 'offline']);
    }

    private function regras(?Totem $totem = null): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:100', Rule::unique('totens', 'nome')->ignore($totem?->id)],
            'localizacao' => ['required', 'string', 'min:3', 'max:150'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(self::STATUS)],
        ];
    }

    private function mensagens(): array
    {
        return [
            'nome.required' => 'Informe o nome do totem.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.unique' => 'Já existe um totem com este nome.',
            'localizacao.required' => 'Informe onde o totem está instalado.',
            'localizacao.min' => 'A localização deve ter pelo menos 3 caracteres.',
            'status.*' => 'Escolha um status válido.',
        ];
    }
}
