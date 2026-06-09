@extends('admin.index')
@section('conteudo')

<main class="main-content">
    <div class="header">
        <div>
            <h1>Gestão de Pacientes</h1>
            <p>Controle de fila e classificação de risco (Manchester).</p>
        </div>
        <div class="search-box">
            <input type="text" id="inputBusca" placeholder="Buscar por nome ou protocolo..." onkeyup="filtrarTabela()" style="padding: 10px; border-radius: 8px; border: 1px solid var(--border);">
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
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#8821</td>
                    <td><strong>João Ricardo Silva</strong></td>
                    <td>45</td>
                    <td>14:30</td>
                    <td><span class="badge" style="background:#fee2e2; color:#ef4444;">EMERGÊNCIA</span></td>
                    <td>Dor torácica intensa, falta de ar</td>
                </tr>
                <tr>
                    <td>#8825</td>
                    <td><strong>Maria das Dores</strong></td>
                    <td>68</td>
                    <td>14:45</td>
                    <td><span class="badge" style="background:#fef3c7; color:#d97706;">URGENTE</span></td>
                    <td>Febre alta (39.5°C)</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<script>
    function filtrarTabela() {
        const input = document.getElementById("inputBusca");
        const filter = input.value.toUpperCase();
        const tr = document.querySelectorAll("#tabelaPacientes tbody tr");

        tr.forEach(row => {
            const texto = row.textContent.toUpperCase();
            row.style.display = texto.includes(filter) ? "" : "none";
        });
    }
</script>

@endsection