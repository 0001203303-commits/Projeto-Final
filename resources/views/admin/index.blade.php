<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SMART TRIAGE</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
        .sidebar { width: 280px; background-color: var(--secondary); color: white; padding: 30px 20px; display: flex; flex-direction: column; height: 100vh; position: fixed; z-index: 100; }
        .sidebar h2 { font-size: 20px; font-weight: 800; margin-bottom: 50px; display: flex; align-items: center; gap: 12px; padding-left: 10px; }
        .sidebar h2 i { background: var(--brand-gradient); padding: 8px; border-radius: 10px; color: white; }
        .sidebar h2 span { color: #15CEC2; }
        .nav-item { padding: 14px 18px; margin-bottom: 10px; border-radius: 12px; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; font-weight: 600; }
        .nav-item:hover { background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(8px); }
        .nav-item.active { background: var(--brand-gradient); color: white; box-shadow: 0 10px 15px rgba(37, 99, 235, 0.3); }
        .main-content { flex: 1; padding: 40px; margin-left: 280px; width: calc(100% - 280px); }
        
        /* Estrutura de Cabeçalho Geral */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .header h1 { font-size: 32px; font-weight: 800; letter-spacing: -1px; }
        .header p { color: var(--text-muted); font-weight: 500; }
        .content-box, .settings-card { background: white; padding: 30px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 25px; }
        
        /* --- NOVO: CSS de Formulários e Configurações Reativado --- */
        .settings-container { max-width: 900px; display: flex; flex-direction: column; gap: 20px; }
        .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 25px; border-bottom: 1px solid var(--border); padding-bottom: 15px; }
        .section-header i { color: var(--primary); width: 22px; height: 22px; }
        .section-header h3 { font-size: 18px; font-weight: 700; color: var(--secondary); }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 13px; font-weight: 600; color: var(--text-muted); }
        .form-group input, .form-group select, .control-row select { padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--border); background: #f8fafc; font-size: 14px; font-weight: 500; color: var(--text-main); transition: var(--transition); outline: none; }
        .form-group input:focus, .form-group select:focus, .control-row select:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(0, 128, 128, 0.1); }
        
        .control-row { display: flex; justify-content: space-between; align-items: center; gap: 30px; }
        .control-info h4 { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .control-info p { font-size: 13px; color: var(--text-muted); font-weight: 500; }
        
        .footer-actions { display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .btn { padding: 12px 24px; border-radius: var(--radius-md); font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; border: none; transition: var(--transition); }
        .btn-primary { background: var(--brand-gradient); color: white; box-shadow: 0 4px 12px rgba(0, 128, 128, 0.2); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 128, 128, 0.3); }
        .btn-secondary { background: #f1f5f9; color: var(--text-muted); }
        .btn-secondary:hover { background: #e2e8f0; color: var(--text-main); }
        /* --------------------------------------------------------- */

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; color: var(--text-muted); padding-bottom: 15px; text-transform: uppercase; border-bottom: 1px solid var(--border); }
        td { padding: 16px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 600; }
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; }
        .badge-online { background: #dcfce7; color: #166534; }
        .badge-offline { background: #fee2e2; color: #991b1b; }
        /* --- CSS da Visão Geral Reativado --- */
        #relogio { display: flex; align-items: center; gap: 8px; background: white; padding: 10px 18px; border-radius: var(--radius-md); border: 1px solid var(--border); font-weight: 700; color: var(--secondary); box-shadow: 0 2px 4px rgba(0,0,0,0.01); }
        #relogio i { color: var(--primary); width: 18px; }

        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01); display: flex; flex-direction: column; gap: 8px; }
        .card h3 { font-size: 14px; color: var(--text-muted); font-weight: 600; }
        .card .value { font-size: 32px; font-weight: 800; color: var(--secondary); letter-spacing: -1px; }
        .card .indicator { font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
        .card .indicator i { width: 14px; height: 14px; }

        .dashboard-row { display: grid; grid-template-columns: 1.6fr 1.4fr; gap: 25px; align-items: start; }
        @media (max-width: 1100px) { .dashboard-row { grid-template-columns: 1fr; } }
        
        .section-box { background: white; padding: 25px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .section-title { font-size: 16px; font-weight: 700; color: var(--secondary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-title i { width: 20px; height: 20px; }
        .chart-container { height: 280px; width: 100%; position: relative; }
        form {
    max-width: 100%;
    padding: 16px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--border, #e2e8f0);
    font-family: inherit;
}

/* Grid de 3 colunas para campos curtos e 2 para os demais */
.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin: 0; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 0; }

form div { margin-bottom: 10px; display: flex; flex-direction: column; }

form label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 4px;
    text-transform: uppercase;
}

form input, form select, form textarea {
    width: 100%;
    padding: 6px 10px; /* Padding reduzido */
    font-size: 0.85rem;
    color: #1e293b;
    background: #ffffff;
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 6px;
    box-sizing: border-box;
}

form input:focus, form select:focus, form textarea:focus {
    outline: none;
    border-color: #3b82f6;
}

form textarea { min-height: 50px; resize: vertical; }

/* Botões alinhados à direita e menores */
.form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; }

form button {
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    border: none;
}

form button[type="submit"] { background: #10b981; color: #fff; }
form button[type="submit"]:hover { background: #059669; }
form .btn-cancelar { background: #f1f5f9; color: #475569; border: 1px solid var(--border, #e2e8f0); }

@media (max-width: 600px) {
    .form-grid-3, .form-grid-2 { grid-template-columns: 1fr; gap: 0; }
}
.form-row-expanded form {
    max-width: 100%;
    margin: 0;
    padding: 10px;
    background: transparent;
    font-family: inherit;
}

.form-row-expanded .form-grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 12px;
    margin: 0;
}

.form-row-expanded .form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin: 0;
}

.form-row-expanded form div {
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
}

.form-row-expanded form label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-row-expanded form input,
.form-row-expanded form select,
.form-row-expanded form textarea {
    width: 100%;
    padding: 6px 10px;
    font-size: 0.85rem;
    color: #1e293b;
    background: #ffffff;
    border: 1px solid var(--border, #cbd5e1);
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.form-row-expanded form input:focus,
.form-row-expanded form select:focus,
.form-row-expanded form textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-row-expanded form textarea {
    min-height: 54px;
    resize: vertical;
    font-family: inherit;
}

.form-row-expanded .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 12px;
    margin-bottom: 0;
    flex-direction: row;
}

.form-row-expanded form button {
    padding: 6px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    border: none;
    transition: background-color 0.15s;
}

.form-row-expanded form button[type="submit"] {
    background: #10b981;
    color: #ffffff;
}

.form-row-expanded form button[type="submit"]:hover {
    background: #059669;
}

.form-row-expanded .btn-cancelar {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid var(--border, #e2e8f0);
}

.form-row-expanded .btn-cancelar:hover {
    background: #e2e8f0;
}

@media (max-width: 768px) {
    .form-row-expanded .form-grid-3,
    .form-row-expanded .form-grid-2 {
        grid-template-columns: 1fr;
        gap: 0;
    }
}
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2><i data-lucide="activity"></i> <span> SMART_<b>TRIAGE</b></span></h2>
        <nav>
            <a href="/admin/index" class="nav-item {{ Request::is('admin/index') ? 'active' : '' }}"><i data-lucide="layout-dashboard"></i> <span>Visão Geral</span></a>
            <a href="/admin/status_totens" class="nav-item {{ Request::is('admin/status_totens') ? 'active' : '' }}"><i data-lucide="monitor"></i> <span>Status Totens</span></a>
            <a href="/admin/pacientes" class="nav-item {{ Request::is('admin/pacientes') ? 'active' : '' }}"><i data-lucide="users"></i> <span>Pacientes</span></a>
            <a href="/admin/alertas" class="nav-item {{ Request::is('admin/alertas') ? 'active' : '' }}"><i data-lucide="alert-triangle"></i> <span>Alertas Ativos</span></a>
            <a href="/admin/configuracoes" class="nav-item {{ Request::is('admin/configuracoes') ? 'active' : '' }}"><i data-lucide="settings"></i> <span>Configurações</span></a>
        </nav>
    </aside>

    @yield('conteudo')

    <script>
        // Executa em TODAS as páginas filhas automaticamente
        lucide.createIcons();

        function aplicarRestricoesGlobais() {
            const perfil = localStorage.getItem('userProfile');
            const menuTotens = document.querySelector('a[href="/admin/status_totens"]');
            const menuAlertas = document.querySelector('a[href="/admin/alertas"]');
            
            if (perfil === 'medico') {
                if (menuTotens) menuTotens.style.display = 'none';
                if (menuAlertas) menuAlertas.style.display = 'none';
            } else {
                if (menuTotens) menuTotens.style.display = 'flex';
                if (menuAlertas) menuAlertas.style.display = 'flex';
            }
        }
        document.addEventListener('DOMContentLoaded', aplicarRestricoesGlobais);
    </script>
</body>
</html>