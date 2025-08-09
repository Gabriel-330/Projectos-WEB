<?php
session_start();
require_once '../Modelo/DAO/DocumentoDAO.php';

if (isset($_POST["validarDocumento"], $_POST["idDocumento"])) {
    $idDocumento = $_POST["idDocumento"];
    $DocumentoDAO = new DocumentoDAO();

    if ($DocumentoDAO->ValidarDocumento($idDocumento)) {
        $_SESSION['success'] = 'Documento validado com sucesso!';
        $_SESSION['icon'] = 'success';
    } else {
        $_SESSION['success'] = 'Erro ao validar o Documento!';
        $_SESSION['icon'] = 'error';
    }

    header("Location: ../Visao/documentoProfessorBase.php");
    exit;
} else {
    echo "ID inválido.";
}
