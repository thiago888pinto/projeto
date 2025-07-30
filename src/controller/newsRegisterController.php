<?php
session_start();
require_once '../controller/newsController.php'; // Ajuste para o caminho correto do seu controller

// Verifica se os campos obrigatórios foram preenchidos
if (!isset($_POST['nomeNoticia'], $_POST['materia'], $_POST['texto'], $_POST['autor'], $_POST['cpf'])) {
    $_SESSION['erro'] = 'Preencha todos os campos obrigatórios.';
    header('Location: /projeto/newsRegister.php');
    exit();
}

// Sanitiza os dados
$nomeNoticia = trim($_POST['nomeNoticia']);
$materia = trim($_POST['materia']);
$texto = trim($_POST['texto']);
$autor = trim($_POST['autor']);
$cpf = trim($_POST['cpf']);
$dataCriacao = date('Y-m-d H:i:s');

// Salva a notícia no banco
$noticiaId = salvarNoticia($nomeNoticia, $materia, $texto, $autor, $cpf, $dataCriacao);

if (!$noticiaId) {
    header('Location: /projeto/newsRegister.php?cod=504');
    exit();
}

// Diretório de destino das imagens
$destino = '../../uploads/';
if (!is_dir($destino)) {
    mkdir($destino, 0755, true);
}

// Processa imagens (se houver)
if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name'])) {
    $descricoes = isset($_POST['descricao']) ? $_POST['descricao'] : [];

    foreach ($_FILES['fotos']['tmp_name'] as $index => $tmpName) {
        $nomeOriginal = $_FILES['fotos']['name'][$index];
        $tipo = $_FILES['fotos']['type'][$index];
        $tamanho = $_FILES['fotos']['size'][$index];
        $erro = $_FILES['fotos']['error'][$index];

        if ($erro !== UPLOAD_ERR_OK) {
            header('Location: /projeto/newsRegister.php?cod=500');
            exit();
        }

        if ($tamanho > 5 * 1024 * 1024) {
            header('Location: /projeto/newsRegister.php?cod=501');
            exit();
        }

        $permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($tipo, $permitidos)) {
            header('Location: /projeto/newsRegister.php?cod=502');
            exit();
        }

        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $nomeFinal = uniqid('img_', true) . '.' . $extensao;
        $caminhoRelativo = 'uploads/' . $nomeFinal;
        $caminhoFinal = $destino . $nomeFinal;

        if (!move_uploaded_file($tmpName, $caminhoFinal)) {
            header('Location: /projeto/newsRegister.php?cod=503');
            exit();
        }

        $descricaoImagem = trim($descricoes[$index]) ?? '';

        // Salva a imagem associada à notícia no banco
        salvarImagemNoticia($noticiaId, $caminhoRelativo, $descricaoImagem);
    }
}

// Redireciona após sucesso
header('Location: /projeto/newsRegister.php?cod=100');
header('Location: /projeto/viva.php?cod=100');
exit();
?>