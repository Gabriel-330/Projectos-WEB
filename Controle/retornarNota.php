<?php
require_once("../Modelo/DAO/NotaDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new NotaDAO();
$Nota = $dao->buscarPorId($id); // Ajusta o nome do método se for diferente

if ($Nota) {
    echo json_encode([
        'idNota'           => $Nota->getIdNota(),
        'idDisciplina'   => $Nota->getIdDisciplina(),
        'idAluno'         => $Nota->getIdAluno(),
        'idCurso'         => $Nota->getIdCurso(),
        'valorNota'       => $Nota->getValorNota(),
        'tipoAvaliacacaoNota'  => $Nota->getTipoAvaliacaoNota(),
        'dataAvaliacaoNota'   => $Nota->getDataValorNota()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Nota não encontrado']);
}
