<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/EventosDAO.php");
require_once("../Modelo/DTO/EventosDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CREATE
    if (isset($_POST["cadastrarEvento"])) {
        $idUtilizador = $_SESSION['idUtilizador'];
        $titulo = trim($_POST['tituloEvento']);
        $tipo = trim($_POST['tipoEvento']);
        $local = trim($_POST['localEvento']);
        $curso = trim($_POST['cursoEvento']);
        $responsavel = trim($_POST['responsavelEvento']);
        $data = trim($_POST['dataEvento']);
        $horaInicio = trim($_POST['horaInicioEvento']);
        $horaFim = trim($_POST['horaFimEvento']);

        $EventoDTO = new EventosDTO();
        $EventoDTO->setIdUtilizador($idUtilizador);
        $EventoDTO->setTituloEvento($titulo);
        $EventoDTO->setTipoEvento($tipo);
        $EventoDTO->setLocalEvento($local);
        $EventoDTO->setIdCurso($curso);
        $EventoDTO->setResponsavelEvento($responsavel);
        $EventoDTO->setDataEvento($data);
        $EventoDTO->setHoraInicioEvento($horaInicio);
        $EventoDTO->setHoraFimEvento($horaFim);

        $EventoDAO = new EventosDAO();

        if ($EventoDAO->cadastrar($EventoDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de evento");
            $notificacaoDTO->setMensagemNotificacoes("Aproxima-se um novo evento: " . $titulo);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Evento cadastrado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/eventoBase.php');
            exit();
        } else {
            $_SESSION['success'] = 'Erro ao cadastrar evento.';
            $_SESSION['icon'] = "error";
            header('location: ../Visao/criarEvento.php');
            exit();
        }
    }

    // UPDATE
    if (isset($_POST["atualizarEvento"])) {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $dataEvento = $_POST['dataEvento'];
        $eventoDAO = new EventosDAO();
        $eventoDTO = new EventosDTO();
        $eventoDTO->setId($id);
        $eventoDTO->setTituloEvento($_POST['titulo']);
        //$eventoDTO->setDescricao($_POST['descricao']);
        $eventoDTO->setDataEvento($_POST['dataEvento']);

        if ($eventoDAO->actualizar($eventoDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de evento");
            $notificacaoDTO->setMensagemNotificacoes("Evento actualizado: " . $titulo);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Evento atualizado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/eventoBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao atualizar evento';
            $_SESSION['icon'] = "error";
        }
    }

    // DELETE
    if (isset($_POST["deletarEvento"])) {
        $id = $_POST['id'];
        $eventoDAO = new EventosDAO();

        if ($eventoDAO->apagar($id)) {
            $notificacaoDTO->setTipoNotificacoes("Delete de evento");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de um evento foram Eliminados!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Evento deletado com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['error'] = 'Erro ao deletar evento';
            $_SESSION['icon'] = "error";
        }
        header('location: ../Visao/eventoBase.php');
        exit();
    }
}
