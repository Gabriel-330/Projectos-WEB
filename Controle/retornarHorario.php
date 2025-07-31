<?php
require_once("../Modelo/DAO/HorarioDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new HorarioDAO();
$Horario = $dao->buscarHorarioPorId($id); // Ajusta o nome do método se for diferente

if ($Horario) {
    echo json_encode([
        'idHorario'           => $Horario->getIdHorario(),
        'idTurma'           => $Horario->getTurmaId(),
        'idDisciplina'   => $Horario->getDisciplinaId(),
        'idCurso'         => $Horario->getIdCurso(),
        'diasSemanaHorario'         => $Horario->getDiaSemana(),
        'horaInicioHorario'       => $Horario->getHoraInicio(),
        'horaFimHorario'        => $Horario->getHoraFim()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Horario não encontrado']);
}
