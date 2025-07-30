<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viva Vôlei</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css\index.css">
    <link rel="icon" href="src\img\369951376_309555475108077_7449468456577851380_n-removebg-preview.png" type="image/png">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <img src="src\img\369951376_309555475108077_7449468456577851380_n-removebg-preview.png" alt="Logo" class="logo">
            <a class="navbar-brand d-flex align-items-center" href="viva.php">
                <h2>Viva Vôlei</h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="noticias.php">Notícias</a></li>
                    <li class="nav-item"><a class="nav-link" href="fotos.php">Fotos</a></li>
                    <li class="nav-item"><a class="nav-link" href="contatos.php">Contatos</a></li>
                    <?php if (isset($_SESSION['cpf'])): ?>
                        <li class="nav-item"><a class="nav-link" href="newsRegister.php">Registrar Notícia</a></li>
                        <li class="nav-item"><a class="nav-link" href="src/controller/logoutController.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-principal">
        <div class="conteudo-centralizado">
            <div class="noticiao">
                <h2>Todas as Notícias</h2>
            </div>

            <div class="noticias-1">
                                 <?php
                require_once 'src/ConexaoMysql.php';
                
                $con = new ConexaoMysql();
                $con->Conectar();

                $sql = "SELECT * FROM noticia ORDER BY created_at DESC";
                $resultado = $con->Consultar($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    while ($noticia = $resultado->fetch_assoc()) {
                        echo '<div class="noticias-wrapper-1" onclick="window.location.href=\'noticia-detalhes.php?id=' . $noticia['idNoticia'] . '\'" style="cursor: pointer; transition: transform 0.2s; border-radius: 8px;" onmouseover="this.style.transform=\'scale(1.02)\'" onmouseout="this.style.transform=\'scale(1)\'">';
                        
                        // Buscar imagem
                        $idNoticia = $noticia['idNoticia'];
                        $sqlImagem = "SELECT caminho FROM imagens WHERE noticia_id = '$idNoticia' LIMIT 1";
                        $resImg = $con->Consultar($sqlImagem);

                        if ($resImg && $resImg->num_rows > 0) {
                            $img = $resImg->fetch_assoc();
                            $imagemPath = htmlspecialchars($img['caminho']);
                            
                            if (file_exists($imagemPath)) {
                                echo '<img src="' . $imagemPath . '" alt="">';
                            } else {
                                echo '<img src="src/img/default.jpg" alt="Imagem padrão">';
                            }
                        } else {
                            echo '<img src="src/img/default.jpg" alt="Imagem padrão">';
                        }

                        echo '<div class="texto-noticia">';
                        echo '<h4>'; 
                        $titulo = htmlspecialchars($noticia['nomeNoticia']);
                        $titulo_quebrado = wordwrap($titulo, 45, "\n", true); 
                        echo nl2br($titulo_quebrado);  
                        '</h4>';
                        echo '<p><small>' . htmlspecialchars($noticia['materia'] ?? 'N/A') . '</small></p>'. date('d/m/Y', strtotime($noticia['created_at']))  ;    
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="sem-noticias">';
                    echo '<p>📰 Nenhuma notícia encontrada no momento.</p>';
                    echo '<p>Seja o primeiro a compartilhar uma novidade!</p>';
                    if (isset($_SESSION['cpf'])) {
                        echo '<a href="newsRegister.php" class="btn-registrar">Registrar Primeira Notícia</a>';
                    }
                    echo '</div>';
                }

                $con->Desconectar();
                ?>
            </div>
        </div>
    </div>

 
    <footer>
        <div class="container">
        <p>&copy; 2025 Viva Vôlei SM. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>