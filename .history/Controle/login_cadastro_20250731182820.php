<?php
session_start(); // Inicia a sessão

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");

// Criar uma instância da conexão
$conexaoObj = new Conn();
$conexao = $conexaoObj->getConexao(); // Obtém a conexão

// Verifique se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["entrar"])) {
        $num_identificacao = $_POST['acesso']; // Pode ser email ou BI
        $senha = $_POST['senha'];
        $genero = $_POST['genero'];
        $lembrar = isset($_POST['lembrar']);

        // Criar instância do DTO e definir os valores
        $utilizadorDTO = new UtilizadorDTO();
        $utilizadorDTO->setNumIdentificacao($num_identificacao); //Pega o BI
        $utilizadorDTO->setGeneroUtilizador($genero); //Pega o BI
        $utilizadorDTO->setSenhaUtilizador($senha); // Pega a senha

        // Criar instância do DAO
        $utilizadorDAO = new UtilizadorDAO();

        // Busca o utilizador pelo campo 'acesso' (que pode ser email ou BI)
        $utilizador = $utilizadorDAO->autenticar($utilizadorDTO->getNumIdentificacao());

        if ($utilizador) {
            // Verifica a senha com password_verify()
            if (password_verify($senha, $utilizador->getSenhaUtilizador())) {
                // Guardar os dados no DTO
                $utilizadorDTO->setIdUtilizador($utilizador->getIdUtilizador());
                $utilizadorDTO->setNumIdentificacao($utilizador->getNumIdentificacao());


                // Guardar na sessão
                $_SESSION['idUtilizador'] = $utilizador->getIdUtilizador();
                $_SESSION['acesso'] = $utilizador->getNumIdentificacao();
                $_SESSION['nomeUtilizador'] = $utilizador->getNomeUtilizador();
                $_SESSION['perfilUtilizador'] = $utilizador->getPerfilUtilizador();

                // Se lembrar for ativado, guardar cookie
                if ($lembrar) {
                    setcookie('idUtilizador', $utilizadorDTO->getIdUtilizador(), time() + (30 * 24 * 60 * 60), "/");
                }
                //Lógica para redirecionar com base no tipo de acesso
                $acessoUpper = strtoupper($utilizadorDTO->getNumIdentificacao());

                // Regex para BI angolano: 9 números + 2 letras maiúsculas + 3 números
                if (preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $acessoUpper)) {


                    if ($utilizador->getPerfilUtilizador() == "Aluno") {
                        header("Location: ../Visao/indexAluno.php");
                        $_SESSION['success'] = "Login feito com sucesso!";
                        $_SESSION['icon'] = "success";
                        exit();
                    } elseif ($utilizador->getPerfilUtilizador() == "Administrador") {
                        header("Location: ../Visao/indexAdmin.php");
                        $_SESSION['success'] = "Login feito com sucesso!";
                        $_SESSION['icon'] = "success";
                        exit();
                    } elseif ($utilizador->getPerfilUtilizador() == "Professor") {
                        header("Location: ../Visao/indexProfessor.php");
                        $_SESSION['success'] = "Login feito com sucesso!";
                        $_SESSION['icon'] = "success";
                        exit();
                    }
                }
            } else {
                $_SESSION['success'] = "Credenciais inválidas!"; // Palavra passe
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = "Credenciais inválidas!"; //Caso não retorne nehum utilizador
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/index.php"); // Redireciona de volta para o login
            exit();
        }

        header("Location: ../Visao/index.php"); // Redireciona de volta para o login
        exit();
    }

    if (isset($_POST["cadastrar"])) {
        $nome = $_POST['nomeUtilizador'];
        $email = $_POST['emailUtilizador'];
        $telefone = $_POST['telefoneUtilizador'];
        $senha = $_POST['senha'];
        $acesso = $_POST['acesso'];
        $morada = $_POST['moradaUtilizador'];
        $perfil = "Administrador";
        $senhaEncriptada = password_hash($senha, PASSWORD_DEFAULT);


        // Crie as instâncias necessárias para cadastrar o novo usuário
        $utilizadorDAO = new UtilizadorDAO();
        $utilizadorDTO = new UtilizadorDTO();

        // Atribua os valores ao DTO
        $utilizadorDTO->setNomeUtilizador($nome);
        $utilizadorDTO->setEmailUtilizador($email);
        $utilizadorDTO->setTelefoneUtilizador($telefone);
        $utilizadorDTO->setSenhaUtilizador($senhaEncriptada);
        $utilizadorDTO->setIdAcesso(1);
        $utilizadorDTO->setMoradaUtilizador($morada);
        $utilizadorDTO->setPerfilUtilizador($perfil);
        $utilizadorDTO->setNumIdentificacao($acesso);


        // Tente cadastrar o usuário
        if ($utilizadorDAO->cadastrar($utilizadorDTO)) {
            $_SESSION['success'] = 'Cadastro feito com sucesso!';
            header('location: ../Visao/index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar';
        }
    }
}
