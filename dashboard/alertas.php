<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Alertas | SMART TRIAGE</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
             --brand-gradient: linear-gradient(135deg, #008080 0%, #09746b 100%);
            --primary: #2563eb;
            --secondary: #0f172a;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --success: #10b981;
            --bg-light: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { display: flex; background-color: var(--bg-light); color: var(--text-main); min-height: 100vh; }

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

        .header { margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header h1 { font-size: 30px; font-weight: 800; letter-spacing: -1px; }
        .header p { color: var(--text-muted); font-weight: 500; margin-top: 5px; }

        .update-tag { 
            font-size: 12px; color: var(--text-muted); font-weight: 600; 
            display: flex; align-items: center; gap: 6px; background: white;
            padding: 8px 15px; border-radius: 20px; border: 1px solid var(--border);
        }

        /* --- FILTROS --- */
        .alert-filters { display: flex; gap: 12px; margin-bottom: 30px; }
        .filter-btn { 
            padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border); 
            background: white; cursor: pointer; font-size: 14px; font-weight: 700; 
            transition: var(--transition); color: var(--text-muted);
        }
        .filter-btn:hover { border-color: var(--primary); color: var(--primary); }
        .filter-btn.active { background: var(--secondary); color: white; border-color: var(--secondary); }

        /* --- LISTA DE ALERTAS --- */
        .alerts-list { display: flex; flex-direction: column; gap: 18px; }
        
        .alert-card { 
            background: white; border-radius: var(--radius); border: 1px solid var(--border); 
            padding: 24px; display: flex; align-items: center; gap: 24px;
            transition: var(--transition); position: relative; overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        
        .alert-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px rgba(0,0,0,0.05); }
        
        /* Faixa de Severidade */
        .severity-bar { position: absolute; left: 0; top: 0; bottom: 0; width: 6px; }
        .critical { background: var(--danger); }
        .moderate { background: var(--warning); }
        .low { background: var(--info); }

        /* Ícones dos Alertas */
        .alert-icon { 
            width: 54px; height: 54px; border-radius: 14px; 
            display: flex; align-items: center; justify-content: center; 
            flex-shrink: 0;
        }
        
        .bg-red-soft { background: #fee2e2; color: var(--danger); }
        .bg-orange-soft { background: #fef3c7; color: var(--warning); }
        .bg-blue-soft { background: #dbeafe; color: var(--info); }

        .alert-info { flex: 1; }
        .alert-info h4 { font-size: 17px; font-weight: 700; margin-bottom: 6px; color: var(--secondary); }
        .alert-info h4 span { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-left: 10px; }
        .alert-info p { font-size: 14px; color: var(--text-muted); line-height: 1.5; }

        /* Botões de Ação */
        .alert-actions { display: flex; gap: 10px; }
        
        .btn-action { 
            padding: 10px 16px; border-radius: 10px; border: 1px solid var(--border); 
            background: white; cursor: pointer; font-size: 13px; font-weight: 700; 
            display: flex; align-items: center; gap: 8px; transition: var(--transition);
        }
        
        .btn-action:hover { background: var(--bg-light); border-color: #cbd5e1; transform: scale(1.05); }
        
        .btn-resolve { 
            background: var(--success); color: white; border: none; 
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }
        .btn-resolve:hover { background: #059669; color: white; }

        /* Animação de saída */
        .fade-out { opacity: 0; transform: translateX(20px); pointer-events: none; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2><i data-lucide="activity"></i> <span>SMART_<b>TRIAGE</b></span></h2>
        
        <nav>
            <a href="index.php" class="nav-item"><i data-lucide="layout-dashboard"></i> <span>Visão Geral</span></a>
            <a href="status_totens.php" class="nav-item"><i data-lucide="monitor"></i> <span>Status Totens</span></a>
            <a href="pacientes.php" class="nav-item"><i data-lucide="users"></i> <span>Pacientes</span></a>
            <a href="alertas.php" class="nav-item active"><i data-lucide="alert-triangle"></i> <span>Alertas Ativos</span></a>
        </nav>
        
        <a href="configuracoes.php" class="nav-item" style="margin-top: auto;">
            <i data-lucide="settings"></i> <span>Configurações</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="header">
            <div>
                <h1>Central de Alertas</h1>
                <p>Monitoramento preventivo de hardware e conectividade.</p>
            </div>
            <div class="update-tag">
                <i data-lucide="refresh-cw" style="width: 14px;"></i>
                <span id="lastUpdate">Sincronizado agora</span>
            </div>
        </header>

        <div class="alert-filters">
            <button class="filter-btn active">Todos (3)</button>
            <button class="filter-btn">Críticos</button>
            <button class="filter-btn">Insumos</button>
            <button class="filter-btn">Rede</button>
        </div>

        <div class="alerts-list">
            
            <div class="alert-card">
                <div class="severity-bar critical"></div>
                <div class="alert-icon bg-red-soft"><i data-lucide="wifi-off"></i></div>
                <div class="alert-info">
                    <h4>Totem 04 Offline <span>• Há 15 minutos</span></h4>
                    <p>O dispositivo localizado no <strong>Corredor B</strong> parou de enviar sinais de atividade (Heartbeat). Verifique a conexão local.</p>
                </div>
                <div class="alert-actions">
                    <button class="btn-action" onclick="alert('Iniciando diagnóstico remoto...')"><i data-lucide="terminal"></i> Diagnosticar</button>
                    <button class="btn-action btn-resolve" onclick="removerAlerta(this)"><i data-lucide="check-circle-2"></i> Resolver</button>
                </div>
            </div>

            <div class="alert-card">
                <div class="severity-bar moderate"></div>
                <div class="alert-icon bg-orange-soft"><i data-lucide="scroll"></i></div>
                <div class="alert-info">
                    <h4>Papel Quase Acabando <span>• Há 42 minutos</span></h4>
                    <p>Totem 02 (Emergência): Nível de bobina atingiu <strong>8%</strong>. Restam aproximadamente 15 tickets de impressão.</p>
                </div>
                <div class="alert-actions">
                    <button class="btn-action"><i data-lucide="truck"></i> Solicitar Suporte</button>
                    <button class="btn-action btn-resolve" onclick="removerAlerta(this)"><i data-lucide="check-circle-2"></i> Resolver</button>
                </div>
            </div>

            <div class="alert-card">
                <div class="severity-bar low"></div>
                <div class="alert-icon bg-blue-soft"><i data-lucide="thermometer-sun"></i></div>
                <div class="alert-info">
                    <h4>Temperatura Elevada <span>• Há 1 hora</span></h4>
                    <p>Totem 01 (Recepção): CPU atingiu <strong>72°C</strong>. Recomenda-se verificar a obstrução das saídas de ar do gabinete.</p>
                </div>
                <div class="alert-actions">
                    <button class="btn-action" onclick="removerAlerta(this)"><i data-lucide="eye"></i> Marcar Ciente</button>
                </div>
            </div>

        </div>
    </main>

    <script>
        
        lucide.createIcons();

        
        function removerAlerta(btn) {
            const card = btn.closest('.alert-card');
            card.classList.add('fade-out');
            
            setTimeout(() => {
                card.style.display = 'none';
                
                
                const countBtn = document.querySelector('.filter-btn.active');
                const atual = parseInt(countBtn.innerText.match(/\d+/)) || 0;
                if (atual > 0) countBtn.innerText = `Todos (${atual - 1})`;
            }, 300);
        }

        
        setInterval(() => {
            const agora = new Date();
            document.getElementById('lastUpdate').innerText = `Atualizado às ${agora.getHours()}:${agora.getMinutes().toString().padStart(2, '0')}`;
        }, 30000);
		
		
		//função para perfilde médico
		
				function aplicarRestricoes() {
			const perfil = localStorage.getItem('userProfile');
			
		   
			const menuTotens = document.querySelector('a[href="status_totens.php"]');
			const menuAlertas = document.querySelector('a[href="alertas.php"]');
			
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
</body>
</html>