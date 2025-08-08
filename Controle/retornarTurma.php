<?php
require_once("../Modelo/DAO/TurmaDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new TurmaDAO();
$turma = $dao->buscarPorId($id);

if ($turma) {
    echo json_encode([
        'idTurma'        => $turma->getIdTurma(),
        'nomeTurma'      => $turma->getNomeTurma(),
        'salaTurma'      => $turma->getSalaTurma(),
        'idCurso'        => $turma->getIdCurso(),
        'idProfessor'    => $turma->getIdProfessor(),
        'idAluno'        => $turma->getIdAluno()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Turma não encontrada']);
}
