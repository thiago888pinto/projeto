<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" href="src/img/LogoSample_StrongPurple.png" type="image/png">
</head>

<body>
    <div class="btn-back">
        <i class='bx bx-arrow-back'></i>
        <a href="/projeto/viva.php">VOLTAR</a>
    </div>
    <div class="container">
        <div class="form-box login">
            <form action="src/controller/loginController.php" method="post">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="cpf" id="" placeholder="CPF" required>
                    <i class='bx bxs-id-card'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="email" id="" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="senha-login" placeholder="Senha" required>
                    <i class="fa-regular fa-eye-slash" id="eye-login" style="cursor: pointer;"></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <div class="form-box register">
            <form action="src/controller/registerController.php" method="post">
                <h1>Registrar</h1>
                <div class="input-box">
                    <input type="text" name="nome" id="" placeholder="Nome" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="cpf" id="" max="14" min="14" placeholder="CPF" required>
                    <i class='bx bxs-id-card'></i>
                </div>
                <div class="input-box">
                    <input type="date" name="dataNasc" id="" required>
                </div>
                <div class="input-box">
                    <input type="text" name="endereco" id="" placeholder="Endereço" required>
                    <i class='bx bxs-building-house'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" id="" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="senha-register" placeholder="Senha" required>
                    <i class="fa-regular fa-eye-slash" id="eye-register" style="cursor: pointer;"></i>
                </div>
                <button type="submit" class="btn">Registrar</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Bem-Vindo de volta!</h1>
                <p>Ainda não tem uma conta?</p>
                <button class="btn register-btn">Registrar</button>
            </div>
        </div>
        <div class="toggle-box">
            <div class="toggle-panel toggle-right">
                <h1>Olá, Bem-Vindo!</h1>
                <p>Já possui uma conta?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.querySelector('.container');
        const registerBtn = document.querySelector('.register-btn');
        const loginBtn = document.querySelector('.login-btn');

        registerBtn.addEventListener('click', () => {
            container.classList.add('active');
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove('active');
        });

        // Verifica o parâmetro na URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('register') && urlParams.get('register') === 'true') {
                container.classList.add('active');
            }
        });
    </script>

    <!--ALTERA A VISIBILIDADE DA SENHA DO LOGIN--->
    <script>
        // Altera a visibilidade da senha no campo de login
        let eyeLogin = document.getElementById("eye-login");
        let senhaLogin = document.getElementById("senha-login");

        eyeLogin.onclick = function() {
            if (senhaLogin.type === "password") {
                senhaLogin.type = "text"; // Altera o tipo do input para "text" (mostrar a senha)
                eyeLogin.classList.remove("fa-eye-slash"); // Remove o ícone de olho fechado
                eyeLogin.classList.add("fa-eye"); // Adiciona o ícone de olho aberto
            } else {
                senhaLogin.type = "password"; // Altera o tipo do input para "password" (esconder a senha)
                eyeLogin.classList.remove("fa-eye"); // Remove o ícone de olho aberto
                eyeLogin.classList.add("fa-eye-slash"); // Adiciona o ícone de olho fechado
            }
        };

        // Altera a visibilidade da senha no campo de registro
        let eyeRegister = document.getElementById("eye-register");
        let senhaRegister = document.getElementById("senha-register");

        eyeRegister.onclick = function() {
            if (senhaRegister.type === "password") {
                senhaRegister.type = "text"; // Altera o tipo do input para "text" (mostrar a senha)
                eyeRegister.classList.remove("fa-eye-slash"); // Remove o ícone de olho fechado
                eyeRegister.classList.add("fa-eye"); // Adiciona o ícone de olho aberto
            } else {
                senhaRegister.type = "password"; // Altera o tipo do input para "password" (esconder a senha)
                eyeRegister.classList.remove("fa-eye"); // Remove o ícone de olho aberto
                eyeRegister.classList.add("fa-eye-slash"); // Adiciona o ícone de olho fechado
            }
        };

    </script>
    <?php
    // Verifica o código de erro na URL
    if (isset($_GET['cod']) && $_GET['cod'] == '171') {
        echo "<script>alert('Erro: Dados incorretos. Por favor, tente novamente.');</script>";
    }

    if (isset($_GET['cod']) && $_GET['cod'] == '172') {
        echo "<script>alert('Erro: Já existe uma conta existente com algum desses dados.');</script>";
    }
    ?>
</body>

</html>