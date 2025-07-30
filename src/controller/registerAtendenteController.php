<?php
if ($_POST) {
    require_once './atendenteController.php';

    //$users = usersLoadAll();
    $email = $_POST['email'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $endereco = $_POST['endereco'];
    $dataNasc = $_POST['dataNasc'];
    $senha = $_POST['password'];

    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $result = verifyAtendente($cpf, $email);


    if ($result == 0) {
        // Se não existir usuário com esse CPF ou email, registra o novo usuário
        atendenteRegister($nome, $email, $hash, $endereco, $cpf, $dataNasc);
        header('location: /atPage.php?cod=103');
        exit;
    } else {
        // Se já existir, redireciona com uma mensagem de erro
        header('location: /cadastroAtendente.php?register=true&cod=172');
        exit;
    }

    
}
