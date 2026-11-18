<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use App\Models\TriagemComentario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ConfiguracaoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        return view('painel.configuracoes.index', [
            'usuario' => $usuario,
            'temHistorico' => $this->temHistorico($usuario),
            'ultimoAdmin' => $this->ehUltimoAdmin($usuario),
            'atendimentoAberto' => Triagem::where('atendido_por', $usuario->id)->where('status', 'em_atendimento')->exists(),
        ]);
    }

    public function atualizarPerfil(Request $request)
    {
        $usuario = $request->user();
        $novoEmail = Str::lower(trim((string) $request->input('email')));
        $trocaEmail = $novoEmail !== Str::lower($usuario->email);

        $dados = $request->validateWithBag('perfil', [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario->id)],
            'senha_atual' => [Rule::requiredIf($trocaEmail), 'nullable', 'current_password'],
        ], [
            'name.required' => 'Informe o nome.',
            'name.min' => 'O nome está muito curto.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe um usuário com este e-mail.',
            'senha_atual.required' => 'Informe a senha atual para trocar o e-mail.',
            'senha_atual.current_password' => 'A senha atual está incorreta.',
        ]);

        $usuario->name = trim($dados['name']);
        $usuario->email = Str::lower(trim($dados['email']));
        $usuario->save();

        Log::info('Dados da conta alterados pelo próprio usuário', ['usuario' => $usuario->id, 'email_alterado' => $trocaEmail]);

        return back()->with('ok', 'Informações atualizadas.');
    }

    public function atualizarSenha(Request $request)
    {
        $request->validateWithBag('senha', [
            'senha_atual' => ['required', 'current_password'],
            'nova_senha' => [
                'required', 'confirmed', 'max:72', 'different:senha_atual',
                function ($atributo, $valor, $falhar) {
                    if (mb_strlen((string) $valor) < 10) {
                        $falhar('A nova senha deve ter pelo menos 10 caracteres.');
                    } elseif (! preg_match('/\p{Lu}/u', $valor) || ! preg_match('/\p{Ll}/u', $valor)) {
                        $falhar('Use letras maiúsculas e minúsculas.');
                    } elseif (! preg_match('/\d/', $valor)) {
                        $falhar('Inclua pelo menos um número.');
                    }
                },
            ],
        ], [
            'senha_atual.required' => 'Informe a senha atual.',
            'senha_atual.current_password' => 'A senha atual está incorreta.',
            'nova_senha.required' => 'Informe a nova senha.',
            'nova_senha.confirmed' => 'A confirmação não confere com a nova senha.',
            'nova_senha.max' => 'A senha pode ter no máximo 72 caracteres.',
            'nova_senha.different' => 'A nova senha deve ser diferente da atual.',
        ]);

        $usuario = $request->user();
        $usuario->password = $request->input('nova_senha'); // o cast "hashed" faz o hash
        $usuario->setRememberToken(Str::random(60));        // invalida o "manter conectado"
        $usuario->save();

        $request->session()->regenerate();

        Log::info('Senha alterada pelo próprio usuário', ['usuario' => $usuario->id]);

        return back()->with('ok', 'Senha alterada com sucesso.');
    }

    public function atualizarAparencia(Request $request)
    {
        $dados = $request->validate([
            'tema' => ['required', 'in:claro,escuro,auto'],
        ], [
            'tema.*' => 'Escolha uma das opções de aparência.',
        ]);

        $request->user()->forceFill(['tema' => $dados['tema']])->save();

        return back()->with('ok', 'Aparência atualizada.');
    }

    public function excluir(Request $request)
    {
        $usuario = $request->user();

        $request->merge(['confirmacao' => Str::upper(trim((string) $request->input('confirmacao')))]);

        $request->validateWithBag('excluir', [
            'senha_atual' => ['required', 'current_password'],
            'confirmacao' => ['required', Rule::in(['EXCLUIR'])],
        ], [
            'senha_atual.required' => 'Informe a sua senha.',
            'senha_atual.current_password' => 'A senha está incorreta.',
            'confirmacao.required' => 'Digite EXCLUIR para confirmar.',
            'confirmacao.in' => 'Digite exatamente a palavra EXCLUIR.',
        ]);

        if (Triagem::where('atendido_por', $usuario->id)->where('status', 'em_atendimento')->exists()) {
            return back()->withErrors(['acao' => 'Você tem um atendimento em andamento. Finalize-o antes de excluir a conta.'], 'excluir');
        }

        if ($this->ehUltimoAdmin($usuario)) {
            return back()->withErrors(['acao' => 'Você é o único administrador ativo. Crie outro administrador antes de excluir esta conta.'], 'excluir');
        }

        $desativar = $this->temHistorico($usuario);

        Log::info('Conta removida pelo próprio usuário', ['usuario' => $usuario->id, 'apenas_desativada' => $desativar]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($desativar) {
            $usuario->forceFill(['ativo' => false, 'remember_token' => Str::random(60)])->save();
            $mensagem = 'Sua conta foi desativada. Como você já atendeu pacientes, ela foi preservada para manter o histórico. Um administrador pode reativá-la.';
        } else {
            $usuario->delete();
            $mensagem = 'Sua conta foi excluída.';
        }

        return redirect()->route('login')->with('status', $mensagem);
    }

    // ------------------------------------------------------------------

    private function temHistorico(User $usuario): bool
    {
        return $usuario->triagensAtendidas()->exists()
            || TriagemComentario::where('user_id', $usuario->id)->exists();
    }

    private function ehUltimoAdmin(User $usuario): bool
    {
        return $usuario->perfil === 'admin'
            && ! User::where('perfil', 'admin')->where('ativo', true)->whereKeyNot($usuario->id)->exists();
    }
}
