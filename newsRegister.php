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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Registrar Notícias</title>
    <link rel="stylesheet" href="css/newsRegister.css">
    <link rel="icon" href="src/img/LogoSample_StrongPurple.png" type="image/png">
</head>

<body>
    <div class="btn-back">
        <i class='bx bx-arrow-back'></i>
        <a href="noticias.php">VOLTAR</a>
    </div>
    <div class="container">
        <div class="form-box login">
            <div class="subContainter">
                <h2>Notícias</h2>
                <?php
                require_once 'src/controller/newsController.php';
                require_once 'src/ConexaoMysql.php';

                $cpf = $_SESSION['cpf'];
                $noticias = noticiasRegistradas($cpf);

                if (!empty($noticias)) {
                    foreach ($noticias as $noticia) {
                        echo '<div class="news-card">';
                        echo '<a href="noticias.php">' . htmlspecialchars($noticia['nomeNoticia']) . '</a>';
                        echo '<p><strong>Matéria:</strong> ' . htmlspecialchars($noticia['materia'] ?? 'N/A') . '</p>';
                        echo '<p><strong>Texto:</strong> ' . htmlspecialchars(substr($noticia['texto'] ?? 'N/A', 0, 100)) . '...</p>';
                        echo '<p><strong>Autor:</strong> ' . htmlspecialchars($noticia['autor'] ?? 'N/A') . '</p>';

                        // Adicionando os botões de editar e excluir
                        echo '<div class="action-buttons">';
                        echo '<a href="editNoticia.php?id=' . $noticia['idNoticia'] . '" class="btn-edit">Editar</a>';
                        echo '<a href="deleteNoticia.php?id=' . $noticia['idNoticia'] . '" class="btn-delete" onclick="return confirm(\'Tem certeza que deseja excluir esta notícia?\');">Excluir</a>';
                        echo '</div>';

                        // Buscar a primeira imagem da notícia
                        $con = new ConexaoMysql();
                        $con->Conectar();
                        $idNoticia = $noticia['idNoticia'];
                        $sqlImagem = "SELECT caminho FROM imagens WHERE noticia_id = '$idNoticia' LIMIT 1";
                        $resImg = $con->Consultar($sqlImagem);

                        if ($resImg && $resImg->num_rows > 0) {
                            $img = $resImg->fetch_assoc();
                            $imagemPath = htmlspecialchars($img['caminho']);
                            if (file_exists($imagemPath)) {
                                echo '<img src="' . $imagemPath . '" alt="Imagem da notícia">';
                            } else {
                                echo '<img src="src/img/default.jpg" alt="Imagem padrão">';
                            }
                        } else {
                            echo '<img src="src/img/default.jpg" alt="Imagem padrão">';
                        }

                        $con->Desconectar();

                        echo '<p><small><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($noticia['created_at'])) . '</small></p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Nenhuma notícia encontrada.</p>';
                }
                ?>
            </div>
        </div>

        <div class="form-box register">
            <form action="src/controller/newsRegisterController.php" method="post" enctype="multipart/form-data">
                <h1>Registro de Notícia</h1>

                <div class="input-box">
                    <input type="text" name="nomeNoticia" placeholder="Nome da notícia" required maxlength="100">
                    <i class='bx bxs-news'></i>
                </div>

                <div class="input-box">
                    <input type="text" name="materia" placeholder="Matéria" maxlength="50">
                    <i class='bx bxs-file'></i>
                </div>

                <div class="input-box">
                    <textarea name="texto" placeholder="Texto da notícia (máximo 1000 caracteres)" required maxlength="1000" rows="4"></textarea>
                    <i class='bx bxs-message'></i>
                </div>

                <div class="input-box">
                    <label for="numImagens" class="form-label">Número de Imagens</label>
                    <select class="form-control" id="numImagens" name="numImagens" onchange="gerarCamposImagens()" required>
                        <option value="0">Selecione o número de imagens</option>
                        <?php
                        for ($i = 1; $i <= 7; $i++) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="imagensContainer"></div>

                <div class="input-box">
                    <input type="text" name="autor" placeholder="Autor" maxlength="50">
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="text" name="cpf" value="<?php echo $_SESSION['cpf']; ?>" readonly required>
                    <i class='bx bxs-id-card'></i>
                </div>

                <button type="submit" class="btn">Registrar Notícia</button>
            </form>
        </div>

        <script>
            function gerarCamposImagens() {
                let numImagens = document.getElementById('numImagens').value;
                let container = document.getElementById('imagensContainer');
                container.innerHTML = '';

                for (let i = 1; i <= numImagens; i++) {
                    let div = document.createElement('div');
                    div.classList.add('mb-3');

                    let fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.name = 'fotos[]';
                    fileInput.classList.add('form-control');
                    fileInput.accept = 'image/*';
                    div.appendChild(fileInput);

                    let labelDesc = document.createElement('label');
                    labelDesc.classList.add('form-label');
                    labelDesc.innerText = 'Descrição da Imagem ' + i;
                    div.appendChild(labelDesc);

                    let textArea = document.createElement('textarea');
                    textArea.name = 'descricao[]';
                    textArea.classList.add('form-control');
                    div.appendChild(textArea);

                    container.appendChild(div);
                }
            }
        </script>
    </div>

    <?php
    if (isset($_GET['cod'])) {
        switch ($_GET['cod']) {
            case '100':
                echo "<script>alert('Notícia cadastrada com sucesso!');</script>";
                break;
            case '111':
                echo "<script>alert('Notícia excluída com sucesso!');</script>";
                break;
            case '500':
                echo "<script>alert('Erro ao fazer upload da imagem.');</script>";
                break;
            case '501':
                echo "<script>alert('A imagem deve ter no máximo 5MB.');</script>";
                break;
            case '502':
                echo "<script>alert('Formato de arquivo não permitido. Use: JPG, JPEG, PNG, GIF ou WEBP.');</script>";
                break;
            case '503':
                echo "<script>alert('Erro no upload da imagem.');</script>";
                break;
            case '504':
                echo "<script>alert('Erro ao salvar notícia no banco de dados.');</script>";
                break;
            case '505':
                echo "<script>alert('Erro no banco de dados.');</script>";
                break;
        }
    }

    if (isset($_SESSION['sucesso'])) {
        echo "<script>alert('" . $_SESSION['sucesso'] . "');</script>";
        unset($_SESSION['sucesso']);
    }
    if (isset($_SESSION['erro'])) {
        echo "<script>alert('" . $_SESSION['erro'] . "');</script>";
        unset($_SESSION['erro']);
    }
    ?>
</body>

</html>
