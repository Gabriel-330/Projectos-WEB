<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/acessoDAO.php");
require_once("../Modelo/DTO/acessoDTO.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["aceder"])) {
    // Sanitizar o input para evitar injeções simples
    $n_acesso = trim(filter_input(INPUT_POST, 'numero_acesso', FILTER_SANITIZE_SPECIAL_CHARS));

    if (!empty($n_acesso)) {
        // Criar DTO e definir o número de acesso
        $acessoDTO = new acessoDTO();
        $acessoDTO->setAcesso($n_acesso);

        // Criar DAO e tentar autenticar
        $acessoDAO = new acessoDAO();
        $utilizador = $acessoDAO->autenticar_acesso($acessoDTO->getAcesso());

        if ($utilizador) {
            // Guardar dados de Acesso ANTES do redirecionamento
            $_SESSION['Acesso'] = $acessoDTO->getAcesso();

            // Armazenar info de sessão antes do redirecionamento
            $_SESSION['success'] = "Acesso permitido!";
            $_SESSION['icon'] = "success";

            header("Location: ../Visao/cadastro.php");
            exit();
        } else {
            $_SESSION['success'] = "Acesso negado!";
            $_SESSION['icon'] = "error";

            header("Location: ../Visao/index.php");
            exit();
        }
    } else {
        $_SESSION['success'] = "Por favor, introduza o número de acesso.";
        $_SESSION['icon'] = "warning";

        header("Location: ../Visao/index.php");
        exit();
    }
}
