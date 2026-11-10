<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    private const PERFIS = ['medico' => 'Médico', 'ti' => 'Técnico de TI', 'admin' => 'Administrador'];

    public function index()
    {
        $usuarios = User::withCount('triagensAtendidas')
            ->orderByDesc('ativo')
            ->orderBy('name')
            ->get();

        return view('painel.usuarios.index', ['usuarios' => $usuarios, 'perfis' => self::PERFIS]);
    }

    public function create()
    {
        return view('painel.usuarios.form', $this->dadosForm(new User(['perfil' => 'medico', 'ativo' => true])));
    }

    public function store(Request $request)
    {
        $dados = $this->validar($request);
        $senha = Str::password(12, symbols: false);

        $usuario = User::create([
            'name'     => trim($dados['name']),
            'email'    => Str::lower(trim($dados['email'])),
            'perfil'   => $dados['perfil'],
            'ativo'    => true,
            'password' => $senha, // o cast "hashed" do model faz o hash
        ]);

        return redirect()->route('painel.usuarios.index')
            ->with('ok', 'Usuário criado.')
            ->with('senha_temporaria', ['nome' => $usuario->name, 'email' => $usuario->email, 'senha' => $senha]);
    }

    public function edit(User $usuario)
    {
        return view('painel.usuarios.form', $this->dadosForm($usuario));
    }

    public function update(Request $request, User $usuario)
    {
        $ehEu = $usuario->is($request->user());
        $dados = $this->validar($request, $usuario, $ehEu);

        if (! $ehEu && $dados['perfil'] !== $usuario->perfil && $this->temAtendimentoAberto($usuario)) {
            return back()->withInput()
                ->withErrors(['acao' => 'Este usuário tem um atendimento em andamento. Peça para finalizá-lo antes de mudar o perfil.']);
        }

        $usuario->name = trim($dados['name']);
        $usuario->email = Str::lower(trim($dados['email']));

        // O próprio admin não muda o seu perfil
        if (! $ehEu) {
            $usuario->perfil = $dados['perfil'];
        }

        $usuario->save();

        return redirect()->route('painel.usuarios.index')->with('ok', 'Usuário atualizado.');
    }

    public function redefinirSenha(User $usuario)
    {
        $senha = Str::password(12, symbols: false);

        $usuario->password = $senha;
        $usuario->setRememberToken(Str::random(60)); // invalida o "manter conectado"
        $usuario->save();

        return redirect()->route('painel.usuarios.index')
            ->with('ok', 'Senha redefinida.')
            ->with('senha_temporaria', ['nome' => $usuario->name, 'email' => $usuario->email, 'senha' => $senha]);
    }

    public function alternar(Request $request, User $usuario)
    {
        if ($usuario->is($request->user())) {
            return back()->withErrors(['acao' => 'Você não pode desativar a sua própria conta.']);
        }

        if ($usuario->ativo && $this->temAtendimentoAberto($usuario)) {
            return back()->withErrors(['acao' => 'Este usuário tem um atendimento em andamento. Peça para finalizá-lo antes de desativar a conta.']);
        }

        $usuario->update(['ativo' => ! $usuario->ativo]);

        return back()->with('ok', $usuario->ativo ? 'Conta reativada.' : 'Conta desativada.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->is($request->user())) {
            return back()->withErrors(['acao' => 'Você não pode excluir a sua própria conta.']);
        }

        if ($usuario->triagensAtendidas()->exists()) {
            return back()->withErrors(['acao' => 'Este usuário já atendeu pacientes e não pode ser excluído. Desative a conta para preservar o histórico.']);
        }

        $usuario->delete();

        return back()->with('ok', 'Usuário excluído.');
    }

    // ------------------------------------------------------------------

    private function validar(Request $request, ?User $usuario = null, bool $ehEu = false): array
    {
        return $request->validate([
            'name'   => ['required', 'string', 'min:3', 'max:150'],
            'email'  => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario?->id)],
            'perfil' => [$ehEu ? 'nullable' : 'required', Rule::in(array_keys(self::PERFIS))],
        ], [
            'name.required'   => 'Informe o nome.',
            'name.min'        => 'O nome está muito curto.',
            'email.required'  => 'Informe o e-mail.',
            'email.email'     => 'Informe um e-mail válido.',
            'email.unique'    => 'Já existe um usuário com este e-mail.',
            'perfil.required' => 'Escolha o perfil.',
            'perfil.in'       => 'Perfil inválido.',
        ]);
    }

    private function temAtendimentoAberto(User $usuario): bool
    {
        return Triagem::where('atendido_por', $usuario->id)->where('status', 'em_atendimento')->exists();
    }

    private function dadosForm(User $usuario): array
    {
        return ['usuario' => $usuario, 'perfis' => self::PERFIS];
    }
}