<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="author" content="Estrela Dourada">
    <link rel="stylesheet" href="assets/scss/main.scss">
    <link rel="stylesheet" href="css/style-login.css">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="imagens/graduate-cap-icone-head.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="js/alertsMessage.js"></script>
    <script src="js/sweetalert.js"></script>
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
            <h2 style="color: #fff;">Bem-vindo ao Sistema de Gestão de Aluno do Estrela!</h2>
            <p>Aceda ao seu perfil, acompanhe o seu progresso académico e muito mais.
                Inicie sessão para continuar.</p>

            <div class="social-icons">
                <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
            </div>

        </div>
        <div class="login-form-right">
            <h1>Login</h1>
            <form method="POST" action="../Controle/login_cadastro.php" autocomplete="off">

                <div class="form-floating mt-3">
                    <input type="text" class="form-control rounded-1" id="acesso" name="acesso" placeholder="Digite seus dados de acesso" style="height: 50px;" required>
                    <label for="acesso" style="font-size: 13px;">Nº de Identificação (BI ou Passaporte)</label>
                </div>

                <div class="form-floating mt-3">
                    <input type="password" class="form-control rounded-1" id="senha" name="senha" placeholder="Digite sua senha" style="height: 50px;" required>
                    <label for="senha" style="font-size: 13px;">Senha</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input h-80" id="lembrar" name="lembrar">
                        <label class="form-check-label" for="lembrar">Lembrar de mim</label>
                    </div>
                    <a href="#" target="_blank">Esqueceu a senha?</a>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-1" name="entrar">Entrar</button>
                    <button type="button" class="btn btn-outline-primary rounded-1" data-bs-toggle="modal" data-bs-target="#modalHorarioApagar">
                        Área Restrita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalHorarioApagar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirme a sua Identidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Insira o código de Acesso</p>
                    <form id="formHorarioExcluir" method="POST" action="../Controle/acesso.php" autocomplete="off">
                        <input type="password" class="form-control form-control-sm rounded-1" name="numero_acesso" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formHorarioExcluir" class="btn btn-primary" name="aceder">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/vendor/global/global.min.js" type="text/javascript"></script>
    <script src="assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="assets/vendor/chart-js/chart.bundle.min.js" type="text/javascript"></script>
    <script src="assets/vendor/peity/jquery.peity.min.js" type="text/javascript"></script>
    <script src="assets/vendor/apexchart/apexchart.js" type="text/javascript"></script>
    <script src="assets/js/dashboard/dashboard-1.js" type="text/javascript"></script>
    <script src="assets/vendor/bootstrap-datetimepicker/js/moment.js" type="text/javascript"></script>
    <script src="assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="assets/js/custom.min.js" type="text/javascript"></script>
    <script src="assets/js/deznav-init.js" type="text/javascript"></script>
</body>

</html>