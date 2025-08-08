<?php
require_once("../Modelo/DAO/MatriculaDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new MatriculaDAO();
$matricula = $dao->buscarMatriculaPorId($id);

if ($matricula) {
    echo json_encode([
        'idMatricula'        => $matricula->getIdMatricula(),
        'idAluno'            => $matricula->getIdAluno(),
        'idTurma'            => $matricula->getIdTurma(),
        'idCurso'            => $matricula->getIdCurso(),
        'dataMatricula'      => $matricula->getDataMatricula(),
        'estadoMatricula'    => $matricula->getEstadoMatricula(),
        'classeMatricula'    => $matricula->getClasseMatricula(),
        'periodoMatricula'   => $matricula->getPeriodoMatricula()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Matrícula não encontrada']);
}
