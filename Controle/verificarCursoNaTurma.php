<?php
session_start();
require_once '../Modelo/DAO/verificarTabelaDAO.php';

$dao = new verificarTabelaDAO();

if (!$dao->tabelaTemDados('Curso')) {
    $_SESSION['success'] = 'Deves inserir um curso primeiro!';
    $_SESSION['icon'] = 'warning';
    echo json_encode(['status' => 'redirect', 'redirect' => '../Visao/turmaBase.php']);
    exit();
}

echo json_encode(['status' => 'ok']);