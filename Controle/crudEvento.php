<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/EventosDAO.php");
require_once("../Modelo/DTO/EventosDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

// Instanciar DAOs e DTOs de Notificação
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR EVENTO ===
    if (isset($_POST["cadastrarEvento"])) {
        $idUtilizador = $_SESSION['idUtilizador'] ?? null;

        // Obter e limpar os dados do formulário
        $titulo = trim($_POST['tituloEvento'] ?? '');
        $tipo = trim($_POST['tipoEvento'] ?? '');
        $local = trim($_POST['localEvento'] ?? '');
        $curso = trim($_POST['cursoEvento'] ?? '');
        $responsavel = trim($_POST['responsavelEvento'] ?? '');
        $data = trim($_POST['dataEvento'] ?? '');
        $horaInicio = trim($_POST['horaInicioEvento'] ?? '');
        $horaFim = trim($_POST['horaFimEvento'] ?? '');

        // Validar dados essenciais
        if (empty($titulo) || empty($tipo) || empty($data) || empty($horaInicio) || empty($horaFim)) {
            $_SESSION['error'] = 'Por favor, preencha todos os campos obrigatórios.';
            $_SESSION['icon'] = 'error';
            header('location: ../Visao/criarEvento.php');
            exit();
        }

        // Criar DTO e preencher
        $eventoDTO = new EventosDTO();
        $eventoDTO->setIdUtilizador($idUtilizador);
        $eventoDTO->setTituloEvento($titulo);
        $eventoDTO->setTipoEvento($tipo);
        $eventoDTO->setLocalEvento($local);
        $eventoDTO->setIdCurso($curso);
        $eventoDTO->setResponsavelEvento($responsavel);
        $eventoDTO->setDataEvento($data);
        $eventoDTO->setHoraInicioEvento($horaInicio);
        $eventoDTO->setHoraFimEvento($horaFim);

        $eventoDAO = new EventosDAO();

        // Tentar cadastrar
        if ($eventoDAO->cadastrar($eventoDTO)) {
            // Criar notificação
            $notificacaoDTO->setTipoNotificacoes("Cadastro de evento");
            $notificacaoDTO->setMensagemNotificacoes("Aproxima-se um novo evento: " . $titulo);
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($idUtilizador);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Evento cadastrado com sucesso!';
            $_SESSION['icon'] = 'success';
            header('location: ../Visao/eventoBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar evento.';
            $_SESSION['icon'] = 'error';
            header('location: ../Visao/criarEvento.php');
            exit();
        }
    }

    // === ATUALIZAR EVENTO ===
    if (isset($_POST["atualizarEvento"])) {
        $id = $_POST['id'] ?? null;
        $titulo = trim($_POST['titulo'] ?? '');
        $dataEvento = trim($_POST['dataEvento'] ?? '');

        if (empty($id) || empty($titulo) || empty($dataEvento)) {
            $_SESSION['error'] = 'Dados inválidos para atualização.';
            $_SESSION['icon'] = 'error';
            header('location: ../Visao/eventoBase.php');
            exit();
        }

        $eventoDTO = new EventosDTO();
        $eventoDTO->setId($id);
        $eventoDTO->setTituloEvento($titulo);
        $eventoDTO->setDataEvento($dataEvento);

        $eventoDAO = new EventosDAO();

        if ($eventoDAO->actualizar($eventoDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Atualização de evento");
            $notificacaoDTO->setMensagemNotificacoes("Evento atualizado: " . $titulo);
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Evento atualizado com sucesso!';
            $_SESSION['icon'] = 'success';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar evento.';
            $_SESSION['icon'] = 'error';
        }

        header('location: ../Visao/eventoBase.php');
        exit();
    }

    // === DELETAR EVENTO ===
    if (isset($_POST["deletarEvento"])) {
        $id = $_POST['id'] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = 'ID inválido para exclusão.';
            $_SESSION['icon'] = 'error';
            header('location: ../Visao/eventoBase.php');
            exit();
        }

        $eventoDAO = new EventosDAO();

        if ($eventoDAO->apagar($id)) {
            $notificacaoDTO->setTipoNotificacoes("Eliminação de evento");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de um evento foram eliminados!");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Evento deletado com sucesso!';
            $_SESSION['icon'] = 'success';
        } else {
            $_SESSION['error'] = 'Erro ao deletar evento.';
            $_SESSION['icon'] = 'error';
        }

        header('location: ../Visao/eventoBase.php');
        exit();
    }
}
