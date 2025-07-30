<?php
session_start();
include_once __DIR__ . '/src/controller/newsController.php';

if (!isset($_SESSION['cpf'])) {
    header('location: /projeto/newsRegister.php');
    exit();
}

if (isset($_GET['id'])) {
    $idNoticia = $_GET['id'];
    $noticia = detalhesNoticia($idNoticia);
    if (!$noticia) {
        echo "Notícia não encontrada.";
        exit();
    }
    $noticia = $noticia->fetch_assoc();
} else {
    echo "ID da notícia não fornecido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Notícia</title>
    <link rel="stylesheet" href="css/newsEdit.css">
</head>
<body>
    <h1>Editar Notícia</h1>
    <form action="src/controller/editNoticiaController.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="idNoticia" value="<?php echo $noticia['idNoticia']; ?>">
        
        <label for="nomeNoticia">Nome da Notícia:</label>
        <input type="text" name="nomeNoticia" value="<?php echo htmlspecialchars($noticia['nomeNoticia']); ?>" required>

        <label for="materia">Matéria:</label>
        <input type="text" name="materia" value="<?php echo htmlspecialchars($noticia['materia']); ?>" >

        <label for="texto">Texto:</label>
        <textarea name="texto" required><?php echo htmlspecialchars($noticia['texto']); ?></textarea>

        <label for="autor">Autor:</label>
        <input type="text" name="autor" value="<?php echo htmlspecialchars($noticia['autor']); ?>" >

        <label for="imagem">Imagem (opcional):</label>
        <input type="file" name="imagem">

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
