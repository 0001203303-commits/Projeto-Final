<?php
// A senha que o usuário digitou no formulário de cadastro
$senha_digitada = 'senha123';

// Gera a hash segura usando o algoritmo padrão atual (BCRYPT)
$senha_criptografada = password_hash($senha_digitada, PASSWORD_DEFAULT);

// O resultado será aquela string gigante cheia de caracteres:
echo $senha_criptografada; 
// Exemplo de saída: $2y$10$mC1M9CjUXSfeC9VlB1.vbe1wI/A24oGg.8Yy4Vq2VpfehD3N87m2q
?>