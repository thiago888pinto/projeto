<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria de Fotos - Viva Vôlei</title>
    
    <!-- Bootstrap e FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/fotos.css">
    
    <link rel="icon" href="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" type="image/png">
    
    <style>
        .categoria-foto:hover .categoria-admin {
            opacity: 1 !important;
        }
        
        .admin-actions .btn {
            margin: 0.5rem;
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .admin-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
          <img src="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" alt="Logo" class="img-fluid logo" id="logo">
           <a class="navbar-brand d-flex align-items-center" href="viva.php">
                <h2>Viva Vôlei</h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="noticias.php">Notícias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fotos.php">Fotos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contatos.php">Contatos</a>
                    </li>
                    <?php if (isset($_SESSION['cpf'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="photo-register.php">Registrar Fotos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categoria-register.php">Gerenciar Categorias</a>
                        <li class="nav-item"><a class="nav-link" href="src/controller/logoutController.php">Logout</a></li>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php
    if (isset($_GET['cod']) && $_GET['cod'] == '109') {
        echo '<div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <strong>Pronto!</strong> Perfil excluído com sucesso!
        </div>';
    }
    ?>

    <div class="container-principal">
        <div class="conteudo-centralizado">
            <h2 class="noticiao">Galeria de Fotos</h2>
            
            <?php if (isset($_SESSION['cpf'])): ?>
                <div class="admin-actions" style="text-align: center; margin-bottom: 2rem;">
                    <a href="categoria-register.php" class="btn btn-success me-2">
                        <i class="fas fa-plus"></i> Nova Categoria
                    </a>
                    <a href="photo-register.php" class="btn btn-primary">
                        <i class="fas fa-camera"></i> Adicionar Fotos
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="bloco-categorias">
                <?php
                require_once 'src/ConexaoMysql.php';
                $con = new ConexaoMysql();
                $con->Conectar();

                // Buscar todas as categorias
                $sql = "SELECT * FROM categorias_fotos ORDER BY created_at DESC";
                $resultado = $con->Consultar($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    while ($categoria = $resultado->fetch_assoc()) {
                        // Contar quantas fotos tem na categoria
                        $sqlCount = "SELECT COUNT(*) as total FROM fotos WHERE categoria_id = " . $categoria['id'];
                        $resultCount = $con->Consultar($sqlCount);
                        $count = $resultCount->fetch_assoc()['total'];
                        
                        echo '<div class="categoria-foto" onclick="window.location.href=\'galeria-categoria.php?id=' . $categoria['id'] . '\'" style="cursor: pointer; position: relative;">';
                        
                        // Verificar se a imagem de capa existe
                        if ($categoria['imagem_capa'] && file_exists($categoria['imagem_capa'])) {
                            echo '<img src="' . htmlspecialchars($categoria['imagem_capa']) . '" alt="' . htmlspecialchars($categoria['nome']) . '">';
                        } else {
                            echo '<img src="src/img/default.jpg" alt="Imagem padrão">';
                        }
                        
                        echo '<div class="categoria-info">';
                        echo '<h4>' . htmlspecialchars($categoria['nome']) . '</h4>';
                        echo '<p>' . htmlspecialchars($categoria['descricao']) . '</p>';
                        echo '<small style="color: rgba(255, 255, 255, 1); font-weight: bold;">📸 ' . $count . ' foto(s)</small>';
                        echo '</div>';
                        
                        // Botão de editar/excluir para admins - só aparece no hover e para usuários logados
                        if (isset($_SESSION['cpf'])) {
                            echo '<div class="categoria-admin" style="position: absolute; top: 10px; right: 10px; opacity: 0; transition: opacity 0.3s;">';
                            echo '<a href="categoria-edit.php?id=' . $categoria['id'] . '" class="btn btn-sm btn-warning me-1" onclick="event.stopPropagation();" title="Editar">';
                            echo '<i class="fas fa-edit"></i></a>';
                            echo '<a href="categoria-delete.php?id=' . $categoria['id'] . '" class="btn btn-sm btn-danger" onclick="event.stopPropagation(); return confirm(\'Tem certeza que deseja excluir esta categoria e todas as suas fotos?\');" title="Excluir">';
                            echo '<i class="fas fa-trash"></i></a>';
                            echo '</div>';
                        }
                        
                        echo '</div>';
                    }
                } else {
                    echo '<div class="sem-categorias" style="text-align: center; padding: 3rem; background: whitesmoke; border-radius: 15px; margin: 2rem 0;">';
                    echo '<i class="fas fa-folder-open" style="font-size: 4rem; color: #6f42c1; margin-bottom: 1rem;"></i>';
                    echo '<h3>Nenhuma categoria encontrada</h3>';
                    echo '<p>Crie sua primeira categoria de fotos!</p>';
                    if (isset($_SESSION['cpf'])) {
                        echo '<a href="categoria-register.php" class="btn btn-primary mt-3">';
                        echo '<i class="fas fa-plus"></i> Criar Primeira Categoria</a>';
                    }
                    echo '</div>';
                }

                $con->Desconectar();
                ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy;2025 Viva Vôlei SM. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>