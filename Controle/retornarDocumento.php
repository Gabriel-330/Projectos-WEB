<?php
require_once("../Modelo/DAO/DocumentoDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new DocumentoDAO();
$documento = $dao->buscarPorId($id);

if ($documento) {
    echo json_encode([
        'idDocumento'             => $documento->getIdDocumento(),
        'tipoDocumento'           => $documento->getTipoDocumento(),
        'estadoDocumento'         => $documento->getEstadoDocumento(),
        'dataEmissaoDocumento'    => $documento->getDataEmissaoDocumento(),
        'caminhoArquivoDocumento' => $documento->getCaminhoArquivoDocumento(),
        'idAluno'                 => $documento->getIdAluno(),
        'idCurso'                 => $documento->getIdCurso(),
        'idNota'                  => $documento->getIdNota(),
        'idDisciplina'            => $documento->getIdDisciplina(),
        'idTurma'                  => $documento->getIdTurma(),
        'idProfessor'             => $documento->getIdProfessor(),
        'cursoDocumento'          => $documento->getCursoDocumento(),
        'turmaDocumento'          => $documento->getTurmaDocumento(),
        'classeDocumento'         => $documento->getClasseDocumento(),
        'periodoDocumento'        => $documento->getPeriodoDocumento(),
        'disciplinaDocumento'     => $documento->getDisciplinaDocumento()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Documento não encontrado']);
}
