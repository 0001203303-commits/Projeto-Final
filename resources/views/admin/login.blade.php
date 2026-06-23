<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartTriage - Login</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-gradient: linear-gradient(135deg, #008080 0%, #09746b 100%);
            --primary: #008080; --secondary: #0f172a; --success: #10b981;
            --warning: #f59e0b; --danger: #ef4444; --bg-main: #f8fafc;
            --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0;
            --radius: 16px; --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --radius-lg: 20px; --radius-md: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { display: flex; background-color: var(--bg-main); color: var(--text-main); min-height: 100vh; }
        
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 20px;
            background-color: var(--bg-main);
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 40px 35px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .login-logo {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--secondary);
        }
        .login-logo i {
            background: var(--brand-gradient);
            padding: 10px;
            border-radius: var(--radius-md);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-logo span { color: #15CEC2; }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        .login-header p {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: var(--danger);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #d1fae5;
            color: var(--success);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .form-group input {
            padding: 14px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: #f8fafc;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            transition: var(--transition);
            outline: none;
        }
        .form-group input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 128, 128, 0.1);
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
            font-weight: 600;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            cursor: pointer;
        }
        .remember-me input {
            accent-color: var(--primary);
            cursor: pointer;
        }
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }
        .forgot-password:hover {
            color: #09746b;
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            transition: var(--transition);
        }
        .btn-primary {
            background: var(--brand-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.2);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 128, 128, 0.3);
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-container">
            
            <div class="login-logo">
                <i data-lucide="activity"></i>
                Smart<span>Triage</span>
            </div>

            <div class="login-header">
                <h2>Seja bem-vindo de volta</h2>
                <p>Insira suas credenciais para acessar o painel</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert-success">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf <div class="form-group">
                    <label for="email">E-mail corporativo</label>
                    <input type="email" id="email" name="email" 
                           placeholder="nome@empresa.com" 
                           value="{{ old('email') }}" 
                           required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Senha de acesso</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                </div>

                <div class="login-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Lembrar-me
                    </label>
                    <a href="#" class="forgot-password">Esqueceu a senha?</a>
                </div>

                <button type="submit" class="btn btn-primary">
                    Acessar o Painel
                    <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                </button>
            </form>

        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>