<?php
require_once("../Modelo/DAO/ProfessorDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new ProfessorDAO();
$Professor = $dao->buscarPorId($id); // Ajusta o nome do método se for diferente

if ($Professor) {
    echo json_encode([
        'idProfessor'           => $Professor->getIdProfessor(),
        'nomeProfessor'           => $Professor->getNomeProfessor(),
        'generoProfessor'   => $Professor->getGeneroProfessor(),
        'emailProfessor'         => $Professor->getEmailProfessor(),
        'dataNascProfessor'         => $Professor->getDataDeNascimentoProfessor(),
        'dataContProfessor'       => $Professor->getDataContProfessor(),
        'tipoContProfessor'        => $Professor->getTipoContratoProfessor(),
        'moradaProfessor'   => $Professor->getMoradaProfessor(),
        'nIdentificacaoProfessor'        => $Professor->getnIdentificacao()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Professor não encontrado']);
}
