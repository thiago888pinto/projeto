<?php
session_start();

// Verificar se foi passado um ID e se é um número válido
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: noticias.php');
    exit();
}

$idNoticia = intval($_GET['id']);

require_once 'src/ConexaoMysql.php';
$con = new ConexaoMysql();
$con->Conectar();

// Buscar a notícia específica
$sql = "SELECT * FROM noticia WHERE idNoticia = '$idNoticia'";
$resultado = $con->Consultar($sql);

if (!$resultado || $resultado->num_rows == 0) {
    header('Location: noticias.php');
    exit();
}

$noticia = $resultado->fetch_assoc();

// Buscar todas as imagens da notícia
$sqlImagens = "SELECT caminho FROM imagens WHERE noticia_id = '$idNoticia'";
$resultadoImagens = $con->Consultar($sqlImagens);

$imagens = [];
if ($resultadoImagens && $resultadoImagens->num_rows > 0) {
    while ($img = $resultadoImagens->fetch_assoc()) {
        $imagens[] = $img['caminho'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['nomeNoticia']); ?> - Viva Vôlei</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="src/img/LogoSample_StrongPurple.png" type="image/png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700&display=swap');

        .conteudo-centralizado{
            background: whitesmoke;
        }
        .noticia-completa {
            max-width: 800px;
            padding: 2rem;
            background: whitesmoke;
            border-radius: 10px;
            margin-left: 4.6pc;
        }
        
        
        
        .noticia-title {
            color: #000000ff;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            font-family:   'Palatino', sans-serif;
        }
        
        .noticia-meta {
            color: whitesmoke;
            font-size: 0.9rem;
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
     
        
        .noticia-imagem {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .noticia-texto {
            line-height: 1.8;
           font-size: 1.1rem;
            color: #333;
            text-align: justify;
            font-family: Arial, Helvetica, sans-serif;
        }
        
        @media (max-width: 768px) {
            .noticia-completa {
                margin: 1rem;
                padding: 1rem;
            }
            
            .noticia-title {
                font-size: 2rem;
            }
            
            .noticia-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            .container{
                color: black;
            }
        }
    </style>
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
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container-principal">
        <div class="conteudo-centralizado">
            <a href="noticias.php" class="btn-voltar">← Voltar para Notícias</a>
            
            <div class="noticia-completa">
                <div class="noticia-header">
                  <h1 class="noticia-title">
                        <?php 
                        $titulo = htmlspecialchars($noticia['nomeNoticia']);
                        $titulo_quebrado = wordwrap($titulo, 65, "\n", true); 
                        echo nl2br($titulo_quebrado); 
                        
                        ?>
                    </h1>
                </div>

                <?php if (!empty($imagens)): ?>
                <div class="noticia-imagens">
                    <?php foreach ($imagens as $imagemPath): ?>
                        <?php if (file_exists($imagemPath)): ?>
                            <img src="<?php echo htmlspecialchars($imagemPath); ?>" 
                                 alt="<?php echo htmlspecialchars($noticia['nomeNoticia']); ?>" 
                                 class="noticia-imagem">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="noticia-texto">
                   <?php
                    echo  date('d/m/Y', strtotime($noticia['created_at'])); 
                    echo '<br>';
                    echo '<p><strong>' . htmlspecialchars($noticia['materia'] ?? 'N/A') . '</strong></p>' ;                   
                    $texto = htmlspecialchars($noticia['texto']);
                    $texto_quebrado = wordwrap($texto, 100, "\n", true); 
                    echo nl2br($texto_quebrado); 
                   ?> 
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy;2025 Viva Vôlei SM. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$con->Desconectar();
?>