<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contatos</title>
    
    <!-- Bootstrap e FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/contatos.css">
    
    <link rel="icon" href="src\img\369951376_309555475108077_7449468456577851380_n-removebg-preview.png" type="image/png">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
          <img  src="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" alt="Logo" class="img-fluid logo" id="logo">
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
            <h2 class="noticiao">Contatos</h2>
            <div class="bloco-categorias">

                <div class="categoria-foto">
                    <a href="https://www.instagram.com/vivaavoleism/" target="_blank"><img src="src\img\image.png" alt="Temporada 2024"></a>
                    <div class="categoria-info">
                        <h4>Instagram</h4>
                    </div>
                </div>
                <div class="categoria-foto">
                    <img src="src\img\Captura de tela 2025-07-24 193940.png" alt="Torneio Inclusivo"></a>
                    <div class="categoria-info">
                        <h4>WhatsApp</h4>
                    </div>
                </div>
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
