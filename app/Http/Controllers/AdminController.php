<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Pacientes;
use App\Models\Totens;

class AdminController extends Controller
{
    // Exibe a tela inicial do Admin (se logado)
    public function home()
    {
        $inicioHoje = now()->startOfDay();
        $fimHoje = now()->endOfDay();

        $triagensHoje = Pacientes::whereBetween('created_at', [$inicioHoje, $fimHoje])->get(['created_at']);
        $dadosGrafico = array_fill(0, 24, 0);

        foreach ($triagensHoje as $triagem) {
            $hora = Carbon::parse($triagem->created_at)->hour;
            $dadosGrafico[$hora]++;
        }

        $totens = Totens::query()->orderBy('nome')->get();

        return view('admin.home', [
            'total_triagens' => $triagensHoje->count(),
            'tempo_medio' => null,
            'terminais_ativos' => $totens->where('status', 'ONLINE')->count(),
            'total_terminais' => $totens->count(),
            'alertas_criticos' => Pacientes::whereBetween('created_at', [$inicioHoje, $fimHoje])
                ->whereIn('urgencia', ['EMERGÊNCIA (VERMELHO)', 'MUITO URGENTE (LARANJA)'])
                ->count(),
            'dados_grafico' => $dadosGrafico,
            'rotulos_grafico' => array_map(fn ($hora) => sprintf('%02dh', $hora), range(0, 23)),
            'totens' => $totens,
        ]);
    }

    // Exibe a tela de Login
    public function login()
        {
            // Se já estiver logado, manda para a home do admin
            if (Auth::check()) {
                return redirect()->route('admin.home');
            }
            
            // Se NÃO estiver logado, apenas mostra a tela de login (sem redirecionar!)
            return view('admin.login'); 
        }

    // Processa a tentativa de Login
    public function loginPost(Request $request)
    {
        // 1. Validação dos dados informados
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Insira um formato de e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        $remember = $request->has('remember');

        // 2. Tentativa de login
        if (Auth::attempt($credentials, $remember)) {
            // Regenera a sessão por segurança
            $request->session()->regenerate();

            // Redireciona para onde o usuário tentava ir, ou para a home do Admin
            return redirect()->intended('/admin');
        }

        // 3. Se falhar, retorna com erro
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->withInput($request->only('email', 'remember'));
    }

    // Exibe a tela de Cadastro
    public function cadastro()
    {
        return view('admin.cadastro');
    }

    // Processa a criação de um novo usuário administrador
    public function cadastroPost(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'password.confirmed' => 'As senhas informadas não coincidem.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.'
        ]);

        User::create([
            'nome' => $request->nome,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Conta criada com sucesso! Faça o login.');
    }

    // Realiza o Logout
    public function logout(Request $request)
    {
        // Desloga o usuário do Laravel
        Auth::logout();

        // Invalida a sessão atual do usuário
        $request->session()->invalidate();

        // Regenera o token CSRF para prevenir ataques de fixação de sessão
        $request->session()->regenerateToken();

        // Redireciona o usuário de volta para a tela de login com uma mensagem
        return redirect()->route('login')->with('success', 'Sessão encerrada com sucesso.');
    }
    public function configuracoes()
    {
        return view('admin.configuracoes');
    }
    public function alertas()
    {
        return view('admin.alertas');
    }
     public function triagensHoje(): JsonResponse
    {
        $total_triagens = \App\Models\Pacientes::whereDate('created_at', now()->toDateString())->count();
                return response()->json([
            'quantidade' => $total_triagens
        ]);
        
    }
}