<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header('Location: login.php');
    exit();
}

// Verificar se foi passado um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: fotos.php');
    exit();
}

$foto_id = intval($_GET['id']);
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : null;

require_once 'src/ConexaoMysql.php';
$con = new ConexaoMysql();
$con->Conectar();

// Buscar a foto para verificar se existe e pegar o caminho do arquivo
$sqlFoto = "SELECT * FROM fotos WHERE id = '$foto_id'";
$resultado = $con->Consultar($sqlFoto);

if (!$resultado || $resultado->num_rows == 0) {
    $con->Desconectar();
    header('Location: fotos.php');
    exit();
}

$foto = $resultado->fetch_assoc();
$categoria_id = $categoria_id ?: $foto['categoria_id'];

// Excluir a foto do banco de dados
$sqlDelete = "DELETE FROM fotos WHERE id = '$foto_id'";
$sucesso = $con->Executar($sqlDelete);

if ($sucesso) {
    // Excluir o arquivo físico se existir
    if (file_exists($foto['caminho'])) {
        unlink($foto['caminho']);
    }
    
    // Redirecionar de volta para a galeria da categoria
    header("Location: galeria-categoria.php?id=$categoria_id&msg=foto_excluida");
} else {
    // Redirecionar com mensagem de erro
    header("Location: galeria-categoria.php?id=$categoria_id&msg=erro_excluir");
}

$con->Desconectar();
exit();
?>