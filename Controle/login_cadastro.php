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
        $num_identificacao = trim(filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING));
        $senha = $_POST['senha'] ?? '';
        $lembrar = isset($_POST['lembrar']);

        if (empty($num_identificacao) || empty($senha)) {
            $_SESSION['success'] = "Preencha todos os campos.";
            $_SESSION['icon'] = "warning";
            header("Location: ../Visao/index.php");
            exit();
        }

        $utilizadorDTO = new UtilizadorDTO();
        $utilizadorDTO->setNumIdentificacao($num_identificacao);
        $utilizadorDTO->setSenhaUtilizador($senha);

        $utilizadorDAO = new UtilizadorDAO();
        $utilizador = $utilizadorDAO->autenticar($utilizadorDTO->getNumIdentificacao());

        if ($utilizador && password_verify($senha, $utilizador->getSenhaUtilizador())) {

            // Guardar na sessão
            $_SESSION['idUtilizador'] = $utilizador->getIdUtilizador();
            $_SESSION['acesso'] = $utilizador->getNumIdentificacao();
            $_SESSION['nomeUtilizador'] = $utilizador->getNomeUtilizador();
            $_SESSION['perfilUtilizador'] = $utilizador->getPerfilUtilizador();
            $_SESSION['success'] = "Login feito com sucesso!";
            $_SESSION['icon'] = "success";

            // Cookie para lembrar o utilizador
            if ($lembrar) {
                setcookie('idUtilizador', $utilizador->getIdUtilizador(), time() + (30 * 24 * 60 * 60), "/");
            }

            // Direcionamento com base no perfil
            switch ($utilizador->getPerfilUtilizador()) {
                case "Aluno":
                    header("Location: ../Visao/indexAluno.php");
                    break;
                case "Administrador":
                    header("Location: ../Visao/indexAdmin.php");
                    break;
                case "Professor":
                    header("Location: ../Visao/indexProfessor.php");
                    break;
                default:
                    $_SESSION['success'] = "Perfil de utilizador desconhecido.";
                    $_SESSION['icon'] = "error";
                    header("Location: ../Visao/index.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['success'] = "Credenciais inválidas!";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/index.php");
            exit();
        }
    }

    // CADASTRO
    if (isset($_POST["cadastrar"])) {
        $nome     = trim($_POST['nomeUtilizador'] ?? '');
        $email    = trim($_POST['emailUtilizador'] ?? '');
        $telefone = trim($_POST['telefoneUtilizador'] ?? '');
        $senha    = $_POST['senha'] ?? '';
        $acesso   = trim($_POST['acesso'] ?? '');
        $genero   = $_POST['genero'] ?? '';
        $morada   = trim($_POST['moradaUtilizador'] ?? '');
        $perfil   = "Administrador";

        if (empty($nome) || empty($email) || empty($telefone) || empty($senha) || empty($acesso)) {
            $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos.';
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
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar.';
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/index.php');
            exit();
        }
    }
}
