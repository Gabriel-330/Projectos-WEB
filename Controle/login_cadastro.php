<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");

$conexaoObj = new Conn();
$conexao = $conexaoObj->getConexao();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // LOGIN
    if (isset($_POST["entrar"])) {
        $num_identificacao = trim(filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_SPECIAL_CHARS));
        $senha = $_POST['senha'] ?? '';
        $lembrar = isset($_POST['lembrar']);

        if (empty($num_identificacao) || empty($senha)) {
            $_SESSION['error'] = "Preencha todos os campos.";
            header("Location: ../Visao/index.php");
            exit();
        }

        $utilizadorDAO = new UtilizadorDAO();
        $utilizador = $utilizadorDAO->autenticar($num_identificacao);

        if ($utilizador && password_verify($senha, $utilizador->getSenhaUtilizador())) {

            // Guardar na sessão
            $_SESSION['idUtilizador'] = $utilizador->getIdUtilizador();
            $_SESSION['acesso'] = $utilizador->getNumIdentificacao();
            $_SESSION['nomeUtilizador'] = $utilizador->getNomeUtilizador();
            $_SESSION['perfilUtilizador'] = $utilizador->getPerfilUtilizador();
            $_SESSION['success'] = "Login feito com sucesso!";

            // Cookie para lembrar o utilizador (com flags de segurança)
            if ($lembrar) {
                setcookie(
                    'idUtilizador',
                    $utilizador->getIdUtilizador(),
                    time() + (30 * 24 * 60 * 60),
                    "/",
                    "",  // seu domínio se quiser especificar
                    true, // Secure (HTTPS only)
                    true  // HttpOnly (não acessível via JS)
                );
            }

            // Direcionamento com base no perfil
            switch ($utilizador->getPerfilUtilizador()) {
                case "Aluno":
                    $_SESSION['success'] = "Login feito com sucesso!";
                    $_SESSION['icon'] = 'success';
                    header("Location: ../Visao/indexAluno.php");
                    exit();
                    break;
                case "Administrador":
                    $_SESSION['success'] = "Login feito com sucesso!";
                    $_SESSION['icon'] = 'success';
                    header("Location: ../Visao/indexAdmin.php");
                    exit();
                    break;
                case "Professor":
                    $_SESSION['success'] = "Login feito com sucesso!";
                    $_SESSION['icon'] = 'success';
                    header("Location: ../Visao/indexProfessor.php");
                    exit();
                    break;
                default:
                    // Mensagem genérica para perfis desconhecidos
                    $_SESSION['success'] = "Não foi possível identificar seu perfil.";
                    header("Location: ../Visao/index.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Credenciais inválidas!";
            header("Location: ../Visao/index.php");
            exit();
        }
    }

    // CADASTRO
    if (isset($_POST["cadastrar"])) {
        $nome     = trim($_POST['nomeUtilizador'] ?? '');
        $email    = filter_var(trim($_POST['emailUtilizador'] ?? ''), FILTER_VALIDATE_EMAIL);
        $telefone = trim($_POST['telefoneUtilizador'] ?? '');
        $senha    = $_POST['senha'] ?? '';
        $acesso   = trim($_POST['acesso'] ?? '');
        $genero   = $_POST['genero'] ?? '';
        $morada   = trim($_POST['moradaUtilizador'] ?? '');
        $perfil   = "Administrador";

        if (empty($nome) || !$email || empty($telefone) || empty($senha) || empty($acesso)) {
            $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos corretamente.';
            header('Location: ../Visao/index.php');
            exit();
        }

        $senhaEncriptada = password_hash($senha, PASSWORD_DEFAULT);

        $utilizadorDTO = new UtilizadorDTO();
        $utilizadorDTO->setNomeUtilizador($nome);
        $utilizadorDTO->setEmailUtilizador($email);
        $utilizadorDTO->setTelefoneUtilizador($telefone);
        $utilizadorDTO->setGeneroUtilizador($genero);
        $utilizadorDTO->setSenhaUtilizador($senhaEncriptada);
        $utilizadorDTO->setIdAcesso(1);
        $utilizadorDTO->setMoradaUtilizador($morada);
        $utilizadorDTO->setPerfilUtilizador($perfil);
        $utilizadorDTO->setNumIdentificacao($acesso);

        $utilizadorDAO = new UtilizadorDAO();
        if ($utilizadorDAO->cadastrar($utilizadorDTO)) {
            $_SESSION['success'] = 'Cadastro feito com sucesso!';
            header('Location: ../Visao/index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar.';
            header('Location: ../Visao/index.php');
            exit();
        }
    }
}
