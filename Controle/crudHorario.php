<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/HorarioDAO.php");
require_once("../Modelo/DTO/HorarioDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR HORÁRIO ===
    if (isset($_POST["cadastrarHorario"])) {
        $idTurma      = trim($_POST["turmaHorario"] ?? '');
        $idDisciplina = trim($_POST["disciplinaHorario"] ?? '');
        $idCurso      = trim($_POST["cursoHorario"] ?? '');
        $dia          = trim($_POST["diaHorario"] ?? '');
        $inicio       = trim($_POST["horarioInicio"] ?? '');
        $fim          = trim($_POST["hararioFim"] ?? ''); // nome conforme o form HTML

        // Verificação básica
        if (empty($idTurma) || empty($idDisciplina) || empty($idCurso) || empty($dia) || empty($inicio) || empty($fim)) {
            $_SESSION['error'] = "Todos os campos são obrigatórios.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/criarHorario.php");
            exit();
        }

        $horarioDTO = new HorarioDTO();
        $horarioDTO->setTurmaId($idTurma);
        $horarioDTO->setDisciplinaId($idDisciplina);
        $horarioDTO->setIdCurso($idCurso);
        $horarioDTO->setDiaSemana($dia);
        $horarioDTO->setHoraInicio($inicio);
        $horarioDTO->setHoraFim($fim);

        $horarioDAO = new HorarioDAO();

        if ($horarioDAO->criarHorario($horarioDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de horário");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo horário.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Horário cadastrado com sucesso!";
            $_SESSION['icon'] = "success";
            header("Location: ../Visao/horarioBase.php");
            exit();
        } else {
            $_SESSION['error'] = "Erro ao cadastrar horário.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/criarHorario.php");
            exit();
        }

        // === APAGAR HORÁRIO ===
    } elseif (isset($_POST['apagarHorario'])) {
        $id = $_POST['idHorario'] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "ID de horário inválido.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/horarioBase.php");
            exit();
        }

        $horarioDTO = new HorarioDTO();
        $horarioDTO->setId($id);
        $horarioDAO = new HorarioDAO();

        if ($horarioDAO->apagarHorario($horarioDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Eliminação de horário");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de um horário foram eliminados.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Horário apagado com sucesso!";
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['error'] = "Erro ao apagar horário.";
            $_SESSION['icon'] = "error";
        }

        header("Location: ../Visao/horarioBase.php");
        exit();

        // === ATUALIZAR HORÁRIO ===
    } elseif (isset($_POST["actualizarHorario"])) {
        $id           = trim($_POST['idHorario'] ?? '');
        $idTurma      = trim($_POST['turmaHorario'] ?? '');
        $idDisciplina = trim($_POST["disciplinaHorario"] ?? '');
        $idCurso      = trim($_POST["cursoHorario"] ?? '');
        $dia          = trim($_POST["diaHorario"] ?? '');
        $inicio       = trim($_POST["horarioInicio"] ?? '');
        $fim          = trim($_POST["hararioFim"] ?? '');

        if (empty($id) || empty($idTurma) || empty($idDisciplina) || empty($idCurso) || empty($dia) || empty($inicio) || empty($fim)) {
            $_SESSION['error'] = "Todos os campos devem ser preenchidos.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/horarioBase.php");
            exit();
        }

        $horarioDTO = new HorarioDTO();
        $horarioDTO->setId($id);
        $horarioDTO->setTurmaId($idTurma);
        $horarioDTO->setDisciplinaId($idDisciplina);
        $horarioDTO->setIdCurso($idCurso);
        $horarioDTO->setDiaSemana($dia);
        $horarioDTO->setHoraInicio($inicio);
        $horarioDTO->setHoraFim($fim);

        $horarioDAO = new HorarioDAO();

        if ($horarioDAO->actualizarHorario($horarioDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Atualização de horário");
            $notificacaoDTO->setMensagemNotificacoes("Houve uma atualização de horário.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Horário atualizado com sucesso!";
            $_SESSION['icon'] = "success";
            header("Location: ../Visao/horarioBase.php");
            exit();
        } else {
            $_SESSION['error'] = "Erro ao atualizar horário.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/indexAdmin.php");
            exit();
        }
    }
}
