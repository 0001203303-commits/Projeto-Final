@extends('admin.index')
@section('conteudo')

<main class="main-content">
    <header class="header">
        <div>
            <h1>Status dos Totens</h1>
            <p>Monitoramento de hardware, conectividade e suprimentos.</p>
        </div>
    </header>

    <div class="content-box">
        <h3>Uptime Semanal (%)</h3>
        <div style="height: 250px;">
            <canvas id="graficoPerformanceTotem"></canvas>
        </div>
    </div>

    <div class="content-box">
        <table id="tabelaTotens">
            <thead>
                <tr>
                    <th>Dispositivo</th>
                    <th>Endereço IP</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Totem 01 - Recepção</strong></td>
                    <td>192.168.1.50</td>
                    <td><span class="badge badge-online">● ONLINE</span></td>
                </tr>
                <tr>
                    <td><strong>Totem 04 - Corredor B</strong></td>
                    <td>192.168.1.53</td>
                    <td><span class="badge badge-offline">● OFFLINE</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('graficoPerformanceTotem').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Totem 01', 'Totem 02', 'Totem 03', 'Totem 04'],
                datasets: [{
                    label: 'Uptime %',
                    data: [100, 98, 100, 45],
                    backgroundColor: ['#10b981', '#10b981', '#10b981', '#ef4444'],
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    });
</script>

@endsection 