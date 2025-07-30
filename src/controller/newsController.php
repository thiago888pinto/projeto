<?php
include_once __DIR__ . '/../ConexaoMysql.php';

/**
 * Registra uma notícia e suas imagens.
 */
function newsRegister($nomeNoticia, $materia, $texto, $autor, $cpf, $imagens = [], $descricoes = []) {
    $con = new ConexaoMysql();
    $con->Conectar();

    // Inserir notícia
    $sql = "INSERT INTO noticia (nomeNoticia, materia, texto, autor, donoCpf) 
            VALUES ('$nomeNoticia', '$materia', '$texto', '$autor', '$cpf')";
    $noticiaId = $con->Executar($sql, true); // true para retornar o insert_id

    if (!$noticiaId) {
        $con->Desconectar();
        return false;
    }

    // Inserir imagens, se houver
    for ($i = 0; $i < count($imagens); $i++) {
        $caminho = $imagens[$i];
        $descricao = isset($descricoes[$i]) ? $descricoes[$i] : '';

        $sqlImg = "INSERT INTO imagens (noticia_id, caminho, descricao) 
                   VALUES ('$noticiaId', '$caminho', '$descricao')";
        $con->Executar($sqlImg);
    }

    $con->Desconectar();
    return true;
}

function salvarNoticia($nomeNoticia, $materia, $texto, $autor, $cpf, $dataCriacao)
{
    $con = new ConexaoMysql();
    $con->Conectar();

    $sql = "INSERT INTO noticia (nomeNoticia, materia, texto, autor, donoCpf, created_at)
            VALUES ('$nomeNoticia', '$materia', '$texto', '$autor', '$cpf', '$dataCriacao')";

    $id = $con->Executar($sql, true); // true retorna o ID gerado pelo insert
    $con->Desconectar();

    return $id; // Pode retornar false se falhar
}

function salvarImagemNoticia($noticiaId, $caminhoImagem, $descricao)
{
    $con = new ConexaoMysql();
    $con->Conectar();

    $sql = "INSERT INTO imagens (noticia_id, caminho, descricao)
            VALUES ('$noticiaId', '$caminhoImagem', '$descricao')";

    $result = $con->Executar($sql);
    $con->Desconectar();

    return $result;
}


/**
 * Retorna todas as notícias de um determinado CPF.
 */
function noticiasRegistradas($cpf) {
    $con = new ConexaoMysql();
    $con->Conectar();

    $sql = "SELECT * FROM noticia WHERE donoCpf = '$cpf'";
    $result = $con->Consultar($sql);

    $con->Desconectar();
    return $result;
}

/**
 * Retorna os detalhes de uma notícia específica.
 */
function detalhesNoticia($idNoticia) {
    $con = new ConexaoMysql();
    $con->Conectar();

    $sql = "SELECT * FROM noticia WHERE idNoticia = '$idNoticia'";
    $result = $con->Consultar($sql);

    $con->Desconectar();
    return $result;
}

/**
 * Edita uma notícia.
 */
function editNoticia($idNoticia, $nomeNoticiaEdit, $materiaEdit, $textoEdit, $imagemEdit, $autorEdit) {
    $con = new ConexaoMysql();
    $con->Conectar();

    // Verifica se a notícia existe
    $sql = "SELECT * FROM noticia WHERE idNoticia = '$idNoticia'";
    $result = $con->Consultar($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Atualiza os campos da notícia
        if ($user['nomeNoticia'] !== $nomeNoticiaEdit) {
            $con->Executar("UPDATE noticia SET nomeNoticia = '$nomeNoticiaEdit' WHERE idNoticia = '$idNoticia'");
        }

        if ($user['materia'] !== $materiaEdit) {
            $con->Executar("UPDATE noticia SET materia = '$materiaEdit' WHERE idNoticia = '$idNoticia'");
        }

        if ($user['texto'] !== $textoEdit) {
            $con->Executar("UPDATE noticia SET texto = '$textoEdit' WHERE idNoticia = '$idNoticia'");
        }

        // Atualiza a imagem se uma nova imagem foi enviada
        if (!empty($imagemEdit['name'])) {
            // Caminho para salvar a nova imagem
            $imagemPath = 'uploads/' . basename($imagemEdit['name']);
            // Move a imagem para o diretório desejado
            if (move_uploaded_file($imagemEdit['tmp_name'], '../../' . $imagemPath)) {
                // Atualiza o caminho da imagem no banco de dados
                $con->Executar("UPDATE noticia SET imagem = '$imagemPath' WHERE idNoticia = '$idNoticia'");
            } else {
                // Se o upload falhar, você pode retornar um erro ou continuar sem atualizar a imagem
                // Aqui, você pode decidir se quer retornar um erro ou não atualizar a imagem
            }
        }

        if ($user['autor'] !== $autorEdit) {
            $con->Executar("UPDATE noticia SET autor = '$autorEdit' WHERE idNoticia = '$idNoticia'");
        }
    }

    $con->Desconectar();
    return true;
}

/**
 * Exclui uma notícia pelo ID.
 */
function excluirNoticia($idNoticia) {
    $con = new ConexaoMysql();
    $con->Conectar();

    $con->Executar("DELETE FROM noticia WHERE idNoticia = '$idNoticia'");

    $con->Desconectar();
    return true;
}

/**
 * Busca notícias para popular dropdowns ou listagens.
 */
function buscarNoticias($cpf) {
    $con = new ConexaoMysql();
    $con->Conectar();

    $sql = "SELECT nomeNoticia, idNoticia FROM noticia WHERE donoCpf = '$cpf'";
    $result = $con->Consultar($sql);

    $con->Desconectar();
    return $result;
}
?>