 @extends('admin.index')
@section('conteudo')

    <div class="main-content">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Status dos Totens</h1>
                <p>Monitoramento técnico e nível de insumos.</p>
            </div>
            <button class="btn-refresh" onclick="recarregarDados()">
                <i data-lucide="rotate-cw"></i> Atualizar Status
            </button>
        </div>

        <div class="content-box">
            <h3 style="margin-bottom: 15px;">Disponibilidade dos Totens (Últimas 24h)</h3>
            <div class="chart-container">
                <canvas id="graficoPerformanceTotem"></canvas>
            </div>
        </div>

        <div class="content-box">
            <h3 style="margin-bottom: 15px;">Listagem de Dispositivos</h3>
            <table id="tabelaTotens">
                <thead>
                    <tr>
                        <th>Nome do Dispositivo</th>
                        <th>IP</th>
                        <th>Nível de Papel</th>
                        <th>Última Resposta</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Totem 01 - Recepção</td>
                        <td>192.168.1.50</td>
                        <td>85%</td>
                        <td>Há 2 min</td>
                        <td><span class="badge badge-online">ONLINE</span></td>
                    </tr>
                    <tr>
                        <td>Totem 02 - Emergência</td>
                        <td>192.168.1.51</td>
                        <td>12%</td>
                        <td>Há 1 min</td>
                        <td><span class="badge badge-online">ONLINE</span></td>
                    </tr>
                    <tr>
                        <td>Totem 04 - Corredor B</td>
                        <td>192.168.1.53</td>
                        <td>0%</td>
                        <td>Há 45 min</td>
                        <td><span class="badge badge-offline">OFFLINE</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Inicializa Ícones
        lucide.createIcons();

        // Configuração do Gráfico de Disponibilidade
        const ctx = document.getElementById('graficoPerformanceTotem').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Totem 01', 'Totem 02', 'Totem 03', 'Totem 04'],
                datasets: [{
                    label: 'Uptime (%)',
                    data: [100, 98, 100, 45], // Dados fictícios
                    backgroundColor: ['#10b981', '#10b981', '#10b981', '#ef4444'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Função de simulação de atualização
        function recarregarDados() {
            const btn = document.querySelector('.btn-refresh');
            btn.innerHTML = '<i data-lucide="loader"></i> Atualizando...';
            lucide.createIcons();
            
            setTimeout(() => {
                alert("Dados dos totens sincronizados com sucesso!");
                btn.innerHTML = '<i data-lucide="rotate-cw"></i> Atualizar Status';
                lucide.createIcons();
            }, 1500);
        }
    </script>
</body>
</html>

@endsection