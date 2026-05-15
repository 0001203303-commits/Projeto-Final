<?php
// 1. CONEXÃO COM O BANCO DE DADOS
$host = "localhost";
$user = "root";
$pass = "";
$db   = "smart_triage";

// Tentativa de conexão (usamos @ para não exibir erro feio caso o banco esteja desligado)
$conn = @mysqli_connect($host, $user, $pass, $db);

// Inicializamos as variáveis com valores padrão (caso o banco falhe)
$total_triagens = "---"; 
$tempo_medio = "00:00";
$terminais_ativos = "0";
$alertas_criticos = "0";
$dados_grafico = [0, 0, 0, 0, 0, 0, 0];
$res_totens = null;

if ($conn) {
    // 1. Busca total de triagens hoje
    $res_triagens = mysqli_query($conn, "SELECT COUNT(*) as total FROM triagens WHERE data = CURDATE()");
    if ($res_triagens) {
        $row = mysqli_fetch_assoc($res_triagens);
        $total_triagens = $row['total'];
    }

    // 2. Busca total de totens ativos
    $res_ativos = mysqli_query($conn, "SELECT COUNT(*) as total FROM totens WHERE status = 'ONLINE'");
    if ($res_ativos) {
        $terminais_ativos = mysqli_fetch_assoc($res_ativos)['total'];
    }

    // 3. Busca lista de totens para a tabela
    $res_totens = mysqli_query($conn, "SELECT * FROM totens");

    // 4. Busca dados para o gráfico
    $res_grafico = mysqli_query($conn, "SELECT contagem FROM historico_triagem ORDER BY id ASC LIMIT 7");
    if ($res_grafico && mysqli_num_rows($res_grafico) > 0) {
        $dados_grafico = [];
        while($r = mysqli_fetch_assoc($res_grafico)) { 
            $dados_grafico[] = (int)$r['contagem']; 
        }
    }

    // Valores fixos (ou você pode criar tabelas para eles depois)
    $tempo_medio = "08:45"; 
    $alertas_criticos = "02";

} else {
    // Se a conexão falhou, exibimos uma mensagem amigável no lugar dos dados reais
    $total_triagens = "Erro DB";
}
?>