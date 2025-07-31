<?php
require_once("../Modelo/DAO/CursoDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new CursoDAO();
$Curso = $dao->MostrarPorId($id); // Ajusta o nome do método se for diferente

if ($Curso) {
    echo json_encode([
        'idCurso'           => $Curso->getIdCurso(),
        'nomeCurso'           => $Curso->getNomeCurso()  
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Curso não encontrado']);
}
