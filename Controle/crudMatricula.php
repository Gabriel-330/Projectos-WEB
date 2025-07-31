<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/MatriculaDAO.php");
require_once("../Modelo/DTO/MatriculaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CREATE
    if (isset($_POST["cadastrarMatricula"])) {
    $idTurma = trim($_POST["turmaMatricula"]);
    $idCurso = trim($_POST["cursoMatricula"]);
    $idAluno = trim($_POST["alunoMatricula"]);
    $estadoMatricula = trim($_POST["estadoMatricula"]);
    $periodoMatricula = trim($_POST["periodoMatricula"]);
    $classeMatricula = trim($_POST["classeMatricula"]);
    $dataMatricula = trim($_POST["dataMatricula"]);

    $dto = new MatriculaDTO();
    $dao = new MatriculaDAO();

    $dto->setIdTurma($idTurma);
    $dto->setIdCurso($idCurso);
    $dto->setIdAluno($idAluno);
    $dto->setDataMatricula($dataMatricula);
    $dto->setEstadoMatricula($estadoMatricula);
    $dto->setClasseMatricula($classeMatricula);
    $dto->setPeriodoMatricula($periodoMatricula);

    if ($dao->criarMatricula($dto)) {
        $notificacaoDTO->setTipoNotificacoes("Cadastro de matricula");
        $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado uma nova matricula");
        $notificacaoDTO->setlidaNotificacoes(0);
        $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
        $notificacaoDAO->criarNotificacao($notificacaoDTO);
        $_SESSION['success'] = "Matrícula cadastrada com sucesso!";
        $_SESSION['icon'] = "success";
        header("Location: ../Visao/matriculaBase.php");
        exit();
    } else {
        $_SESSION['success'] = "Erro ao cadastrar matrícula.";
        $_SESSION['icon'] = "error";
        header("Location: ../Visao/criarMatricula.php");
        exit();
    }
    }

    // UPDATE
    if (isset($_POST["actualizarMatricula"])) {
        $idMatricula = $_POST['idMatricula'];
        $idTurma=$_POST['turmaMatricula'];
        $idAluno = $_POST['alunoMatricula'];
        $idCurso = $_POST['cursoMatricula'];
        $dataMatricula = $_POST['dataMatricula'];
        $estado = $_POST['estadoMatricula'];

        $matriculaDAO = new MatriculaDAO();
        $matriculaDTO = new MatriculaDTO();

        $matriculaDTO->setIdMatricula($idMatricula);
        $matriculaDTO->setIdAluno($idAluno);
        $matriculaDTO->setIdCurso($idCurso);
        $matriculaDTO->setDataMatricula($dataMatricula);
        $matriculaDTO->setEstadoMatricula($estado);
        $matriculaDTO->setIdTurma($idTurma);
        

        if ($matriculaDAO->actualizarMatricula($matriculaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de matricula");
            $notificacaoDTO->setMensagemNotificacoes("Houve uma actualização de maricula!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Matricula atualizada com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/matriculaBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao atualizar matricula';
            $_SESSION['icon'] = "error";
        }
    }

    // DELETE
    if (isset($_POST["apagarMatricula"])) {
        $id = $_POST['idMatricula'];
        $matriculaDAO = new MatriculaDAO();

        if ($matriculaDAO->apagarMatricula($id)) {
            $notificacaoDTO->setTipoNotificacoes("Delete de matricula");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de uma matricula foram Eliminados!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Matricula apagada com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['error'] = 'Erro ao deletar matricula';
            $_SESSION['icon'] = "error";
        }
        header('location: ../Visao/matriculaBase.php');
        exit();
    }
}
?>
