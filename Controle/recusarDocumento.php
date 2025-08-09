<?php
session_start();
require_once '../Modelo/DAO/DocumentoDAO.php';

if (isset($_POST["recusarDocumento"], $_POST["idDocumento"])) {
    $idDocumento = $_POST["idDocumento"];
    $DocumentoDAO = new DocumentoDAO();

    if ($DocumentoDAO->RecusarDocumento($idDocumento)) {
        $_SESSION['success'] = 'Documento recusado com sucesso!';
        $_SESSION['icon'] = 'success';
    } else {
        $_SESSION['success'] = 'Erro ao recusar o Documento!';
        $_SESSION['icon'] = 'error';
    }
    if ($_SESSION['perfilUtilizador'] == 'Administrador') {
        header("Location: ../Visao/documentoBase.php");
    }else{

    }

    exit;
} else {
    echo "ID inválido.";
}
