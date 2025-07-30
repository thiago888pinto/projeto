<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header('Location: login.php');
    exit();
}

$mensagem = '';
$tipo_mensagem = '';

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    
    if (empty($nome)) {
        $mensagem = 'O nome da categoria é obrigatório!';
        $tipo_mensagem = 'danger';
    } else {
        // Gerar slug automaticamente
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nome)));
        
        require_once 'src/ConexaoMysql.php';
        $con = new ConexaoMysql();
        $con->Conectar();
        
        // Verificar se o slug já existe
        $sqlCheck = "SELECT id FROM categorias_fotos WHERE slug = '$slug'";
        $resultCheck = $con->Consultar($sqlCheck);
        
        if ($resultCheck && $resultCheck->num_rows > 0) {
            $mensagem = 'Já existe uma categoria com esse nome!';
            $tipo_mensagem = 'danger';
        } else {
            $imagem_capa = '';
            
            // Processar upload da imagem de capa
            if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] == 0) {
                $upload_dir = 'uploads/categorias/';
                
                // Criar diretório se não existir
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $arquivo_nome = $_FILES['imagem_capa']['name'];
                $arquivo_tmp = $_FILES['imagem_capa']['tmp_name'];
                $arquivo_ext = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));
                
                // Verificar se é uma imagem
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($arquivo_ext, $extensoes_permitidas)) {
                    $novo_nome = uniqid() . '_' . time() . '.' . $arquivo_ext;
                    $caminho_arquivo = $upload_dir . $novo_nome;
                    
                    if (move_uploaded_file($arquivo_tmp, $caminho_arquivo)) {
                        $imagem_capa = $caminho_arquivo;
                    }
                }
            }
            
            // Inserir categoria no banco
            $sql = "INSERT INTO categorias_fotos (nome, slug, descricao, imagem_capa) VALUES ('$nome', '$slug', '$descricao', '$imagem_capa')";
            
            if ($con->Executar($sql)) {
                $mensagem = 'Categoria criada com sucesso!';
                $tipo_mensagem = 'success';
                
                // Limpar campos
                $nome = '';
                $descricao = '';
            } else {
                $mensagem = 'Erro ao criar categoria. Tente novamente.';
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
    <title>Gerenciar Categorias - Viva Vôlei</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" type="image/png">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            
        }

        body{
            background:linear-gradient(whitesmoke 10% ,#ede561 30%,#edb65e 70%,whitesmoke 95%); 
        }

        
        .logo {
            max-height: 110px;
            margin-top: -15px;
            margin-bottom: -15px;
            margin-left: 5px;
        }

        .centralizar {
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            margin-top: -20px;
        }


        .navbar {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            height: 130px;
            background: linear-gradient(pink, #ede561 );
            color: #2e3189;
        }

        .navbar a {
            position: relative;
            font-size: 24px;
            color: #2e3189;
            text-decoration: none;
            font-weight: 500;
            margin-left: 40px;
            margin-right: 40px;
        }

        .navbar a::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            width: 0;
            height: 2px;
            background: rgb(94, 67, 249); 
            transition: .3s;
        }

        .navbar a:hover::before {
            width: 100%;
        }

        .navbar-nav {
            text-align: center;
        }

        .navbar-nav .nav-link {
            padding: 10px 20px;
            font-size: 1.1rem;
        }

        @media (max-width: 992px) {
            .navbar-collapse {
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
                border-bottom-left-radius: 40px;
                border-bottom-right-radius: 40px;
            }
        }

        .btn {
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #ff00a6ff;
            color: white;
        }
        .section {
            padding: 50px 0;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease, transform 1s ease;
        }
        .section.visible {
            opacity: 1;
            transform: translateY(0);
            margin-top: 50px;
        }
        .feature {
            padding: 40px;
            border-radius: 10px;
        }
        .feature h2 {
            text-align: center;
        }
        .feature img {
            max-width: 100%;
            border-radius: 10px;
        }
        
        footer {
            background-color: whitesmoke;
            padding: 20px 0;
            text-align: center;
            margin-top: 130px;
        }

        .alert{
            width: 600px;
            margin-left: calc(50% - 300px);
            margin-top: 28px;
            margin-bottom: -1px;
            text-align: center;
        }

        .containerslide {
            max-width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 50vh; 
            margin-top: 20px; 
            position: relative;
            margin: auto; 
        }
        
        .slider-wrapper {
            position: relative;
            max-width: 100%;  
            margin: 0 auto;
            overflow: hidden;
            text-align: center;
        }
        
        .slider {
            align-self: center;
            display:flex;
            aspect-ratio: 25 / 9;  
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            box-shadow: 0 1.5rem 3rem -0.75rem hsla(0, 0%, 0%, 0.25);
            border-radius: 0.5rem;
            -ms-overflow-style: none; 
            scrollbar-width: none; 
            width: 100%; 
            height: 80vh;  
            transition: 1.5s ease;
            
        }
        
        .slider::-webkit-scrollbar {
            display: none; 
        }
        
        .slider img {
            flex: 1 0 100%;
            scroll-snap-align: start;
            object-fit: cover;  
            min-width: 100%; 
            height: auto; 
        }
        
     
        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0);
            color: white;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            z-index: 2;
            padding: 1rem;
            border-radius: 50%;
            opacity: 0.75;
            transition: opacity 0.3s ease;
        }
        
        .slider-nav:hover {
            opacity: 1;
        }
        
        .left {
            left: 10px;
        }
        
        .right {
            right: 10px; 
        }
        
        
        @media (max-width: 768px) {
            .slider-wrapper {
                max-width: 95%;  
            }
        
            .slider {
                height: 50vh;  
            }
        }
        .noticias-wrapper-1{
            width: 70%;
            position: relative;
            margin: 20px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1;
        }

        .noticias-wrapper-1 img {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
        }

        .texto-noticia {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 60%, transparent 100%);
            color: white;
            padding: 20px;
            box-sizing: border-box;
            text-align: left;
            z-index: 10;
            min-height: 120px;
        }

        .texto-noticia h4 {
            margin-bottom: 10px;
            font-size: 1.3rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        }

        .texto-noticia p {
            margin-bottom: 8px;
            line-height: 1.4;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        .container-principal {
        display: flex;
        justify-content: center;
        min-height: 100vh;
        width: 100%;
        border-radius: 0px;
        margin-top: 30px;
        }

        
        .conteudo-centralizado {
        background-color: whitesmoke;
        width: 100%;
        max-width: 1000px; 
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
        }
        .noticiao{
            text-align: center;
            margin-top: 15px;
        }
        .paginacao {
            display: flex;
            justify-content: center;
            gap: 8px; 
            margin: 20px 0;
            margin-top: 30px;
        }

        .paginacao button, .numero-pagina {
            padding: 8px 16px;
            text-decoration: none;
            background-color: #ffffff;
            border: 1px solid;
            border-radius: 4px;
            transition: all 0.3s;
            border-color: #000000;
        }
        .numero-pagina-ativo{
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid;
            border-radius: 4px;
            transition: all 0.3s ease;
            background-color: #007bff;
            color: white;
            border-color: #007bff;

        }
        
        .numero-pagina :hover{
            background-color: #d9ff00;

        }

            .noticia-detalhe{
            border:2px solid;
            width: 70%;
            padding-top: 5%;
            border: blue;
            position: relative;
            margin-left: 5pc;
            
                    
        }
            .container-principal-noticia{
            display: flex;
            justify-content: center;
            min-height: 180vh;
            width: 100%;
            border-radius: 0px;
            margin-top: 30px;
        }
            .noticiao-detalhe{
            text-align: center;
            color:#2e3189 ;
        }    
            .paragrafos-noticia{
            font-weight: 300;
            color: #3a3f3f;
            line-height: 1.8;    

        }
        .texto-noticia-detalhe{
            margin-top: 15px;
        }
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
        
        .preview-imagem {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 1rem;
        }
        
        .categorias-existentes {
            margin-top: 3rem;
        }
        
        .categoria-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .categoria-info {
            display: flex;
            align-items: center;
        }
        
        .categoria-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }
        
        .categoria-acoes {
            display: flex;
            gap: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .categoria-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .categoria-acoes {
                margin-top: 1rem;
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <img src="src/img/369951376_309555475108077_7449468456577851380_n-removebg-preview.png" alt="Logo" class="img-fluid logo" id="logo">
            <a href="viva.php"><h2>Viva Vôlei</h2></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="noticias.php">Notícias</a></li>
                    <li class="nav-item"><a class="nav-link" href="fotos.php">Fotos</a></li>
                    <li class="nav-item"><a class="nav-link" href="contatos.php">Contatos</a></li>
                    <li class="nav-item"><a class="nav-link" href="photo-register.php">Registrar Fotos</a></li>
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
                    <i class="fas fa-folder-plus"></i> Gerenciar Categorias
                </h2>
                
                <?php
                // Exibir mensagens de URL
                if (isset($_GET['msg'])) {
                    switch($_GET['msg']) {
                        case 'categoria_excluida':
                            echo '<div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    <strong>Sucesso!</strong> Categoria excluída com sucesso!
                                  </div>';
                            break;
                        case 'erro_excluir':
                            echo '<div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    <strong>Erro!</strong> Não foi possível excluir a categoria. Tente novamente.
                                  </div>';
                            break;
                    }
                }
                ?>
                
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
                                <label for="nome" class="form-label">
                                    <i class="fas fa-tag"></i> Nome da Categoria *
                                </label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" 
                                       required maxlength="100">
                                <small class="form-text text-muted">Ex: Campeonato 2025, Treinos de Base...</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="imagem_capa" class="form-label">
                                    <i class="fas fa-image"></i> Imagem de Capa
                                </label>
                                <input type="file" class="form-control" id="imagem_capa" name="imagem_capa" 
                                       accept="image/*" onchange="previewImagem(this)">
                                <small class="form-text text-muted">JPG, PNG, GIF ou WebP (opcional)</small>
                                <img id="preview" class="preview-imagem" style="display: none;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">
                            <i class="fas fa-align-left"></i> Descrição
                        </label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                  maxlength="500"><?php echo isset($descricao) ? htmlspecialchars($descricao) : ''; ?></textarea>
                        <small class="form-text text-muted">Breve descrição sobre esta categoria</small>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Criar Categoria
                        </button>
                    </div>
                </form>
                
                <!-- Listar categorias existentes -->
                <div class="categorias-existentes">
                    <h4><i class="fas fa-list"></i> Categorias Existentes</h4>
                    
                    <?php
                    require_once 'src/ConexaoMysql.php';
                    $con = new ConexaoMysql();
                    $con->Conectar();
                    
                    $sql = "SELECT cf.*, COUNT(f.id) as total_fotos FROM categorias_fotos cf 
                            LEFT JOIN fotos f ON cf.id = f.categoria_id 
                            GROUP BY cf.id ORDER BY cf.created_at DESC";
                    $resultado = $con->Consultar($sql);
                    
                    if ($resultado && $resultado->num_rows > 0) {
                        while ($categoria = $resultado->fetch_assoc()) {
                            echo '<div class="categoria-item">';
                            echo '<div class="categoria-info">';
                            
                            if ($categoria['imagem_capa'] && file_exists($categoria['imagem_capa'])) {
                                echo '<img src="' . htmlspecialchars($categoria['imagem_capa']) . '" alt="' . htmlspecialchars($categoria['nome']) . '">';
                            } else {
                                echo '<div style="width: 60px; height: 60px; background: #6f42c1; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">';
                                echo '<i class="fas fa-image text-white"></i></div>';
                            }
                            
                            echo '<div>';
                            echo '<h6 class="mb-1">' . htmlspecialchars($categoria['nome']) . '</h6>';
                            echo '<small class="text-muted">' . $categoria['total_fotos'] . ' foto(s)</small><br>';
                            echo '<small class="text-muted">' . htmlspecialchars($categoria['descricao']) . '</small>';
                            echo '</div>';
                            echo '</div>';
                            
                            echo '<div class="categoria-acoes">';
                            echo '<a href="galeria-categoria.php?id=' . $categoria['id'] . '" class="btn btn-sm btn-info" title="Ver fotos">';
                            echo '<i class="fas fa-eye"></i></a>';
                            echo '<button class="btn btn-sm btn-warning" onclick="editarCategoria(' . $categoria['id'] . ')" title="Editar">';
                            echo '<i class="fas fa-edit"></i></button>';
                            echo '<button class="btn btn-sm btn-danger" onclick="excluirCategoria(' . $categoria['id'] . ', \'' . htmlspecialchars($categoria['nome']) . '\')" title="Excluir">';
                            echo '<i class="fas fa-trash"></i></button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="text-muted text-center">Nenhuma categoria cadastrada ainda.</p>';
                    }
                    
                    $con->Desconectar();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; Viva Vôlei. Todos os direitos reservados.</p>
            <p>Contato: <a href="#">seuemail@exemplo.com</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function previewImagem(input) {
            const preview = document.getElementById('preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
        
        function excluirCategoria(id, nome) {
            if (confirm(`Tem certeza que deseja excluir a categoria "${nome}"?\nTodas as fotos desta categoria também serão excluídas!`)) {
                window.location.href = `categoria-delete.php?id=${id}`;
            }
        }
        
        function editarCategoria(id) {
            // Por enquanto, redirecionar para a mesma página com ID
            // Você pode implementar um modal de edição aqui
            alert('Funcionalidade de edição será implementada em breve!');
        }
    </script>
</body>

</html>