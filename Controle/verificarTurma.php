<?php
session_start();
require_once '../Modelo/DAO/verificarTabelaDAO.php';

$dao = new verificarTabelaDAO();

if (!$dao->tabelaTemDados('Turma')) {
    $_SESSION['success'] = 'Deves inserir uma Turma primeiro!';
    $_SESSION['icon'] = 'warning';
    echo json_encode(['status' => 'redirect', 'redirect' => '../Visao/alunoBase.php']);
    exit();
}

echo json_encode(['status' => 'ok']);