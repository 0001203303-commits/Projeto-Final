<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use Illuminate\Http\Request;

class FilaController extends Controller
{
    public function index()
    {
        return view('painel.fila.index', $this->dadosDaFila());
    }

    /** Devolve só a lista, para a tela se atualizar sozinha. */
    public function parcial()
    {
        return view('painel.fila._lista', $this->dadosDaFila());
    }

    public function mostrar(Triagem $triagem)
    {
        abort_if($triagem->cor === null, 404);

        $triagem->load(['paciente', 'atendente', 'sintomas.categoria', 'respostas.pergunta.sintoma']);

        return view('painel.fila.ficha', compact('triagem'));
    }

    public function chamar(Request $request, Triagem $triagem)
    {
        // Atualização atômica: se dois médicos clicarem juntos, só um consegue
        $alterados = Triagem::whereKey($triagem->id)
            ->where('status', 'aguardando')
            ->update([
                'status'       => 'em_atendimento',
                'atendido_por' => $request->user()->id,
                'chamado_em'   => now(),
            ]);

        if (! $alterados) {
            return back()->withErrors(['acao' => 'Este paciente já foi chamado por outro profissional.']);
        }

        return redirect()->route('painel.fila.mostrar', $triagem)->with('ok', 'Paciente chamado.');
    }

    public function finalizar(Request $request, Triagem $triagem)
    {
        $alterados = Triagem::whereKey($triagem->id)
            ->where('status', 'em_atendimento')
            ->where('atendido_por', $request->user()->id)
            ->update([
                'status'        => 'finalizada',
                'finalizado_em' => now(),
            ]);

        if (! $alterados) {
            return back()->withErrors(['acao' => 'Só quem chamou o paciente pode finalizar o atendimento.']);
        }

        return redirect()->route('painel.fila.index')->with('ok', 'Atendimento finalizado.');
    }

    private function dadosDaFila(): array
    {
        $base = fn () => Triagem::with(['paciente', 'sintomas', 'atendente'])->whereNotNull('cor');

        // Alerta imediato primeiro, depois por gravidade, depois por ordem de chegada
        $aguardando = $base()
            ->where('status', 'aguardando')
            ->orderByDesc('alerta_imediato')
            ->orderByRaw("FIELD(cor, 'laranja', 'amarelo', 'verde', 'azul')")
            ->orderBy('created_at')
            ->get();

        $emAtendimento = $base()
            ->where('status', 'em_atendimento')
            ->orderBy('chamado_em')
            ->get();

        return compact('aguardando', 'emAtendimento');
    }
}