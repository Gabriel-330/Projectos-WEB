<?php
header("Content-Type: application/json");
require_once("../Modelo/DAO/AlunoDAO.php");

try {
    // Receber o corpo JSON e converter para array
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar se os dados vieram corretamente
    if (!isset($input['classe'], $input['curso'], $input['periodo'], $input['turma'])) {
        throw new Exception("Dados incompletos recebidos.");
    }

    $classe = $input['classe'];
    $curso = $input['curso'];
    $periodo = $input['periodo'];
    $turma = $input['turma'];

    $daoAluno = new AlunoDAO();
    $alunos = $daoAluno->buscarPorFiltro($classe, $curso, $periodo, $turma);

    // Reformatar alunos para JSON limpo
    $resposta = array_map(function ($aluno) {
        return [
            'id' => $aluno->getIdAluno(),  // ajusta conforme teu DTO
            'nome' => $aluno->getNomeAluno()
        ];
    }, $alunos);

    echo json_encode($resposta);

} catch (Exception $e) {
    // Retornar erro em formato JSON
    echo json_encode([
        "erro" => true,
        "mensagem" => $e->getMessage()
    ]);
}
