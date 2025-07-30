<?php
session_start();
include_once __DIR__ . '/../controller/newsController.php';

if (isset($_GET['id'])) {
    $idNoticia = $_GET['id'];
    $resultado = excluirNoticia($idNoticia);

    if ($resultado) {
        $_SESSION['sucesso'] = "Notícia excluída com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir a notícia.";
    }
    header('Location: /projeto/viva.php');
} else {
    $_SESSION['erro'] = "ID da notícia não fornecido.";
    header('Location: /projeto/viva.php');
}
?>
