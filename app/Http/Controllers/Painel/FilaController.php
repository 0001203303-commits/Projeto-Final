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

        /** Devolve só os cartões, para a tela se atualizar sozinha. */
    public function parcial()
    {
        return view('painel.fila._lista', $this->dadosDaFila());
    }
       public function mostrar(Triagem $triagem)
    {
        abort_if($triagem->cor === null, 404);

        $triagem->load(['paciente', 'atendente', 'sintomas.categoria', 'respostas.pergunta.sintoma', 'comentarios']);

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
        $base = fn () => Triagem::with(['paciente', 'sintomas', 'atendente'])
            ->withCount('comentarios')
            ->whereNotNull('cor');

        $emAtendimento = $base()
            ->where('status', 'em_atendimento')
            ->orderBy('chamado_em')
            ->get();

        // Alerta imediato primeiro, depois por gravidade, depois por ordem de chegada
        $aguardando = $base()
            ->where('status', 'aguardando')
            ->orderByDesc('alerta_imediato')
            ->orderByRaw("FIELD(cor, 'laranja', 'amarelo', 'verde', 'azul')")
            ->orderBy('created_at')
            ->get();

        $atendidos = $base()
            ->where('status', 'finalizada')
            ->where('finalizado_em', '>=', now()->subDay())
            ->orderByDesc('finalizado_em')
            ->limit(50)
            ->get();

        $todas = $emAtendimento->concat($aguardando)->concat($atendidos);

        return compact('todas');
    }

        public function comentar(Request $request, Triagem $triagem)
    {
        $dados = $request->validate([
            'texto' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'texto.required' => 'Escreva o comentário.',
            'texto.min'      => 'O comentário está muito curto.',
            'texto.max'      => 'O comentário pode ter no máximo 2000 caracteres.',
        ]);

        // Só o médico que chamou o paciente comenta, e só durante o atendimento
        if ($triagem->status !== 'em_atendimento' || (int) $triagem->atendido_por !== $request->user()->id) {
            return back()->withErrors(['acao' => 'Só é possível comentar durante o atendimento, pelo médico responsável.']);
        }

        $triagem->comentarios()->create([
            'user_id'    => $request->user()->id,
            'autor_nome' => $request->user()->name,
            'texto'      => trim($dados['texto']),
        ]);

        return redirect()
            ->to(route('painel.fila.mostrar', $triagem) . '#comentarios')
            ->with('ok', 'Comentário registrado.');
    }
}