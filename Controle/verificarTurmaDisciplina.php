<?php
session_start();
require_once '../Modelo/DAO/verificarTabelaDAO.php';

$dao = new verificarTabelaDAO();

if (!$dao->tabelaTemDados('Turma') && !$dao->tabelaTemDados('Disciplina')) {
    $_SESSION['success'] = 'Deves inserir uma disciplina e turma primeiro!';
    $_SESSION['icon'] = 'warning';
    echo json_encode(['status' => 'redirect', 'redirect' => '../Visao/horarioBase.php']);
    exit();
} elseif (!$dao->tabelaTemDados('Turma') && $dao->tabelaTemDados('Disciplina')) {
    $_SESSION['success'] = 'Deves inserir uma turma primeiro!';
    $_SESSION['icon'] = 'warning';
    echo json_encode(['status' => 'redirect', 'redirect' => '../Visao/horarioBase.php']);
    exit();
}

echo json_encode(['status' => 'ok']);
