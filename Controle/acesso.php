<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/acessoDAO.php");
require_once("../Modelo/DTO/acessoDTO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["aceder"])) {
    $n_acesso = $_POST['numero_acesso'];
    $acessoDTO = new acessoDTO();
    $acessoDTO->setAcesso($n_acesso);
    $acessoDAO = new acessoDAO();

    // Busca o utilizador pelo campo 'acesso' (que pode ser email ou BI)
    $aceder = $acessoDAO->autenticar_acesso($acessoDTO->getAcesso());
    if ($aceder) {
        header("Location: ../Visao/cadastro.php");
        $_SESSION['success'] = "Acesso permitido!";
        $_SESSION['icon'] = "success";
        exit();
    } else {
        header("Location: ../Visao/index.php");
        $_SESSION['success'] = "Acesso negado!";
        $_SESSION['icon'] = "error";
        exit();
    }
}
