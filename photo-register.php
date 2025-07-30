<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header('Location: login.php');
    exit();
}

$mensagem = '';
$tipo_mensagem = '';
$categoria_selecionada = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : '';

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $categoria_id = intval($_POST['categoria_id']);
    
    if (empty($titulo)) {
        $mensagem = 'O título da foto é obrigatório!';
        $tipo_mensagem = 'danger';
    } elseif (empty($categoria_id)) {
        $mensagem = 'Selecione uma categoria!';
        $tipo_mensagem = 'danger';
    } elseif (!isset($_FILES['fotos']) || empty($_FILES['fotos']['name'][0])) {
        $mensagem = 'Selecione pelo menos uma foto para upload!';
        $tipo_mensagem = 'danger';
    } else {
        require_once 'src/ConexaoMysql.php';
        $con = new ConexaoMysql();
        $con->Conectar();
        
        // Verificar se a categoria existe
        $sqlCategoria = "SELECT nome FROM categorias_fotos WHERE id = '$categoria_id'";
        $resultCategoria = $con->Consultar($sqlCategoria);
        
        if (!$resultCategoria || $resultCategoria->num_rows == 0) {
            $mensagem = 'Categoria inválida!';
            $tipo_mensagem = 'danger';
        } else {
            $upload_dir = 'uploads/fotos/';
            
            // Criar diretório se não existir
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $arquivos_salvos = 0;
            $total_arquivos = count($_FILES['fotos']['name']);
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            for ($i = 0; $i < $total_arquivos; $i++) {
                if ($_FILES['fotos']['error'][$i] == 0) {
                    $arquivo_nome = $_FILES['fotos']['name'][$i];
                    $arquivo_tmp = $_FILES['fotos']['tmp_name'][$i];
                    $arquivo_ext = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));
                    
                    // Verificar se é uma imagem
                    if (in_array($arquivo_ext, $extensoes_permitidas)) {
                        $novo_nome = uniqid() . '_' . time() . '_' . $i . '.' . $arquivo_ext;
                        $caminho_arquivo = $upload_dir . $novo_nome;
                        
                        if (move_uploaded_file($arquivo_tmp, $caminho_arquivo)) {
                            // Inserir no banco de dados
                            $titulo_individual = $total_arquivos > 1 ? $titulo . ' - Foto ' . ($i + 1) : $titulo;
                            $sql = "INSERT INTO fotos (titulo, descricao, caminho, categoria_id) VALUES ('$titulo_individual', '$descricao', '$caminho_arquivo', '$categoria_id')";
                            
                            if ($con->Executar($sql)) {
                                $arquivos_salvos++;
                            }
                        }
                    }
                }
            }
            
            if ($arquivos_salvos > 0) {
                $mensagem = "$arquivos_salvos foto(s) adicionada(s) com sucesso!";
                $tipo_mensagem = 'success';
                
                // Limpar campos
                $titulo = '';
                $descricao = '';
            } else {
                $mensagem = 'Erro ao salvar as fotos. Verifique se são arquivos de imagem válidos.';
                $tipo_mensagem = 'danger';
            }
        }
        
        $con->Desconectar();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Fotos - Viva Vôlei</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/fotos.css">
    
    <link rel="icon" href="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" type="image/png">
    
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .form-title {
            color: #6f42c1;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        
        .btn-voltar {
            background: #6f42c1;
            color: white;
            padding: 0.7rem 2rem;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2rem;
            transition: all 0.3s;
        }
        
        .btn-voltar:hover {
            background: #5a359a;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .preview-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .file-drop-zone {
            border: 2px dashed #6f42c1;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .file-drop-zone:hover {
            background: #f0f2ff;
            border-color: #5a359a;
        }
        
        .file-drop-zone.dragover {
            background: #e8ebff;
            border-color: #5a359a;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .preview-container {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
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
                    <li class="nav-item"><a class="nav-link" href="categoria-register.php">Gerenciar Categorias</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-principal">
        <div class="conteudo-centralizado">
            <a href="fotos.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar para Galeria
            </a>
            
            <div class="form-container">
                <h2 class="form-title">
                    <i class="fas fa-camera"></i> Adicionar Fotos
                </h2>
                
                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">
                                    <i class="fas fa-heading"></i> Título *
                                </label>
                                <input type="text" class="form-control" id="titulo" name="titulo" 
                                       value="<?php echo isset($titulo) ? htmlspecialchars($titulo) : ''; ?>" 
                                       required maxlength="200">
                                <small class="form-text text-muted">Ex: Treino da Seleção, Final do Campeonato...</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">
                                    <i class="fas fa-folder"></i> Categoria *
                                </label>
                                <select class="form-control" id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    <?php
                                    require_once 'src/ConexaoMysql.php';
                                    $con = new ConexaoMysql();
                                    $con->Conectar();
                                    
                                    $sql = "SELECT * FROM categorias_fotos ORDER BY nome";
                                    $resultado = $con->Consultar($sql);
                                    
                                    if ($resultado && $resultado->num_rows > 0) {
                                        while ($cat = $resultado->fetch_assoc()) {
                                            $selected = ($categoria_selecionada == $cat['id']) ? 'selected' : '';
                                            echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['nome']) . '</option>';
                                        }
                                    }
                                    
                                    $con->Desconectar();
                                    ?>
                                </select>
                                <small class="form-text text-muted">
                                    Não encontrou a categoria? 
                                    <a href="categoria-register.php" target="_blank">Criar nova categoria</a>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">
                            <i class="fas fa-align-left"></i> Descrição
                        </label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                  maxlength="500"><?php echo isset($descricao) ? htmlspecialchars($descricao) : ''; ?></textarea>
                        <small class="form-text text-muted">Descrição sobre as fotos (opcional)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-images"></i> Fotos *
                        </label>
                        <div class="file-drop-zone" onclick="document.getElementById('fotos').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p><strong>Clique aqui ou arraste suas fotos</strong></p>
                            <p class="text-muted">JPG, PNG, GIF ou WebP - Máximo 10MB por foto</p>
                            <p class="text-muted">Você pode selecionar múltiplas fotos</p>
                        </div>
                        <input type="file" class="form-control" id="fotos" name="fotos[]" 
                               accept="image/*" multiple style="display: none;" onchange="previewFotos(this)">
                        
                        <div id="preview-container" class="preview-container"></div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Adicionar Fotos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; Viva Vôlei SM. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let selectedFiles = [];
        
        function previewFotos(input) {
            const container = document.getElementById('preview-container');
            container.innerHTML = '';
            selectedFiles = Array.from(input.files);
            
            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-btn" onclick="removeFile(${index})">×</button>
                        `;
                        container.appendChild(div);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }
        
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            previewFotos(document.getElementById('fotos'));
        }
        
        function updateFileInput() {
            const input = document.getElementById('fotos');
            const dt = new DataTransfer();
            
            selectedFiles.forEach(file => {
                dt.items.add(file);
            });
            
            input.files = dt.files;
        }
        
        // Drag and drop functionality
        const dropZone = document.querySelector('.file-drop-zone');
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            const input = document.getElementById('fotos');
            input.files = files;
            previewFotos(input);
        });
    </script>
</body>

</html>