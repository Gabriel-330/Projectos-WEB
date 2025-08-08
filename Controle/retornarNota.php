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
$nota = $dao->buscarPorId($id);

if ($nota) {
    echo json_encode([
        'idNota'               => $nota->getIdNota(),
        'idAluno'              => $nota->getIdAluno(),
        'idDisciplina'         => $nota->getIdDisciplina(),
        'idCurso'              => $nota->getIdCurso(),
        'idProfessor'          => $nota->getIdProfessor(),
        'valorNota'            => $nota->getValorNota(),
        'dataValorNota'        => $nota->getDataValorNota(),
        'tipoAvaliacaoNota'    => $nota->getTipoAvaliacaoNota(),
        'trimestreNota'        => $nota->getTrimestreNota(),
        'tipoNota'             => $nota->getTipoNota(),
        'nomeDisciplina'       => $nota->getNomeDisciplina(),
        'nomeCurso'            => $nota->getNomeCurso(),
        'nomeAluno'            => $nota->getNomeAluno(),
        'dataNascimentoAluno'  => $nota->getDataNascimentoAluno(),
        'responsavelAluno'     => $nota->getResponsavelAluno()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Nota não encontrada']);
}
