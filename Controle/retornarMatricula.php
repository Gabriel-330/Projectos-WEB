<?php
require_once '../Modelo/DAO/MatriculaDAO.php';
require_once '../Modelo/DTO/MatriculaDTO.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $matriculaDAO = new MatriculaDAO();
    $matricula = $matriculaDAO->buscarMatriculaPorId($id); // Certifique-se que este método existe no DAO

    if ($matricula instanceof MatriculaDTO) {
        $dados = [
            'idMatricula' => $matricula->getIdMatricula(),
            'idAluno' => $matricula->getIdAluno(),
            'idTurma' => $matricula->getIdTurma(),
            'idCurso' => $matricula->getIdCurso(),
            'dataMatricula' => $matricula->getDataMatricula(),
            'estadoMatricula' => $matricula->getEstadoMatricula(),
            'nomeAluno' => $matricula->getNomeAluno(),
            'nomeTurma' => $matricula->getNomeTurma(),
            'nomeCurso' => $matricula->getNomeCurso(),
            'classeMatricula' => $matricula->getClasseMatricula(),
            'periodoMatricula' => $matricula->getPeriodoMatricula()
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados);
    } else {
        echo json_encode(['erro' => 'Matrícula não encontrada']);
    }
} else {
    echo json_encode(['erro' => 'ID inválido']);
}
