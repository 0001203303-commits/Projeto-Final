<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "smart_triage";

$conn = mysqli_connect($host, $user, $pass, $db);

// O Arduino vai enviar via método GET (pela URL)
if(isset($_GET['valor']) && isset($_GET['totem_id'])) {
    $valor = $_GET['valor'];
    $id = $_GET['totem_id'];

    // Atualiza o nível de insumo do totem no banco
    $sql = "UPDATE totens SET insumo = '$valor' WHERE id = '$id'";
    
    if(mysqli_query($conn, $sql)) {
        echo "Dados registrados com sucesso!";
    } else {
        echo "Erro: " . mysqli_error($conn);
    }
}
?>