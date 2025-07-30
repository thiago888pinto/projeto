<?php
session_start();
include_once __DIR__ . '/../controller/newsController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNoticia = $_POST['idNoticia'];
    $nomeNoticiaEdit = $_POST['nomeNoticia'];
    $materiaEdit = $_POST['materia'];
    $textoEdit = $_POST['texto'];
    $autorEdit = $_POST['autor'];
    $imagemEdit = $_FILES['imagem'];

    $resultado = editNoticia($idNoticia, $nomeNoticiaEdit, $materiaEdit, $textoEdit, $imagemEdit, $autorEdit);

    if ($resultado) {
        $_SESSION['sucesso'] = "Notícia editada com sucesso!";
        header('Location: /projeto/newsRegister.php');
    } else {
        $_SESSION['erro'] = "Erro ao editar a notícia.";
        header('Location: /projeto/newsRegister.php');
    }
}
?>
