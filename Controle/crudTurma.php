<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/TurmaDAO.php");
require_once("../Modelo/DTO/TurmaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$turmaDAO = new TurmaDAO();
$turmaDTO = new TurmaDTO();
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR TURMA ===
    if (isset($_POST["cadastrarTurma"])) {
        $nomeTurma = trim($_POST['nomeTurma']);
        $cursoTurma = trim($_POST['cursoTurma']);
        $directorTurma = trim($_POST['directorTurma']);
        $nSalaTurma = trim($_POST['nSalaTurma']);

        $turmaDTO->setNomeTurma($nomeTurma);
        $turmaDTO->setIdCurso($cursoTurma);
        $turmaDTO->setIdProfessor($directorTurma);
        $turmaDTO->setSalaTurma($nSalaTurma);

        // Verificar se já existe turma com mesmo nome e curso
        if ($turmaDAO->existeTurma($nomeTurma, $cursoTurma)) {
            $_SESSION['success'] = 'Já existe uma turma com este nome para o curso selecionado.';
            $_SESSION['icon'] = "error";
            header('Location: ../Visao/turmaBase.php');
            exit();
        }

        if ($turmaDAO->cadastrar($turmaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de Turma");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrada uma nova turma: " . $nomeTurma);
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Turma cadastrada com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['success'] = 'Erro ao cadastrar turma.';
            $_SESSION['icon'] = "error";
        }
        header('Location: ../Visao/turmaBase.php');
        exit();
    }

    // === ATUALIZAR TURMA ===
    if (isset($_POST['actualizar'])) {
        $idTurma = $_POST['idTurma'];
        $nomeTurma = trim($_POST['nomeTurma']);
        $cursoTurma = trim($_POST['cursoTurma']);
        $directorTurma = trim($_POST['directorTurma']);
        $nSalaTurma = trim($_POST['nSalaTurma']);

        $turmaDTO->setIdTurma($idTurma);
        $turmaDTO->setNomeTurma($nomeTurma);
        $turmaDTO->setIdCurso($cursoTurma);
        $turmaDTO->setIdProfessor($directorTurma);
        $turmaDTO->setSalaTurma($nSalaTurma);

        if ($turmaDAO->actualizar($turmaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Atualização de Turma");
            $notificacaoDTO->setMensagemNotificacoes("Turma atualizada: " . $nomeTurma);
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Turma atualizada com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['success'] = 'Erro ao atualizar turma.';
            $_SESSION['icon'] = "error";
        }
        header('Location: ../Visao/turmaBase.php');
        exit();
    }

    // === APAGAR TURMA ===
    if (isset($_POST['apagar'])) {
        $idTurma = $_POST['idTurma'] ?? null;
        if (!empty($idTurma)) {
            $turmaDTO->setIdTurma($idTurma);

            if ($turmaDAO->apagar($turmaDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Eliminação de Turma");
                $notificacaoDTO->setMensagemNotificacoes("Dados da turma foram eliminados.");
                $notificacaoDTO->setLidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);

                $_SESSION['success'] = 'Turma apagada com sucesso!';
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = 'Erro ao apagar turma.';
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = 'Selecione a turma a ser apagada.';
            $_SESSION['icon'] = "warning";
        }
        header('Location: ../Visao/turmaBase.php');
        exit();
    }
}

