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
        </div>
        <div class="card">
            <h3>Tempo Médio</h3>
            <div class="value">{{ $tempo_medio ?? 'N/D' }} @if($tempo_medio)<small
            style="font-size: 14px; color: var(--text-muted)">min</small>@endif</div>
        </div>
        <div class="card">
            <h3>Terminais Ativos</h3>
            <div class="value">{{ $terminais_ativos ?? 0 }} <span style="font-size: 18px; color: var(--text-muted)">/
                    {{ $total_terminais ?? 0 }}</span></div>
        </div>
        <div class="card">
            <h3>Alertas Críticos</h3>
            <div class="value" style="color: var(--danger)">{{ $alertas_criticos ?? 0 }}</div>
        </div>
    </section>

    <div class="dashboard-row">
        <section class="section-box">
            <div class="section-title"><i data-lucide="bar-chart-3" style="color: var(--primary)"></i> Fluxo de Triagem
                (24h)</div>
            <div class="chart-container">
                <canvas id="graficoTriagem"></canvas>
            </div>
        </section>

        <section class="section-box">
            <div class="section-title"><i data-lucide="server" style="color: var(--primary)"></i> Status dos Terminais
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Localização</th>
                        <th>Insumo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($totens ?? [] as $totem)
                    @php($classe = $totem->status === 'ONLINE' ? 'badge-online' : 'badge-offline')
                    <tr>
                        <td><strong>{{ $totem->nome ?: 'Totem #' . $totem->id }}</strong></td>
                        <td>N/D</td>
                        <td><span class="badge {{ $classe }}">{{ $totem->status ?: 'SEM STATUS' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">Nenhum totem cadastrado.</td>
                    </tr>
                    @endforelse
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
        const dadosGrafico = @json($dados_grafico ?? []);
        const rotulosGrafico = @json($rotulos_grafico ?? []);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: rotulosGrafico,
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