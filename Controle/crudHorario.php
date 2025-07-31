<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/HorarioDAO.php");
require_once("../Modelo/DTO/HorarioDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
if(isset ($_POST["cadastrarHorario"])){

$idTurma = trim($_POST["turmaHorario"]);
    $idDisciplina = trim($_POST["disciplinaHorario"]);
    $idCurso = trim($_POST["cursoHorario"]);
    $dia = trim($_POST["diaHorario"]);
    $inicio = trim($_POST["horarioInicio"]);
    $fim = trim($_POST["hararioFim"]); // erro no nome do campo "hararioFim", mas deixei igual ao HTML que enviaste

    $HorarioDTO = new HorarioDTO();
    $HorarioDAO = new HorarioDAO();

    $HorarioDTO->setTurmaId($idTurma);
    $HorarioDTO->setDisciplinaId($idDisciplina);
    $HorarioDTO->setIdCurso($idCurso);
    $HorarioDTO->setDiaSemana($dia);
    $HorarioDTO->setHoraInicio($inicio);
    $HorarioDTO->setHoraFim($fim);

    if ($HorarioDAO->criarHorario($HorarioDTO)) {
        $notificacaoDTO->setTipoNotificacoes("Cadastro de horario");
        $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo horário");
        $notificacaoDTO->setlidaNotificacoes(0);
        $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
        $notificacaoDAO->criarNotificacao($notificacaoDTO);
        $_SESSION['success'] = "Horário cadastrado com sucesso!";
        $_SESSION['icon'] = "success";
        header("Location: ../Visao/horarioBase.php");
        exit();
    } else {
        $_SESSION['success'] = "Erro ao cadastrar horário.";
        $_SESSION['icon'] = "error";
        header("Location: ../Visao/criarHorario.php");
        exit();
    }
}elseif (isset($_POST['apagarHorario'])) {
    $id = $_POST['idHorario'];

    $horario = new HorarioDTO();
    $horario->setId($id);

    $dao = new HorarioDAO();
    $resultado = $dao->apagarHorario($horario);

    session_start();
    if ($resultado) {
        $notificacaoDTO->setTipoNotificacoes("Delete de horario");
        $notificacaoDTO->setMensagemNotificacoes("Os dados de um horário foram Eliminados!");
        $notificacaoDTO->setlidaNotificacoes(0);
        $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
        $notificacaoDAO->criarNotificacao($notificacaoDTO); 
        $_SESSION['success'] = "Horário apagado com sucesso!";
        $_SESSION['icon'] = "success";
    } else {
        $_SESSION['success'] = "Erro ao apagar horário.";
        $_SESSION['icon'] = "error";
    }

    header("Location: ../Visao/horarioBase.php");
    exit();
} elseif(isset ($_POST["actualizarHorario"])){
    $id= trim($_POST['idHorario']);
    $idTurma= trim($_POST['turmaHorario']);
    $idDisciplina = trim($_POST["disciplinaHorario"]);
    $idCurso = trim($_POST["cursoHorario"]);
    $dia = trim($_POST["diaHorario"]);
    $inicio = trim($_POST["horarioInicio"]);
    $fim = trim($_POST["hararioFim"]); // erro no nome do campo "hararioFim", mas deixei igual ao HTML que enviaste

    $HorarioDTO = new HorarioDTO();
    $HorarioDAO = new HorarioDAO();
    $HorarioDTO->setId($id);
    $HorarioDTO->setTurmaId($idTurma);
    $HorarioDTO->setDisciplinaId($idDisciplina);
    $HorarioDTO->setIdCurso($idCurso);
    $HorarioDTO->setDiaSemana($dia);
    $HorarioDTO->setHoraInicio($inicio);
    $HorarioDTO->setHoraFim($fim);

    if ($HorarioDAO->actualizarHorario($HorarioDTO)) {
        $notificacaoDTO->setTipoNotificacoes("Actualizacao de horario");
        $notificacaoDTO->setMensagemNotificacoes("Houve uma actualização de horário");
        $notificacaoDTO->setlidaNotificacoes(0);
        $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
        $notificacaoDAO->criarNotificacao($notificacaoDTO);
        $_SESSION['success'] = "Horário actualizado com sucesso!";
        $_SESSION['icon'] = "success";
        header("Location: ../Visao/horarioBase.php");
        exit();
    } else {
        $_SESSION['success'] = "Erro ao actualizar horário.";
        $_SESSION['icon'] = "error";
        header("Location: ../Visao/indexAdmin.php");
}  
}
}