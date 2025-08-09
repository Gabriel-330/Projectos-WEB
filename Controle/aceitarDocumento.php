<?php
session_start();
require_once '../Modelo/DAO/DocumentoDAO.php';

if (isset($_POST["aceitarDocumento"], $_POST["idDocumento"])) {
    $idDocumento = $_POST["idDocumento"];
    $DocumentoDAO = new DocumentoDAO();

    if ($DocumentoDAO->AceitarDocumento($idDocumento)) {
        $_SESSION['success'] = 'Documento aceite com sucesso!';
        $_SESSION['icon'] = 'success';
    } else {
        $_SESSION['success'] = 'Erro ao aceitar o Documento!';
        $_SESSION['icon'] = 'error';
    }

    header("Location: ../Visao/documentoBase.php");
    exit;
} else {
    echo "ID inválido.";
}
