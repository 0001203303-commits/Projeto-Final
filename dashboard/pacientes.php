<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Pacientes | SMART TRIAGE</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
             --brand-gradient: linear-gradient(135deg, #008080 0%, #09746b 100%);
            --primary: #2563eb;
            --secondary: #0f172a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --bg-main: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius: 16px;
            --shadow-sm: 0 4px 6px -1px rgba(0,0,0,0.05);
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

        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .header-actions h1 { font-size: 30px; font-weight: 800; letter-spacing: -1px; }
        .header-actions p { color: var(--text-muted); font-weight: 500; }

        /* Barra de Busca */
        .search-box { position: relative; width: 400px; }
        .search-box input { 
            width: 100%; padding: 14px 20px 14px 45px; border-radius: 12px; 
            border: 1px solid var(--border); outline: none; transition: var(--transition);
            font-weight: 500; font-size: 14px;
        }
        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; }

        /* --- STATS MINI --- */
        .stats-mini { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 35px; }
        .stat-item { 
            background: white; padding: 20px; border-radius: 14px; 
            border: 1px solid var(--border); display: flex; align-items: center; gap: 15px;
            transition: var(--transition);
        }
        .stat-item:hover { transform: translateY(-5px); box-shadow: var(--shadow-sm); }
        .stat-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
        .stat-item small { color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .stat-item p { font-size: 22px; font-weight: 800; color: var(--secondary); }

        /* --- TABELA --- */
        .content-box { 
            background: white; border-radius: var(--radius); 
            border: 1px solid var(--border); overflow: hidden; 
            box-shadow: var(--shadow-sm); 
        }
        table { width: 100%; border-collapse: collapse; }
        th { 
            background: #f8fafc; text-align: left; font-size: 12px; 
            color: var(--text-muted); padding: 20px; text-transform: uppercase; 
            font-weight: 700; border-bottom: 1px solid var(--border);
        }
        td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: var(--secondary); font-weight: 500; }
        
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: var(--transition); }
        tbody tr:hover { background-color: #fbfcfe; }

        /* Prioridades Protocolo Manchester */
        .badge { 
            padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; 
            color: white; text-align: center; display: inline-flex; align-items: center; gap: 5px;
        }
        .bg-emergencia { background-color: var(--danger); box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }
        .bg-urgente { background-color: var(--warning); color: #78350f; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2); }
        .bg-pouco-urgente { background-color: var(--success); box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
        .bg-nao-urgente { background-color: var(--info); box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); }

        /* Botão de Ação */
        .btn-view { 
            color: var(--primary); background: var(--primary-light); border: 1px solid #dbeafe; 
            padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: var(--transition);
            display: flex; align-items: center; justify-content: center;
        }
        .btn-view:hover { background: var(--primary); color: white; transform: scale(1.1); }

        .protocol-id { font-weight: 800; color: var(--primary); font-family: monospace; font-size: 15px; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2><i data-lucide="activity"></i> <span>SMART_<b>TRIAGE</b></span></h2>
        
        <nav>
            <a href="index.php" class="nav-item"><i data-lucide="layout-dashboard"></i> <span>Visão Geral</span></a>
            <a href="status_totens.php" class="nav-item"><i data-lucide="monitor"></i> <span>Status Totens</span></a>
            <a href="pacientes.php" class="nav-item active"><i data-lucide="users"></i> <span>Pacientes</span></a>
            <a href="alertas.php" class="nav-item"><i data-lucide="alert-triangle"></i> <span>Alertas Ativos</span></a>
        </nav>
        
        <a href="configuracoes.php" class="nav-item" style="margin-top: auto;">
            <i data-lucide="settings"></i> <span>Configurações</span>
        </a>
    </aside>

    <main class="main-content">
        <div class="header-actions">
            <div>
                <h1>Gestão de Pacientes</h1>
                <p>Controle de fila e classificação de risco (Manchester).</p>
            </div>
            <div class="search-box">
                <i data-lucide="search"></i>
                <input type="text" id="inputBusca" placeholder="Buscar por nome ou protocolo..." onkeyup="filtrarTabela()">
            </div>
        </div>

        <div class="stats-mini">
            <div class="stat-item">
                <div class="stat-icon" style="background: var(--danger);"><i data-lucide="flame"></i></div>
                <div><small>Emergência</small><p>03</p></div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" style="background: var(--warning);"><i data-lucide="clock"></i></div>
                <div><small>Aguardando</small><p>12</p></div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" style="background: var(--success);"><i data-lucide="check-check"></i></div>
                <div><small>Atendidos</small><p>45</p></div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" style="background: var(--primary);"><i data-lucide="user-plus"></i></div>
                <div><small>Total Hoje</small><p>60</p></div>
            </div>
        </div>

        <div class="content-box">
            <table id="tabelaPacientes">
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Paciente</th>
                        <th>Idade</th>
                        <th>Chegada</th>
                        <th>Prioridade</th>
                        <th>Sintomas</th>
                        <th style="text-align: center;">Ficha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="protocol-id">#8821</span></td>
                        <td><strong>João Ricardo Silva</strong></td>
                        <td>45</td>
                        <td>14:30</td>
                        <td><span class="badge bg-emergencia"><i data-lucide="alert-circle" style="width:12px"></i> EMERGÊNCIA</span></td>
                        <td>Dor torácica intensa, falta de ar</td>
                        <td align="center"><button class="btn-view" title="Ver Prontuário"><i data-lucide="file-text" style="width:18px;"></i></button></td>
                    </tr>
                    <tr>
                        <td><span class="protocol-id">#8825</span></td>
                        <td><strong>Maria das Dores</strong></td>
                        <td>68</td>
                        <td>14:45</td>
                        <td><span class="badge bg-urgente"><i data-lucide="alert-triangle" style="width:12px"></i> URGENTE</span></td>
                        <td>Febre alta (39.5°C), calafrios</td>
                        <td align="center"><button class="btn-view" title="Ver Prontuário"><i data-lucide="file-text" style="width:18px;"></i></button></td>
                    </tr>
                    <tr>
                        <td><span class="protocol-id">#8829</span></td>
                        <td><strong>Ana Julia Costa</strong></td>
                        <td>22</td>
                        <td>15:05</td>
                        <td><span class="badge bg-pouco-urgente"><i data-lucide="shield-check" style="width:12px"></i> POUCO URGENTE</span></td>
                        <td>Dor de garganta, tosse seca</td>
                        <td align="center"><button class="btn-view" title="Ver Prontuário"><i data-lucide="file-text" style="width:18px;"></i></button></td>
                    </tr>
                    <tr>
                        <td><span class="protocol-id">#8832</span></td>
                        <td><strong>Pedro Henrique Alencar</strong></td>
                        <td>34</td>
                        <td>15:10</td>
                        <td><span class="badge bg-nao-urgente"><i data-lucide="info" style="width:12px"></i> NÃO URGENTE</span></td>
                        <td>Renovação de receita médica</td>
                        <td align="center"><button class="btn-view" title="Ver Prontuário"><i data-lucide="file-text" style="width:18px;"></i></button></td>
                    </tr>
                    <tr>
                        <td><span class="protocol-id">#8835</span></td>
                        <td><strong>Carlos Eduardo Magno</strong></td>
                        <td>50</td>
                        <td>15:20</td>
                        <td><span class="badge bg-emergencia"><i data-lucide="alert-circle" style="width:12px"></i> EMERGÊNCIA</span></td>
                        <td>Suspeita de AVC, dormência</td>
                        <td align="center"><button class="btn-view" title="Ver Prontuário"><i data-lucide="file-text" style="width:18px;"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        
        lucide.createIcons();

        
        function filtrarTabela() {
            const input = document.getElementById("inputBusca");
            const filter = input.value.toUpperCase();
            const table = document.getElementById("tabelaPacientes");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                const tdProtocolo = tr[i].getElementsByTagName("td")[0];
                const tdNome = tr[i].getElementsByTagName("td")[1];
                
                if (tdNome || tdProtocolo) {
                    const txtProtocolo = tdProtocolo.textContent || tdProtocolo.innerText;
                    const txtNome = tdNome.textContent || tdNome.innerText;
                    
                    if (txtNome.toUpperCase().includes(filter) || txtProtocolo.toUpperCase().includes(filter)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
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
		
		if (localStorage.getItem('userProfile') === 'medico') {
    
    document.querySelectorAll('.coluna-medica').forEach(el => el.style.display = 'table-cell');
}
		
    </script>
</body>
</html>