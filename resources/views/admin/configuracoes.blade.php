@extends('admin.index')
@section('conteudo')

<main class="main-content">
    <header class="header">
        <div>
            <h1>Configurações</h1>
            <p>Ajuste os parâmetros de performance e segurança do sistema.</p>
        </div>
    </header>

    <div class="settings-container">
        <section class="settings-card">
            <div class="section-header">
                <i data-lucide="shield-check"></i>
                <h3>Segurança & Acesso</h3>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Administrador</label>
                    <input type="text" value="Vinícius Oliveira">
                </div>
                <div class="form-group">
                    <label>E-mail Corporativo</label>
                    <input type="email" value="admin@autotriage.com.br">
                </div>
            </div>
        </section>

        <section class="settings-card">
            <div class="section-header">
                <i data-lucide="database"></i>
                <h3>Parâmetros de Rede & Totens</h3>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Intervalo de Polling (Segundos)</label>
                    <select>
                        <option>10s (Alta Precisão)</option>
                        <option selected>30s (Recomendado)</option>
                        <option>60s (Economia de Banda)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Alerta Crítico de Insumo (%)</label>
                    <input type="number" value="10" min="5" max="50">
                </div>
            </div>
        </section>

        <section class="settings-card">
            <div class="section-header">
                <i data-lucide="user-circle"></i>
                <h3>Perfil de Acesso</h3>
            </div>
            <div class="control-row">
                <div class="control-info">
                    <h4>Cargo do Usuário</h4>
                    <p>Selecione seu nível de acesso para personalizar as ferramentas.</p>
                </div>
                <select id="perfilAcesso" onchange="salvarPerfil()">
                    <option value="admin">Administrador de TI</option>
                    <option value="medico">Médico / Assistencial</option>
                </select>
            </div>
        </section>

        <div class="footer-actions">
            <button class="btn btn-secondary">Cancelar</button>
            <button class="btn btn-primary" onclick="confirmarSalvar()">
                <i data-lucide="save"></i> Aplicar Alterações
            </button>
        </div>
    </div>
</main>

<script>
    function confirmarSalvar() {
        alert("✅ Configurações salvas e replicadas!");
    }

    function salvarPerfil() {
        const perfil = document.getElementById('perfilAcesso').value;
        localStorage.setItem('userProfile', perfil);
        
        // Dispara a validação global definida no index.blade.php
        if (typeof aplicarRestricoesGlobais === "function") {
            aplicarRestricoesGlobais();
        }
        
        const msg = perfil === 'medico' ? 'Perfil Médico: Foco em pacientes e fluxo.' : 'Perfil Admin: Acesso total liberado.';
        alert("✅ " + msg);
        window.location.reload();
    }

    window.addEventListener('DOMContentLoaded', () => {
        const salvo = localStorage.getItem('userProfile') || 'admin';
        document.getElementById('perfilAcesso').value = salvo;
    });
</script>

@endsection