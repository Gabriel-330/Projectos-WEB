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
$Matricula = $dao->buscarMatriculaPorId($id); // Ajusta o nome do método se for diferente

if ($Matricula) {
    echo json_encode([
        'idMatricula'           => $Matricula->getIdMatricula(),
        'idAluno'           => $Matricula->getIdAluno(),
        'idTurma'   => $Matricula->getIdTurma(),
        'dataMatricula'         => $Matricula->getDataMatricula(),
        'estadoMatricula'         => $Matricula->getEstadoMatricula(),
        'idCurso'       => $Matricula->getIdCurso()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Matricula não encontrado']);
}
