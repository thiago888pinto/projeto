<?php
if ($_POST) {
    require_once './usersController.php';

    $email = $_POST['email'];
    $senha = $_POST['password'];
    $cpf = $_POST['cpf'];

    $user = usersLogin($email, $cpf);

    if ($user && $user->num_rows > 0) {
        $userData = $user->fetch_assoc(); // tabela usuario
        $hash = $userData['senha']; // acessa a senha hash da tabela 

        if (password_verify($senha, $hash)) {
            session_start();
            $_SESSION['cpf'] = $cpf;
            header('location: /projeto/loguei.php');
            exit();
        } else {
            header('location: ../../login.php?cod=171');
        }
    } else {
        header('location: ../../login.php?cod=171');
    }
}
?>