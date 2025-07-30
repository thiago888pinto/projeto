<?php
    if($_POST) {
        require_once './usersController.php';

        //$users = usersLoadAll();
        $email = $_POST['email'];
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $endereco = $_POST['endereco'];
        $dataNasc = $_POST['dataNasc'];
        $senha = $_POST['password'];

        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $result = verifyUser($cpf ,$email);


        if ($result == 0) {
            // Se não existir usuário com esse CPF ou email, registra o novo usuário
            usersRegister($nome, $email, $hash, $endereco, $cpf, $dataNasc);
            header('location: /projeto/login.php');
            exit;
        } else {
            // Se já existir, redireciona com uma mensagem de erro
            header('location: /projeto/login.php?register=true&cod=172');
            exit;
        }
}
?>