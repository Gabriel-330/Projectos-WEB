<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Cadastro</title>
    <meta charset="utf-8">
    <meta name="author" content="Estrela Dourada">
    <link rel="stylesheet" href="css/style-cadastro.css">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="imagens/graduate-cap-icone-head.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="js/alertsMessage.js"></script>
    <script src="js/sweetalert.js"></script>

    <style>
        #telefone {
            height: 58px;
            /* ou igual ao dos outros .form-floating */
            padding-left: 58px;
            /* espaço para a bandeira do intl-tel-input */
        }

        .iti {
            width: 100%;
        }
    </style>

</head>

<body>
    <?php
    // Verifica se a variável de sessão com a mensagem está definida
    if (isset($_SESSION['success']) && $_SESSION['success'] != '') {

    ?>
        <script>
            swal({
                title: '<?php echo $_SESSION['success']; ?>',
                icon: '<?php echo $_SESSION['icon']; ?>',
                button: "Ok",
            });
        </script>
    <?php

        unset($_SESSION['success']);
    }
    ?>
    <?php
    if (isset($_COOKIE['success'])) {
        echo '<script>
        swal({
            title: "' . htmlspecialchars($_COOKIE['success']) . '",
            icon: "' . htmlspecialchars($_COOKIE['icon']) . '",
            button: "Ok",
        });
    </script>';

        // Apaga os cookies para evitar exibição repetida
        setcookie("success", "", time() - 3600, "/");
        setcookie("icon", "", time() - 3600, "/");
    }
    ?>
    <div class="login-form">
        <div class="login-form-left">
            <h2>Bem-vindo ao Sistema de Gestão de Aluno do Estrela!</h2>
            <p>Aceda ao seu perfil, acompanhe o seu progresso académico e muito mais.
                Inicie sessão para continuar.</p>

            <div class="social-icons">
                <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
            </div>

        </div>
        <div class="login-form-right">
            <h1>Cadastro</h1>
            <form method="POST" action="../Controle/login_cadastro.php" autocomplete="off">

                <!-- Nome -->
                <div class="form-floating mt-3">
                    <input type="text" class="form-control rounded-1" placeholder=" " id="nome" name="nomeUtilizador" required>
                    <label for="nome">Nome</label>
                </div>

                <!-- Gênero -->
                <div class="form-floating mt-3">
                    <select class="form-control rounded-1" id="genero" name="genero" required>
                        <option value="">Selecione o gênero</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                    <label for="genero">Gênero</label>
                </div>

                <!-- Email -->
                <div class="form-floating mt-3">
                    <input type="email" class="form-control rounded-1" placeholder=" " id="email" name="emailUtilizador" required>
                    <label for="email">Email</label>
                </div>

                <!-- Telefone com intl-tel-input -->
                <div class="mt-3">
                    <input type="tel"
                        class="form-control rounded-1"
                        id="telefone"
                        name="telefoneUtilizador"
                        placeholder="Digite o número de telefone"
                        inputmode="numeric"
                        pattern="\+244\d{9}"
                        required>

                </div>

                <!-- Morada -->
                <div class="form-floating mt-3">
                    <input type="text" class="form-control rounded-1" placeholder=" " id="morada" name="moradaUtilizador" required>
                    <label for="morada">Morada</label>
                </div>

                <!-- Dados de Acesso -->
                <div class="form-floating mt-3">
                    <input type="text" class="form-control rounded-1" placeholder=" " id="acesso" name="acesso" required>
                    <label for="acesso">Nº de Identificação (BI/Passaporte)</label>
                </div>

                <!-- Senha -->
                <div class="form-floating mt-3">
                    <input type="password" class="form-control rounded-1" minlength="8" placeholder=" " id="senha" name="senha" required>
                    <label for="senha">Senha</label>
                </div>

                <!-- Checkbox -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check" name="lembrar" required>
                        <label class="form-check-label" for="check">Eu aceito as políticas de privacidade</label>
                    </div>
                </div>

                <!-- Botão -->
                <input type="submit" class="btn btn-primary rounded-1" name="cadastrar" value="Criar Conta">

                <!-- Link de login -->
                <div class="text-center mt-2">
                    <span>Possui uma conta? <a href="index.php" style="color:rgb(42, 93, 159); text-decoration:none;">Iniciar sessão</a></span>
                </div>

            </form>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script>
        const input = document.querySelector("#telefone");

        const iti = window.intlTelInput(input, {
            // Mostra apenas Angola
            onlyCountries: ["ao"],

            // Usa o código internacional (+244)
            nationalMode: false,

            // Remove a possibilidade de abrir o seletor de país
            allowDropdown: false,

            // Carrega utilitários da biblioteca
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
        });

        // Define o valor com +244 quando focar, se estiver vazio
        input.addEventListener("focus", function() {
            if (input.value.trim() === "") {
                const dialCode = iti.getSelectedCountryData().dialCode;
                input.value = `+${dialCode} `;
            }
        });

        // Garante que o campo só aceite dígitos e o símbolo +
        input.addEventListener("input", function() {
            this.value = this.value.replace(/[^\d+]/g, '');
        });
    </script>




</body>

</html>