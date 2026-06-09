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
            <div class="value">{{ $total_triagens ?? 0 }}</div>
            <div class="indicator" style="color: var(--success)"><i data-lucide="trending-up"></i> +12.5%</div>
        </div>
        <div class="card">
            <h3>Tempo Médio</h3>
            <div class="value">{{ $tempo_medio ?? 0 }} <small style="font-size: 14px; color: var(--text-muted)">min</small></div>
            <div class="indicator" style="color: var(--success)"><i data-lucide="check-circle"></i> Dentro da meta</div>
        </div>
        <div class="card">
            <h3>Terminais Ativos</h3>
            <div class="value">{{ $terminais_ativos ?? 0 }} <span style="font-size: 18px; color: var(--text-muted)">/ 08</span></div>
        </div>
        <div class="card">
            <h3>Alertas Críticos</h3>
            <div class="value" style="color: var(--danger)">{{ $alertas_criticos ?? 0 }}</div>
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
                    @if(isset($res_totens) && (is_array($res_totens) || is_object($res_totens)) && count($res_totens) > 0)
                        @foreach($res_totens as $totem)
                            @php 
                                $classe = ($totem['status'] == 'ONLINE') ? 'badge-online' : 'badge-offline';
                            @endphp
                            <tr>
                                <td>{{ $totem['localizacao'] }}</td>
                                <td>{{ $totem['insumo'] }}%</td>
                                <td><span class="badge {{ $classe }}">{{ $totem['status'] }}</span></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>Recepção Principal</td>
                            <td>85%</td>
                            <td><span class="badge badge-online">ONLINE</span></td>
                        </tr>
                        <tr>
                            <td>Triagem Infantil</td>
                            <td>12%</td>
                            <td><span class="badge badge-online">ONLINE</span></td>
                        </tr>
                        <tr>
                            <td>Corredor B</td>
                            <td>0%</td>
                            <td><span class="badge badge-offline">OFFLINE</span></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </section>
    </div>
</main>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Inicializa ícones do Lucide locais
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Relógio digital dinâmico
        function atualizarRelogio() {
            const agora = new Date();
            const relogio = document.getElementById('relogio-texto');
            if (relogio) relogio.innerText = agora.toLocaleTimeString('pt-BR');
        }
        atualizarRelogio();
        setInterval(atualizarRelogio, 1000);

        // Renderização segura do gráfico Chart.js
        const ctx = document.getElementById('graficoTriagem');
        if (ctx) {
            const dadosGrafico = {!! json_encode($dados_grafico ?? [12, 19, 15, 25, 22, 30, 28]) !!};
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['08h', '10h', '12h', '14h', '16h', '18h', '20h'],
                    datasets: [{
                        label: 'Triagens',
                        data: dadosGrafico,
                        borderColor: '#008080',
                        backgroundColor: 'rgba(0, 128, 128, 0.08)',
                        fill: true,
                        tension: 0.4
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
        }
    });
</script>

@endsection