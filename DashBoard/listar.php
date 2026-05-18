<?php
// 1. Conecta ao banco de dados
$conexao = mysqli_connect("localhost", "root", "", "teste");

// 2. Busca todos os usuários da tabela
$sql = "SELECT * FROM usuarios";
$resultado = mysqli_query($conexao, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Lista de Usuários</title>
    <style>
        table { width: 50%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

    <h2>Usuários Cadastrados</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
        </tr>

        <?php
        // 3. Loop para criar uma linha (tr) para cada registro no banco
        while($usuario = mysqli_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . $usuario['nome'] . "</td>";
            echo "<td>" . $usuario['email'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.html">Cadastrar Novo</a>

</body>
</html>

<?php mysqli_close($conexao); ?>
