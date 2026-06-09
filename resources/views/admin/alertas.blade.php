@extends('admin.index')
@section('conteudo')

<main class="main-content">
    <header class="header">
        <div>
            <h1>Central de Alertas</h1>
            <p>Monitoramento preventivo de hardware e conectividade.</p>
        </div>
        <div class="update-tag" style="display: flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 13px; font-weight: 500;">
            <i data-lucide="refresh-cw" style="width: 14px;"></i>
            <span id="lastUpdate">Sincronizado agora</span>
        </div>
    </header>

    <div class="alert-filters" style="display: flex; gap: 10px; margin-bottom: 25px;">
        <button class="filter-btn active" style="padding: 8px 16px; border-radius: 20px; border: 1px solid var(--primary); background: var(--primary); color: white; font-weight: 600; cursor: pointer;">Todos (3)</button>
        <button class="filter-btn" style="padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border); background: white; color: var(--text-main); font-weight: 600; cursor: pointer;">Críticos</button>
        <button class="filter-btn" style="padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border); background: white; color: var(--text-main); font-weight: 600; cursor: pointer;">Insumos</button>
        <button class="filter-btn" style="padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border); background: white; color: var(--text-main); font-weight: 600; cursor: pointer;">Rede</button>
    </div>

    <div class="alerts-list" style="display: flex; flex-direction: column; gap: 15px;">
        
        <div class="alert-card" style="background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: var(--transition);">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="alert-icon" style="background: #fee2e2; color: #ef4444; padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="wifi-off"></i>
                </div>
                <div class="alert-info">
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Totem 04 Offline <span style="font-size: 12px; color: var(--text-muted); font-weight: 500;">• Há 15 minutos</span></h4>
                    <p style="font-size: 14px; color: var(--text-muted); font-weight: 500;">O dispositivo localizado no <strong>Corredor B</strong> parou de enviar sinais de atividade (Heartbeat). Verifique a conexão local.</p>
                </div>
            </div>
            <div class="alert-actions" style="display: flex; gap: 10px;">
                <button class="btn-action" onclick="alert('Iniciando diagnóstico remoto...')" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border); background: white; cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px;"><i data-lucide="terminal" style="width: 16px;"></i> Diagnosticar</button>
                <button class="btn-action" onclick="removerAlerta(this)" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border); background: white; cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--success);"><i data-lucide="check" style="width: 16px;"></i> Resolver</button>
            </div>
        </div>

        <div class="alert-card" style="background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: var(--transition);">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="alert-icon" style="background: #fef3c7; color: #d97706; padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="printer"></i>
                </div>
                <div class="alert-info">
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Pouco Papel (Insumo Crítico) <span style="font-size: 12px; color: var(--text-muted); font-weight: 500;">• Há 32 minutos</span></h4>
                    <p style="font-size: 14px; color: var(--text-muted); font-weight: 500;">A bobina de impressão do <strong>Totem 02 (Pediatria)</strong> está abaixo de 10%. Risco de interrupção na emissão de senhas.</p>
                </div>
            </div>
            <div class="alert-actions" style="display: flex; gap: 10px;">
                <button class="btn-action" onclick="removerAlerta(this)" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border); background: white; cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--success);"><i data-lucide="check" style="width: 16px;"></i> Abastecido</button>
            </div>
        </div>

    </div>
</main>

<script>
    function removerAlerta(btn) {
        const card = btn.closest('.alert-card');
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            card.style.display = 'none';
            
            const countBtn = document.querySelector('.filter-btn.active');
            const atual = parseInt(countBtn.innerText.match(/\d+/)) || 0;
            if (atual > 0) countBtn.innerText = `Todos (${atual - 1})`;
        }, 300);
    }

    // Atualiza o relógio de sincronização a cada 30 segundos
    setInterval(() => {
        const agora = new Date();
        document.getElementById('lastUpdate').innerText = `Atualizado às ${agora.getHours().toString().padStart(2, '0')}:${agora.getMinutes().toString().padStart(2, '0')}`;
    }, 30000);
</script>

<style>
    .alert-card {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
</style>

@endsection