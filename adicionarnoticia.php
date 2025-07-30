<?php
session_start();

if (isset($_SESSION['cpf'])) {
} else {
    header('location: /projeto/viva.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Notícias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-noticia {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-noticia img {
            height: 200px;
            object-fit: cover;
        }
        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <?php

        // Conexão com o banco de dados
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'viva';
        $mysqli = new mysqli($host, $user, $password, $database);

        if ($mysqli->connect_error) {
            die('Erro de conexão: ' . $mysqli->connect_error);
        }

        // Mostra mensagem de sucesso se existir
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['mensagem_sucesso'] . "</div>";
            unset($_SESSION['mensagem_sucesso']);
        }

        // Obtém informações do usuário logado
        $email = $_SESSION['email'];
        $query = "SELECT nome FROM administrador WHERE email = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $administrador = $result->fetch_assoc();
            $administradornome = $administrador['nome'];
            
            echo "<h1>Bem-vindo, " . htmlspecialchars($administradornome) . "</h1>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao carregar informações do usuário.</div>";
            exit();
        }
        ?>

        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Sistema de Notícias</a>
                <div class="navbar-nav">
                    <a class="nav-link" href="logout.php">Sair</a>
                </div>
            </div>
        </nav>

        <div class="mb-4">
            <a href="?acao=visualizar" class="btn btn-primary">Visualizar Notícias</a>
            <?php if ($usuario_role !== 'reporter'): ?>
                <a href="?acao=adicionar" class="btn btn-success">Adicionar Notícia</a>
            <?php endif; ?>
        </div>

        <?php
        // Lógica para visualizar notícias
        if (isset($_GET['acao']) && $_GET['acao'] === 'visualizar') {
            $query = "SELECT n.id, n.titulo, n.resumo, n.data_publicacao, u.nome as autor, 
                     (SELECT i.caminho FROM imagens_noticias i WHERE i.noticia_id = n.id AND i.is_capa = TRUE LIMIT 1) as imagem_capa
                      FROM noticias n
                      JOIN usuarios u ON n.autor_id = u.id
                      WHERE n.status = 'publicado'
                      ORDER BY n.data_publicacao DESC";
            
            $result = $mysqli->query($query);
            
            if ($result->num_rows > 0) {
                echo '<div class="row">';
                while ($noticia = $result->fetch_assoc()) {
                    echo '<div class="col-md-4">';
                    echo '<div class="card card-noticia">';
                    if ($noticia['imagem_capa']) {
                        echo '<img src="' . htmlspecialchars($noticia['imagem_capa']) . '" class="card-img-top" alt="Capa da notícia">';
                    } else {
                        echo '<img src="placeholder.jpg" class="card-img-top" alt="Sem imagem">';
                    }
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($noticia['titulo']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($noticia['resumo']) . '</p>';
                    echo '<p class="text-muted"><small>Por ' . htmlspecialchars($noticia['autor']) . ' em ' . date('d/m/Y', strtotime($noticia['data_publicacao'])) . '</small></p>';
                    echo '<a href="detalhes_noticia.php?id=' . $noticia['id'] . '" class="btn btn-primary">Ler mais</a>';
                    echo '</div></div></div>';
                }
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">Nenhuma notícia publicada ainda.</div>';
            }
        }

        // Lógica para adicionar notícia
        if (isset($_GET['acao']) && $_GET['acao'] === 'adicionar') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $titulo = $mysqli->real_escape_string($_POST['titulo']);
                $resumo = $mysqli->real_escape_string($_POST['resumo']);
                $conteudo = $mysqli->real_escape_string($_POST['conteudo']);
                $categoria = $mysqli->real_escape_string($_POST['categoria']);
                $status = 'publicado'; // Ou definir conforme permissões
                
                // Insere a notícia no banco de dados
                $query = "INSERT INTO noticias (titulo, resumo, conteudo, autor_id, categoria, status) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("sssiss", $titulo, $resumo, $conteudo, $usuario_id, $categoria, $status);
                
                if ($stmt->execute()) {
                    $noticia_id = $mysqli->insert_id;
                    
                    // Processa as imagens
                    if (!empty($_FILES['imagens']['name'][0])) {
                        $diretorio = 'uploads/noticias/';
                        if (!file_exists($diretorio)) {
                            mkdir($diretorio, 0777, true);
                        }
                        
                        for ($i = 0; $i < count($_FILES['imagens']['name']); $i++) {
                            $nome_arquivo = uniqid() . '_' . basename($_FILES['imagens']['name'][$i]);
                            $caminho_completo = $diretorio . $nome_arquivo;
                            
                            if (move_uploaded_file($_FILES['imagens']['tmp_name'][$i], $caminho_completo)) {
                                $descricao = isset($_POST['descricoes'][$i]) ? $mysqli->real_escape_string($_POST['descricoes'][$i]) : '';
                                $is_capa = ($i === 0) ? 1 : 0; // Primeira imagem é a capa
                                
                                $query_img = "INSERT INTO imagens_noticias (noticia_id, caminho, descricao, is_capa) 
                                              VALUES (?, ?, ?, ?)";
                                $stmt_img = $mysqli->prepare($query_img);
                                $stmt_img->bind_param("issi", $noticia_id, $caminho_completo, $descricao, $is_capa);
                                $stmt_img->execute();
                            }
                        }
                    }
                    
                    $_SESSION['mensagem_sucesso'] = "Notícia publicada com sucesso!";
                    header("Location: noticias.php");
                    exit();
                } else {
                    echo '<div class="alert alert-danger">Erro ao publicar notícia: ' . $stmt->error . '</div>';
                }
            }
            
            // Formulário para adicionar notícia
            echo '<div class="card">
                    <div class="card-header">
                        <h2>Adicionar Nova Notícia</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="resumo" class="form-label">Resumo</label>
                                <textarea class="form-control" id="resumo" name="resumo" rows="2" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="conteudo" class="form-label">Conteúdo</label>
                                <textarea class="form-control" id="conteudo" name="conteudo" rows="5" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <input type="text" class="form-control" id="categoria" name="categoria">
                            </div>
                            
                            <div class="mb-3">
                                <label for="imagens" class="form-label">Imagens (Primeira será a capa)</label>
                                <input type="file" class="form-control" id="imagens" name="imagens[]" multiple accept="image/*">
                            </div>
                            
                            <div id="descricoes-container"></div>
                            
                            <button type="submit" class="btn btn-primary">Publicar Notícia</button>
                        </form>
                    </div>
                  </div>';
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para gerenciar descrições das imagens
        document.getElementById('imagens').addEventListener('change', function(e) {
            const container = document.getElementById('descricoes-container');
            container.innerHTML = '';
            
            for (let i = 0; i < this.files.length; i++) {
                const div = document.createElement('div');
                div.className = 'mb-3';
                
                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = 'Descrição para ' + this.files[i].name;
                
                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control';
                input.name = 'descricoes[]';
                
                div.appendChild(label);
                div.appendChild(input);
                container.appendChild(div);
            }
        });
    </script>
</body>
</html>