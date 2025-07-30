<?php
session_start();

// Verificar se foi passado um ID de categoria
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: fotos.php');
    exit();
}

$categoria_id = intval($_GET['id']);

require_once 'src/ConexaoMysql.php';
$con = new ConexaoMysql();
$con->Conectar();

// Buscar informações da categoria
$sqlCategoria = "SELECT * FROM categorias_fotos WHERE id = '$categoria_id'";
$resultadoCategoria = $con->Consultar($sqlCategoria);

if (!$resultadoCategoria || $resultadoCategoria->num_rows == 0) {
    header('Location: fotos.php');
    exit();
}

$categoria = $resultadoCategoria->fetch_assoc();

// Buscar fotos da categoria
$sql = "SELECT * FROM fotos WHERE categoria_id = '$categoria_id' ORDER BY created_at DESC";
$resultado = $con->Consultar($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoria['nome']); ?> - Galeria - Viva Vôlei</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/fotos.css">
    
    <link rel="icon" href="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" type="image/png">
    
    <style>
        .galeria-fotos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 2rem 0;
        }
        
        .foto-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .foto-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(111, 66, 193, 0.2);
        }
        
        .foto-item:hover .foto-admin {
            opacity: 1 !important;
        }
        
        .foto-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .foto-item:hover img {
            transform: scale(1.1);
        }
        
        .foto-info {
            padding: 1rem;
        }
        
        .foto-info h5 {
            color: #000000ff;
            margin-bottom: 0.5rem;
        }
        
        .foto-info p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .foto-data {
            color: #999;
            font-size: 0.8rem;
        }
        
        .btn-voltar {
            background: whitesmoke;
            color: black;
            padding: 0.7rem 2rem;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2rem;
            transition: all 0.3s;
        }
        
        .btn-voltar:hover {
            background: #000560ff;
            color: whitesmoke;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .sem-fotos {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            margin: 2rem 0;
        }
        
        .sem-fotos i {
            font-size: 4rem;
            color: black;
            margin-bottom: 1rem;
        }
        
        /* Modal para visualizar foto em tamanho grande */
        .modal-dialog {
            max-width: 90vw;
        }
        
        .modal-content {
            background: transparent;
            border: none;
        }
        
        .modal-body {
            padding: 0;
            text-align: center;
        }
        
        .modal-body img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 10px;
        }
        
        .categoria-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .categoria-header h2 {
            color: black;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .categoria-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .galeria-fotos {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
            }
            
            .categoria-header h2 {
                font-size: 2rem;
            }
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="noticias.php">Notícias</a></li>
                    <li class="nav-item"><a class="nav-link" href="fotos.php">Fotos</a></li>
                    <li class="nav-item"><a class="nav-link" href="contatos.php">Contatos</a></li>
                    <?php if (isset($_SESSION['cpf'])): ?>
                        <li class="nav-item"><a class="nav-link" href="photo-register.php">Registrar Fotos</a></li>
                        <li class="nav-item"><a class="nav-link" href="categoria-register.php">Gerenciar Categorias</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-principal">
        <div class="conteudo-centralizado">
            <a href="fotos.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar para Galeria
            </a>
            
            <?php
            // Exibir mensagens de feedback
            if (isset($_GET['msg'])) {
                switch($_GET['msg']) {
                    case 'foto_excluida':
                        echo '<div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <strong>Sucesso!</strong> Foto excluída com sucesso!
                              </div>';
                        break;
                    case 'erro_excluir':
                        echo '<div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <strong>Erro!</strong> Não foi possível excluir a foto. Tente novamente.
                              </div>';
                        break;
                }
            }
            ?>
            
            <div class="categoria-header">
                <h2><?php echo htmlspecialchars($categoria['nome']); ?></h2>
                <p><?php echo htmlspecialchars($categoria['descricao']); ?></p>
                
                <?php if (isset($_SESSION['cpf'])): ?>
                    <div class="admin-actions" style="margin-top: 1rem;">
                        <a href="photo-register.php?categoria_id=<?php echo $categoria_id; ?>" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> Adicionar Fotos
                        </a>
                        <a href="categoria-register.php" class="btn btn-success">
                            <i class="fas fa-cog"></i> Gerenciar Categorias
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <div class="galeria-fotos">
                    <?php while ($foto = $resultado->fetch_assoc()): ?>
                        <div class="foto-item" style="position: relative;">
                            <!-- Botões administrativos -->
                            <?php if (isset($_SESSION['cpf'])): ?>
                                <div class="foto-admin" style="position: absolute; top: 10px; right: 10px; opacity: 0; transition: opacity 0.3s; z-index: 10;">
                                    <a href="foto-delete.php?id=<?php echo $foto['id']; ?>&categoria_id=<?php echo $categoria_id; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja excluir esta foto?');" 
                                       title="Excluir foto">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div onclick="mostrarFoto('<?php echo htmlspecialchars($foto['caminho']); ?>', '<?php echo htmlspecialchars($foto['titulo']); ?>', '<?php echo htmlspecialchars($foto['descricao']); ?>')" 
                                 data-bs-toggle="modal" data-bs-target="#modalFoto" style="cursor: pointer;">
                                
                                <?php if (file_exists($foto['caminho'])): ?>
                                    <img src="<?php echo htmlspecialchars($foto['caminho']); ?>" 
                                         alt="<?php echo htmlspecialchars($foto['titulo']); ?>">
                                <?php else: ?>
                                    <img src="src/img/default.jpg" alt="Imagem não encontrada">
                                <?php endif; ?>
                                
                                
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="sem-fotos">
                    <i class="fas fa-camera"></i>
                    <h3>Nenhuma foto encontrada</h3>
                    <p>Esta categoria ainda não possui fotos cadastradas.</p>
                    <?php if (isset($_SESSION['cpf'])): ?>
                        <a href="photo-register.php?categoria_id=<?php echo $categoria_id; ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> Adicionar Primeira Foto
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para visualizar foto em tamanho grande -->
    <div class="modal fade" id="modalFoto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index: 1000;"></button>
                    <img id="fotoModal" src="" alt="" class="img-fluid">
                    <div class="text-white mt-3">
                        <h5 id="tituloModal"></h5>
                        <p id="descricaoModal"></p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function mostrarFoto(caminho, titulo, descricao) {
            document.getElementById('fotoModal').src = caminho;
            document.getElementById('tituloModal').textContent = titulo;
            document.getElementById('descricaoModal').textContent = descricao;
        }
    </script>
</body>

</html>

<?php
$con->Desconectar();
?>