<?php
session_start();

if (!isset($_SESSION['cpf'])) {
    header('location: /projeto/viva.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viva Vôlei - Notícias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/noticias.css">
    <link rel="icon" href="src/img/LogoSample_StrongPurple.png" type="image/png">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="viva.php">
                <img src="src/img/LogoSample_StrongPurple.png" alt="Logo" class="logo">
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
                    <li class="nav-item"><a class="nav-link" href="src/controller/logoutController.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container-principal">
        <div class="conteudo-centralizado">
            <div class="noticiao">
                <h2>Bem vindo ADM</h2>
                <a href="newsRegister.php">Registrar Notícias</a>
                <a href="fotosRegister.php">Registrar Fotos</a>
            </div>

        </div>
    </div>
    <footer>
       <p>&copy; Viva Vôlei SM. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>