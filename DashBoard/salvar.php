<?php
$conexao = mysqli_connect("localhost", "root", "", "teste");

$nome = $_POST['nome'];
$email = $_POST['email'];

$sql = "INSERT INTO usuarios (nome, email) VALUES ('$nome', '$email')";

if(mysqli_query($conexao, $sql)) {
    echo "Dados salvos com sucesso!";
} else {
    echo "Erro: " . mysqli_error($conexao);
}

mysqli_close($conexao);
?>
