<?php
require_once("../Modelo/DAO/EventosDAO.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não especificado']);
    exit;
}

$id = intval($_GET['id']);
$dao = new EventosDAO();
$evento = $dao->buscarPorId($id);

if ($evento) {
    echo json_encode([
        'idEvento'           => $evento->getIdEvento(),
        'tituloEvento'       => $evento->getTituloEvento(),
        'dataEvento'         => $evento->getDataEvento(),
        'horaInicioEvento'   => $evento->getHoraInicioEvento(),
        'horaFimEvento'      => $evento->getHoraFimEvento(),
        'localEvento'        => $evento->getLocalEvento(),
        'responsavelEvento'  => $evento->getResponsavelEvento(),
        'tipoEvento'         => $evento->getTipoEvento(),
        'idCurso'            => $evento->getIdCurso(),
        'idUtilizador'       => $evento->getIdUtilizador()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Evento não encontrado']);
}
