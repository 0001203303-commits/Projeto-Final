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
                @foreach($totens as $totem)
                    <tr>
                        <td><strong>Totem {{ $totem->dispositivo }}</strong></td>
                        <td>{{ $totem->enderecoip }}</td> {{-- Corrigido para a coluna correta do banco --}}
                        <td>
                            @if($totem->status == 'online')
                                <span class="badge badge-online" style="background: #d1fae5; color: #10b981; padding: 4px 8px; border-radius: 4px; font-weight: bold;">● ONLINE</span>
                            @elseif($totem->status == 'desativado')
                                <span class="badge badge-desativado" style="background: #f3f4f6; color: #6b7280; padding: 4px 8px; border-radius: 4px; font-weight: bold;">● DESATIVADO</span>
                            @else
                                <span class="badge badge-offline" style="background: #fee2e2; color: #ef4444; padding: 4px 8px; border-radius: 4px; font-weight: bold;">● OFFLINE</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button class="btn btn-primary" onclick="window.location.reload();">Atualizar</button>
        <button class="btn btn-secondary" onclick="window.location.href='{{ route('TotemController.criar) }}';">Adicionar Totem</button>
    </div>
</main>

{{-- Incluindo o Chart.js caso não esteja no seu layout principal --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('graficoPerformanceTotem').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Totem 01', 'Totem 02', 'Totem 03'],
                datasets: [{
                    label: 'Uptime %',
                    data: [100, 98, 45],
                    backgroundColor: ['#10b981', '#10b981', '#ef4444'],
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    });
</script>

@endsection