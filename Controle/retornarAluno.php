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
$Aluno = $dao->buscarPorId($id); // Ajusta o nome do método se for diferente

if ($Aluno) {
    echo json_encode([
        'idAluno'           => $Aluno->getIdAluno(),
        'nomeAluno'           => $Aluno->getNomeAluno(),
        'generoAluno'   => $Aluno->getGeneroAluno(),
        'dataNascimentoAluno'         => $Aluno->getDataNascimentoAluno(),
        'moradaAluno'       => $Aluno->getMoradaAluno(),
        'responsavelAluno'   => $Aluno->getResponsavelAluno(),
        'contactoResponsavelAluno'        => $Aluno->getContactoResponsavelAluno(),
        'nIdentificacao' =>$Aluno->getnIdentificacao(),
        'idCurso'        => $Aluno->getIdCurso(),
        'idTurma'        => $Aluno->getIdCurso()

    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Aluno não encontrado']);
}
