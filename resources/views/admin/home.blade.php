@extends('admin.index')
@section('conteudo')

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
            <div class="value"><?php echo $total_triagens ?? 0; ?></div>
            <div class="indicator" style="color: var(--success)"><i data-lucide="trending-up"></i> +12.5%</div>
        </div>
        <div class="card">
            <h3>Tempo Médio</h3>
            <div class="value"><?php echo $tempo_medio ?? 0; ?> <small style="font-size: 14px; color: var(--text-muted)">min</small></div>
            <div class="indicator" style="color: var(--success)"><i data-lucide="check-circle"></i> Dentro da meta</div>
        </div>
        <div class="card">
            <h3>Terminais Ativos</h3>
            <div class="value"><?php echo $terminais_ativos ?? 0; ?> <span style="font-size: 18px; color: var(--text-muted)">/ 08</span></div>
        </div>
        <div class="card">
            <h3>Alertas Críticos</h3>
            <div class="value" style="color: var(--danger)"><?php echo $alertas_criticos ?? 0; ?></div>
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
                    <tr>
                        <th>Localização</th>
                        <th>Insumo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (isset($res_totens) && mysqli_num_rows($res_totens) > 0) {
                        while($totem = mysqli_fetch_assoc($res_totens)) {
                            $classe = ($totem['status'] == 'ONLINE') ? 'badge-online' : 'badge-offline';
                            echo "<tr>
                                    <td><strong>{$totem['localizacao']}</strong></td>
                                    <td>{$totem['insumo']}%</td>
                                    <td><span class='badge {$classe}'>● {$totem['status']}</span></td>
                                  </tr>";
                        }
                    } else {
                        // Linhas de simulação caso sua query de banco ainda não esteja injetada na rota do Laravel
                        echo "<tr><td>Recepção Principal</td><td>85%</td><td><span class='badge badge-online'>● ONLINE</span></td></tr>";
                        echo "<tr><td>Triagem Infantil</td><td>12%</td><td><span class='badge badge-online'>● ONLINE</span></td></tr>";
                        echo "<tr><td>Corredor B</td><td>0%</td><td><span class='badge badge-offline'>● OFFLINE</span></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</main>

<script>
    // Executa assim que a estrutura da página carregar
    window.addEventListener('DOMContentLoaded', () => {
        // Inicializa os ícones específicos desta view
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Relógio digital em tempo real
        function atualizarRelogio() {
            const agora = new Date();
            const elemento = document.getElementById('relogio-texto');
            if (elemento) elemento.innerText = agora.toLocaleTimeString('pt-BR');
        }
        atualizarRelogio();
        setInterval(atualizarRelogio, 1000);

        // Renderização do gráfico de fluxo
        const ctx = document.getElementById('graficoTriagem').getContext('2d');
        
        // Evita erro caso a variável vinda do controller não exista
        const dadosGrafico = <?php echo json_encode($dados_grafico ?? [12, 19, 3, 5, 2, 3, 10]); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['08h', '10h', '12h', '14h', '16h', '18h', '20h'],
                datasets: [{
                    label: 'Triagens',
                    data: dadosGrafico,
                    borderColor: '#008080',
                    backgroundColor: 'rgba(0, 128, 128, 0.06)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#008080'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>


@endsection