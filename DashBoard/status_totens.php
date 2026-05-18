<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status dos Totens | SMART TRIAGE</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
   :root {
    /* Cores de Marca - Baseadas em Teal */
    --brand-gradient: linear-gradient(135deg, #008080 0%, #09746b 100%);
    --primary: #008080;
    --primary-light: #2dd4bf; /* Um tom mais claro para hover/detalhes */
    
    /* Cores de Interface */
    --secondary: #0f172a; /* Mantido para contraste profundo */
    --success: #059669;    /* Ajustado para um verde mais próximo do teal */
    --danger: #e11d48;     /* Ajustado para um tom de rosa/vermelho vibrante */
    
    /* Superfícies e Texto */
    --bg-main: #f0f9f9;    /* Um fundo levemente azulado para harmonizar */
    --text-main: #0f172a;
    --text-muted: #475569;
    --border: #ccdada;     /* Bordas mais suaves que combinam com teal */
    
    /* Tokens de Design (Mantidos) */
    --radius-lg: 20px;
    --radius-md: 12px;
    --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { display: flex; background-color: var(--bg-main); color: var(--text-main); min-height: 100vh; }

        /* --- SIDEBAR --- */
        .sidebar { 
            width: 280px; background-color: var(--secondary); color: white; 
            padding: 30px 20px; display: flex; flex-direction: column; 
            height: 100vh; position: fixed; z-index: 100;
        }

        .sidebar h2 { 
            font-size: 20px; font-weight: 800; margin-bottom: 50px; 
            display: flex; align-items: center; gap: 12px; padding-left: 10px;
        }
        .sidebar h2 i { background: var(--brand-gradient); padding: 8px; border-radius: 10px; color: white; }
        .sidebar h2 span { color: #38bdf8; }
        
        .nav-item { 
            padding: 14px 18px; margin-bottom: 10px; border-radius: 12px; cursor: pointer; 
            transition: var(--transition); display: flex; align-items: center; gap: 12px; 
            color: #94a3b8; text-decoration: none; font-weight: 600;
        }
        .nav-item:hover { background: rgba(255, 255, 255, 0.05); color: white; transform: translateX(8px); }
        .nav-item.active { background: var(--brand-gradient); color: white; box-shadow: 0 10px 15px rgba(37, 99, 235, 0.3); }

        /* --- CONTEÚDO --- */
        .main-content { flex: 1; padding: 40px; margin-left: 280px; }

        .header { 
            margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; 
        }
        .header h1 { font-size: 30px; font-weight: 800; letter-spacing: -1px; }
        .header p { color: var(--text-muted); font-weight: 500; }

        /* Botão Refresh */
        .btn-refresh { 
            background: white; color: var(--secondary); border: 1px solid var(--border); 
            padding: 12px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; 
            display: flex; align-items: center; gap: 10px; transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }
        .btn-refresh:hover { background: var(--secondary); color: white; transform: translateY(-2px); }

        /* --- CONTENT BOX --- */
        .content-box { 
            background: white; padding: 30px; border-radius: var(--radius); 
            border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 30px; 
        }
        .content-box h3 { 
            font-size: 18px; font-weight: 800; margin-bottom: 25px; 
            display: flex; align-items: center; gap: 10px; 
        }

        /* Gráfico */
        .chart-container { height: 280px; width: 100%; position: relative; }

        /* --- TABELA --- */
        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; font-size: 11px; color: var(--text-muted); 
            padding: 15px; text-transform: uppercase; font-weight: 700;
            border-bottom: 2px solid #f1f5f9;
        }
        td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 600; }
        
        .ip-address { font-family: 'Courier New', Courier, monospace; color: var(--primary); font-size: 13px; }

        /* Status Badges */
        .badge { 
            padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; 
            display: inline-flex; align-items: center; gap: 6px;
        }
        .badge-online { background: #dcfce7; color: #166534; }
        .badge-offline { background: #fee2e2; color: #991b1b; }

        /* Progress Bar Papel */
        .paper-level-container { width: 140px; }
        .paper-bar-bg { width: 100%; height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-top: 5px; }
        .paper-bar-fill { height: 100%; border-radius: 10px; transition: width 1s ease; }
        
        .fill-high { background: var(--success); }
        .fill-low { background: var(--danger); }
        .fill-warning { background: var(--warning); }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2><i data-lucide="activity"></i> <span>SMART_<b>TRIAGE</b></span></h2>
        
        <nav>
            <a href="index.php" class="nav-item"><i data-lucide="layout-dashboard"></i> <span>Visão Geral</span></a>
            <a href="status_totens.php" class="nav-item active"><i data-lucide="monitor"></i> <span>Status Totens</span></a>
            <a href="pacientes.php" class="nav-item"><i data-lucide="users"></i> <span>Pacientes</span></a>
            <a href="alertas.php" class="nav-item"><i data-lucide="alert-triangle"></i> <span>Alertas Ativos</span></a>
        </nav>
        
        <a href="configuracoes.php" class="nav-item" style="margin-top: auto;">
            <i data-lucide="settings"></i> <span>Configurações</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="header">
            <div>
                <h1>Status dos Totens</h1>
                <p>Monitoramento de hardware, conectividade e suprimentos.</p>
            </div>
            <button class="btn-refresh" id="refreshBtn" onclick="recarregarDados()">
                <i data-lucide="refresh-cw"></i> Sincronizar Agora
            </button>
        </header>

        <div class="content-box">
            <h3><i data-lucide="activity" style="color: var(--primary)"></i> Disponibilidade Semanal (Uptime %)</h3>
            <div class="chart-container">
                <canvas id="graficoPerformanceTotem"></canvas>
            </div>
        </div>

        <div class="content-box">
            <h3><i data-lucide="server" style="color: var(--primary)"></i> Gerenciamento de Terminais</h3>
            <table id="tabelaTotens">
                <thead>
                    <tr>
                        <th>Dispositivo</th>
                        <th>Endereço IP</th>
                        <th>Nível de Papel</th>
                        <th>Latência (ms)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Totem 01 - Recepção</strong></td>
                        <td><span class="ip-address">192.168.1.50</span></td>
                        <td>
                            <div class="paper-level-container">
                                <span>85%</span>
                                <div class="paper-bar-bg"><div class="paper-bar-fill fill-high" style="width: 85%;"></div></div>
                            </div>
                        </td>
                        <td>12ms</td>
                        <td><span class="badge badge-online">● ONLINE</span></td>
                    </tr>
                    <tr>
                        <td><strong>Totem 02 - Emergência</strong></td>
                        <td><span class="ip-address">192.168.1.51</span></td>
                        <td>
                            <div class="paper-level-container">
                                <span style="color: var(--warning); font-weight: 800;">12%</span>
                                <div class="paper-bar-bg"><div class="paper-bar-fill fill-warning" style="width: 12%;"></div></div>
                            </div>
                        </td>
                        <td>15ms</td>
                        <td><span class="badge badge-online">● ONLINE</span></td>
                    </tr>
                    <tr>
                        <td><strong>Totem 03 - Triagem 01</strong></td>
                        <td><span class="ip-address">192.168.1.52</span></td>
                        <td>
                            <div class="paper-level-container">
                                <span>64%</span>
                                <div class="paper-bar-bg"><div class="paper-bar-fill fill-high" style="width: 64%;"></div></div>
                            </div>
                        </td>
                        <td>10ms</td>
                        <td><span class="badge badge-online">● ONLINE</span></td>
                    </tr>
                    <tr>
                        <td><strong>Totem 04 - Corredor B</strong></td>
                        <td><span class="ip-address">192.168.1.53</span></td>
                        <td>
                            <div class="paper-level-container">
                                <span style="color: var(--danger); font-weight: 800;">0%</span>
                                <div class="paper-bar-bg"><div class="paper-bar-fill fill-low" style="width: 0%;"></div></div>
                            </div>
                        </td>
                        <td>---</td>
                        <td><span class="badge badge-offline">● OFFLINE</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        
        lucide.createIcons();

        
        const ctx = document.getElementById('graficoPerformanceTotem').getContext('2d');
        
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, '#3b82f6');
        gradient.addColorStop(1, '#6366f1');

        const performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Totem 01', 'Totem 02', 'Totem 03', 'Totem 04'],
                datasets: [{
                    label: 'Uptime (%)',
                    data: [100, 98, 100, 45],
                    backgroundColor: [
                        '#10b981', 
                        '#10b981', 
                        '#10b981', 
                        '#ef4444'  
                    ],
                    borderRadius: 10,
                    barThickness: 50,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        max: 100,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { font: { weight: '600' } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    }
                }
            }
        });

        
        function recarregarDados() {
            const btn = document.getElementById('refreshBtn');
            const originalContent = btn.innerHTML;
            
            
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="spin"></i> Atualizando...';
            lucide.createIcons();

            
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalContent;
                lucide.createIcons();
                
                
                alert("📡 Sincronização concluída! Os dados de latência e insumos foram atualizados.");
            }, 1800);
        }
		
		//função para perfil de médico
		
				function aplicarRestricoes() {
			const perfil = localStorage.getItem('userProfile');
			
		  
			const menuTotens = document.querySelector('a[href="status_totens.html"]');
			const menuAlertas = document.querySelector('a[href="alertas.html"]');
			
			if (perfil === 'medico') {
				if (menuTotens) menuTotens.style.display = 'none';
				if (menuAlertas) menuAlertas.style.display = 'none';
			} else {
				if (menuTotens) menuTotens.style.display = 'flex';
				if (menuAlertas) menuAlertas.style.display = 'flex';
			}
		}


		document.addEventListener('DOMContentLoaded', aplicarRestricoes);
    </script>

    <style>
        /* Animação de Giro para o Loader */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin { animation: spin 1s linear infinite; }
    </style>
</body>
</html>