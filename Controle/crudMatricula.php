<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/MatriculaDAO.php");
require_once("../Modelo/DTO/MatriculaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR MATRÍCULA ===
    if (isset($_POST["cadastrarMatricula"])) {
        $idTurma          = trim($_POST["turmaMatricula"] ?? '');
        $idCurso          = trim($_POST["cursoMatricula"] ?? '');
        $idAluno          = trim($_POST["alunoMatricula"] ?? '');
        $estadoMatricula  = trim($_POST["estadoMatricula"] ?? '');
        $periodoMatricula = trim($_POST["periodoMatricula"] ?? '');
        $classeMatricula  = trim($_POST["classeMatricula"] ?? '');
        $dataMatricula    = trim($_POST["dataMatricula"] ?? '');

        // Validação mínima
        if (empty($idTurma) || empty($idCurso) || empty($idAluno) || empty($estadoMatricula) || empty($periodoMatricula) || empty($classeMatricula) || empty($dataMatricula)) {
            $_SESSION['error'] = "Todos os campos devem ser preenchidos.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/criarMatricula.php");
            exit();
        }

        $matriculaDTO = new MatriculaDTO();
        $matriculaDTO->setIdTurma($idTurma);
        $matriculaDTO->setIdCurso($idCurso);
        $matriculaDTO->setIdAluno($idAluno);
        $matriculaDTO->setDataMatricula($dataMatricula);
        $matriculaDTO->setEstadoMatricula($estadoMatricula);
        $matriculaDTO->setClasseMatricula($classeMatricula);
        $matriculaDTO->setPeriodoMatricula($periodoMatricula);

        $matriculaDAO = new MatriculaDAO();

        if ($matriculaDAO->criarMatricula($matriculaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de matrícula");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrada uma nova matrícula.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Matrícula cadastrada com sucesso!";
            $_SESSION['icon'] = "success";
            header("Location: ../Visao/matriculaBase.php");
            exit();
        } else {
            $_SESSION['error'] = "Erro ao cadastrar matrícula.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/criarMatricula.php");
            exit();
        }
    }

    // === ATUALIZAR MATRÍCULA ===
    if (isset($_POST["actualizarMatricula"])) {
        $idMatricula    = $_POST['idMatricula'] ?? '';
        $idTurma        = $_POST['turmaMatricula'] ?? '';
        $idAluno        = $_POST['alunoMatricula'] ?? '';
        $idCurso        = $_POST['cursoMatricula'] ?? '';
        $dataMatricula  = $_POST['dataMatricula'] ?? '';
        $periodoMatricula = trim($_POST["periodoMatricula"] ?? '');
        $classeMatricula  = trim($_POST["classeMatricula"] ?? '');
        $estado         = $_POST['estadoMatricula'] ?? '';

        if (empty($idMatricula) || empty($idTurma) || empty($idAluno) || empty($idCurso) || empty($dataMatricula) || empty($estado)) {
            $_SESSION['error'] = "Dados incompletos para atualizar matrícula.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/matriculaBase.php");
            exit();
        }

        $matriculaDTO = new MatriculaDTO();
        $matriculaDTO->setIdMatricula($idMatricula);
        $matriculaDTO->setIdAluno($idAluno);
        $matriculaDTO->setIdCurso($idCurso);
        $matriculaDTO->setDataMatricula($dataMatricula);
        $matriculaDTO->setEstadoMatricula($estado);
        $matriculaDTO->setPeriodoMatricula($periodoMatricula);
        $matriculaDTO->setClasseMatricula($classeMatricula);
        $matriculaDTO->setIdTurma($idTurma);

        $matriculaDAO = new MatriculaDAO();

        if ($matriculaDAO->actualizarMatricula($matriculaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Atualização de matrícula");
            $notificacaoDTO->setMensagemNotificacoes("Uma matrícula foi atualizada.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Matrícula atualizada com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['error'] = 'Erro ao atualizar matrícula.';
            $_SESSION['icon'] = "error";
        }

        header("Location: ../Visao/matriculaBase.php");
        exit();
    }

    // === APAGAR MATRÍCULA ===
    if (isset($_POST["apagarMatricula"])) {
        $id = $_POST['idMatricula'] ?? '';

        if (empty($id)) {
            $_SESSION['error'] = "ID inválido para exclusão.";
            $_SESSION['icon'] = "error";
            header("Location: ../Visao/matriculaBase.php");
            exit();
        }

        $matriculaDAO = new MatriculaDAO();

        if ($matriculaDAO->apagarMatricula($id)) {
            $notificacaoDTO->setTipoNotificacoes("Exclusão de matrícula");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de uma matrícula foram eliminados.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = 'Matrícula apagada com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['error'] = 'Erro ao apagar matrícula.';
            $_SESSION['icon'] = "error";
        }

        header("Location: ../Visao/matriculaBase.php");
        exit();
    }
}
