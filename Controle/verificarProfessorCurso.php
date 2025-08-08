<?php
session_start();
require_once '../Modelo/DAO/verificarTabelaDAO.php';

$dao = new verificarTabelaDAO();

if (!$dao->tabelaTemDados('professor') && !$dao->tabelaTemDados('curso')) {
    $_SESSION['success'] = 'Deves inserir uma curso e professor primeiro!';
    $_SESSION['icon'] = 'warning';
    echo json_encode(['status' => 'redirect', 'redirect' => '../Visao/disciplinaBase.php']);
    exit();
} elseif (!$dao->tabelaTemDados('professor') && $dao->tabelaTemDados('curso')) {
    $_SESSION['success'] = 'Deves inserir uma professor primeiro!';
    $_SESSION['icon'] = 'warning';
    echo json_encode(['status' => 'redirect', 'redirect' => '../Visao/disciplinaBase.php']);
    exit();
} 

echo json_encode(['status' => 'ok']);
