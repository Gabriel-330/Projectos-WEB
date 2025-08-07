<?php
require_once("../Modelo/DAO/AlunoDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new AlunoDAO();
$aluno = $dao->buscarPorId($id); // Ajusta se usares outro nome

if ($aluno) {
    echo json_encode([
        'idAluno'                  => $aluno->getIdAluno(),
        'nomeAluno'               => $aluno->getNomeAluno(),
        'nIdentificacao'          => $aluno->getnIdentificacao(),
        'moradaAluno'             => $aluno->getMoradaAluno(),
        'dataNascimentoAluno'     => $aluno->getDataNascimentoAluno(),
        'generoAluno'             => $aluno->getGeneroAluno(),
        'fotoAluno'               => $aluno->getFotoAluno(),
        'responsavelAluno'        => $aluno->getResponsavelAluno(),
        'contactoResponsavelAluno'=> $aluno->getContactoResponsavelAluno(),
        'anoIngressoAluno'        => $aluno->getAnoIngressoAluno(),
        'idUtilizador'            => $aluno->getIdUtilizador(),
        'idCurso'                 => $aluno->getIdCurso(),
        'idTurma'                 => $aluno->getIdTurma(),
        'nomeCurso'               => $aluno->getNomeCurso(),
        'nomeUtilizador'          => $aluno->getNomeUtilizador(),
        'nomeTurma'               => $aluno->getNomeTurma()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Aluno não encontrado']);
}
