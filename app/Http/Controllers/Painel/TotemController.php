<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Totem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Configuracao;

class TotemController extends Controller
{
        public function index()
    {
        $this->marcarOffline();

        // ... o restante continua igual
    }

    public function parcial()
    {
        $this->marcarOffline();

        // ... o restante continua igual
    }
public function create()
    {
        return view('painel.totens.form', ['totem' => new Totem]);
    }

    public function store(Request $request)
    {
        $dados = $this->validar($request);

        $totem = new Totem($dados);
        $totem->ativo = true;
        $token = $totem->gerarToken();
        $totem->save();

        return redirect()->route('painel.totens.index')
            ->with('ok', 'Totem cadastrado.')
            ->with('token_totem', ['nome' => $totem->nome, 'token' => $token]);
    }

    public function edit(Totem $totem)
    {
        return view('painel.totens.form', compact('totem'));
    }

    public function update(Request $request, Totem $totem)
    {
        $totem->update($this->validar($request, $totem));

        return redirect()->route('painel.totens.index')->with('ok', 'Totem atualizado.');
    }

    public function novoToken(Totem $totem)
    {
        $token = $totem->gerarToken();
        $totem->save();

        return redirect()->route('painel.totens.index')
            ->with('ok', 'Novo token gerado. O token anterior deixou de funcionar.')
            ->with('token_totem', ['nome' => $totem->nome, 'token' => $token]);
    }

    public function alternar(Totem $totem)
    {
        $totem->update(['ativo' => ! $totem->ativo]);

        return back()->with('ok', $totem->ativo ? 'Totem reativado.' : 'Totem desativado. Ele não consegue mais se comunicar com o sistema.');
    }

    public function destroy(Totem $totem)
    {
        $totem->delete();

        return back()->with('ok', 'Totem excluído.');
    }

    

    // ------------------------------------------------------------------

    private function validar(Request $request, ?Totem $totem = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'min:3', 'max:100', Rule::unique('totens', 'nome')->ignore($totem?->id)],
            'local' => ['nullable', 'string', 'max:150'],
            'observacoes' => ['nullable', 'string', 'max:500'],
        ], [
            'nome.required' => 'Informe o nome do totem.',
            'nome.min' => 'O nome está muito curto.',
            'nome.unique' => 'Já existe um totem com este nome.',
            'local.max' => 'O local pode ter no máximo 150 caracteres.',
            'observacoes.max' => 'As observações podem ter no máximo 500 caracteres.',
        ]);
    }

    private function dados(): array
    {
        return ['totens' => Totem::orderByDesc('ativo')->orderBy('nome')->get()];
    }

        /** Totens "online" sem sinal recente passam a "offline" (sem precisar de agendador). */
    private function marcarOffline(): void
    {
        $limite = (int) Configuracao::obter('totem.timeout', (string) config('triagem.totem_timeout', 120));

        Totem::where('status', 'online')
            ->where(function ($q) use ($limite) {
                $q->whereNull('ultimo_sinal')
                  ->orWhere('ultimo_sinal', '<', now()->subSeconds($limite));
            })
            ->update(['status' => 'offline']);
    }

    
}
