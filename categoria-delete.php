<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header('Location: login.php');
    exit();
}

// Verificar se foi passado um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: categoria-register.php');
    exit();
}

$categoria_id = intval($_GET['id']);

require_once 'src/ConexaoMysql.php';
$con = new ConexaoMysql();
$con->Conectar();

// Buscar a categoria para verificar se existe
$sqlCategoria = "SELECT * FROM categorias_fotos WHERE id = '$categoria_id'";
$resultado = $con->Consultar($sqlCategoria);

if (!$resultado || $resultado->num_rows == 0) {
    header('Location: categoria-register.php');
    exit();
}

$categoria = $resultado->fetch_assoc();

// Buscar todas as fotos da categoria para excluir os arquivos
$sqlFotos = "SELECT caminho FROM fotos WHERE categoria_id = '$categoria_id'";
$resultadoFotos = $con->Consultar($sqlFotos);

$arquivos_para_excluir = [];
if ($resultadoFotos && $resultadoFotos->num_rows > 0) {
    while ($foto = $resultadoFotos->fetch_assoc()) {
        $arquivos_para_excluir[] = $foto['caminho'];
    }
}

// Excluir primeiro todas as fotos da categoria
$sqlDeleteFotos = "DELETE FROM fotos WHERE categoria_id = '$categoria_id'";
$con->Executar($sqlDeleteFotos);

// Excluir a categoria
$sqlDeleteCategoria = "DELETE FROM categorias_fotos WHERE id = '$categoria_id'";
$sucesso = $con->Executar($sqlDeleteCategoria);

if ($sucesso) {
    // Excluir arquivos de imagem das fotos
    foreach ($arquivos_para_excluir as $arquivo) {
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }
    }
    
    // Excluir imagem de capa da categoria se existir
    if ($categoria['imagem_capa'] && file_exists($categoria['imagem_capa'])) {
        unlink($categoria['imagem_capa']);
    }
    
    header('Location: categoria-register.php?msg=categoria_excluida');
} else {
    header('Location: categoria-register.php?msg=erro_excluir');
}

$con->Desconectar();
exit();
?>