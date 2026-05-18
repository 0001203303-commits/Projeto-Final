<?php
// 1. CONEXÃO COM O BANCO DE DADOS
$host = "localhost";
$user = "root";
$pass = "";
$db   = "smart_triage";

// Tentativa de conexão
$conn = mysqli_connect($host, $user, $pass, $db);

// Caso queira testar sem banco de dados ainda, podemos criar dados fictícios
// Se a conexão falhar, usaremos valores padrão para o código não quebrar

    
if ($conn) {
    $total_triagens = "342"; 
    $tempo_medio = "08:45";
    $terminais_ativos = "07";
    $alertas_criticos = "02";
    $dados_grafico = [20, 55, 80, 45, 65, 95, 30]; // Dados fictícios
} else {
    // Busca dados reais do banco
    $res_triagens = mysqli_query($conn, "SELECT COUNT(*) as total FROM triagens WHERE data = CURDATE()");
    $row = mysqli_fetch_assoc($res_triagens);
    $total_triagens = $row['total'];

    // Busca status dos totens
    $res_totens = mysqli_query($conn, "SELECT * FROM totens");
    
    // Busca dados para o gráfico (Ex: últimas 7 contagens)
    $res_grafico = mysqli_query($conn, "SELECT contagem FROM historico_triagem ORDER BY hora ASC LIMIT 7");
    $dados_grafico = [];
    while($r = mysqli_fetch_assoc($res_grafico)) { $dados_grafico[] = $r['contagem']; }
}
?>

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
        /* [SEU CSS ORIGINAL MANTIDO AQUI] */
        :root {
            --brand-gradient: linear-gradient(135deg, #008080 0%, #09746b 100%);
            --primary: #008080; --secondary: #0f172a; --success: #10b981;
            --warning: #f59e0b; --danger: #ef4444; --bg-main: #f8fafc;
            --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0;
            --radius: 16px; --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
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
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .header h1 { font-size: 32px; font-weight: 800; letter-spacing: -1px; }
        #relogio { background: white; padding: 12px 20px; border-radius: 14px; border: 1px solid var(--border); box-shadow: var(--shadow); font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 40px; }
        .card { background: white; padding: 25px; border-radius: var(--radius); border: 1px solid var(--border); transition: var(--transition); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .card:hover { transform: translateY(-5px); box-shadow: var(--shadow); }
        .card h3 { font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .card .value { font-size: 32px; font-weight: 800; margin: 10px 0; color: var(--secondary); }
        .card .indicator { font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px; }
        .dashboard-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; min-height: 400px; }
        .section-box { background: white; padding: 30px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .section-title { display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 800; margin-bottom: 25px; }
        .chart-container { height: 300px; width: 100%; position: relative; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; color: var(--text-muted); padding-bottom: 15px; text-transform: uppercase; border-bottom: 1px solid var(--border); }
        td { padding: 16px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 600; }
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; }
        .badge-online { background: #dcfce7; color: #166534; }
        .badge-offline { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2><i data-lucide="activity"></i> <span> SMART_<b>TRIAGE</b></span></h2>
        <nav>
            <a href="index.php" class="nav-item active"><i data-lucide="layout-dashboard"></i> <span>Visão Geral</span></a>
            <a href="status_totens.php" class="nav-item"><i data-lucide="monitor"></i> <span>Status Totens</span></a>
            <a href="pacientes.php" class="nav-item"><i data-lucide="users"></i> <span>Pacientes</span></a>
            <a href="alertas.php" class="nav-item"><i data-lucide="alert-triangle"></i> <span>Alertas Ativos</span></a>

        </nav>
    </aside>

    <main class="main-content">
        <header class="header">
            <div>
                <h1>Visão Geral</h1>
                <p style="color: var(--text-muted); font-weight: 500;">Monitoramento central de triagens.</p>
            </div>
            <div id="relogio">
                <i data-lucide="calendar"></i>
                <span id="relogio-texto">00:00:00</span>
            </div>
        </header>

        <section class="cards-grid">
            <div class="card">
                <h3>Triagens Hoje</h3>
                <div class="value"><?php echo $total_triagens; ?></div>
                <div class="indicator" style="color: var(--success)"><i data-lucide="trending-up"></i> +12.5%</div>
            </div>
            <div class="card">
                <h3>Tempo Médio</h3>
                <div class="value"><?php echo $tempo_medio; ?> <small style="font-size: 14px; color: var(--text-muted)">min</small></div>
                <div class="indicator" style="color: var(--success)"><i data-lucide="check-circle"></i> Dentro da meta</div>
            </div>
            <div class="card">
                <h3>Terminais Ativos</h3>
                <div class="value"><?php echo $terminais_ativos; ?> <span style="font-size: 18px; color: var(--text-muted)">/ 08</span></div>
            </div>
            <div class="card">
                <h3>Alertas Críticos</h3>
                <div class="value" style="color: var(--danger)"><?php echo $alertas_criticos; ?></div>
            </div>
        </section>

        <div class="dashboard-row">
            <section class="section-box">
                <div class="section-title"><i data-lucide="bar-chart-3" style="color: var(--primary)"></i> Fluxo de Triagem (24h)</div>
                <div class="chart-container">
                    <canvas id="graficoTriagem"></canvas>
                </div>
            </section>

            <section class="section-box">
                <div class="section-title"><i data-lucide="server" style="color: var(--primary)"></i> Status dos Terminais</div>
                <table>
                    <thead>
                        <tr><th>Localização</th><th>Insumo</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (isset($res_totens) && mysqli_num_rows($res_totens) > 0) {
                            while($totem = mysqli_fetch_assoc($res_totens)) {
                                $classe = ($totem['status'] == 'ONLINE') ? 'badge-online' : 'badge-offline';
                                echo "<tr>
                                        <td>{$totem['localizacao']}</td>
                                        <td>{$totem['insumo']}%</td>
                                        <td><span class='badge {$classe}'>{$totem['status']}</span></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Nenhum totem encontrado no banco.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <script>
        lucide.createIcons();

        // Relógio simples
        function atualizarRelogio() {
            const agora = new Date();
            document.getElementById('relogio-texto').innerText = agora.toLocaleTimeString('pt-BR');
        }
        setInterval(atualizarRelogio, 1000);

        // Gráfico recebendo dados do PHP
        const ctx = document.getElementById('graficoTriagem').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['08h', '10h', '12h', '14h', '16h', '18h', '20h'],
                datasets: [{
                    label: 'Triagens',
                    data: <?php echo json_encode($dados_grafico); ?>, // Converte array PHP para JS
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</body>
</html>