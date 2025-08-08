<?php
require_once("../Modelo/DAO/DisciplinaDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new DisciplinaDAO();
$disciplina = $dao->buscarPorId($id);

if ($disciplina) {
    echo json_encode([
        'idDisciplina'    => $disciplina->getIdDisciplina(),
        'nomeDisciplina'  => $disciplina->getNomeDisciplina(),
        'classeDisciplina'=> $disciplina->getClasseDisciplina(),
        'idCurso'         => $disciplina->getIdCurso(),
        'idProfessor'     => $disciplina->getIdProfessor(),
 
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Disciplina não encontrada']);
}
