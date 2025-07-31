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
$Disciplina = $dao->buscarPorId($id); // Ajusta o nome do método se for diferente

if ($Disciplina) {
    echo json_encode([
        'idDisciplina'           => $Disciplina->getIdDisciplina(),
        'nomeDisciplina'           => $Disciplina->getNomeDisciplina(),
        'idCurso'   => $Disciplina->getIdCurso(),
        'idProfessor'         => $Disciplina->getidProfessor()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Disciplina não encontrado']);
}
