<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/NotaDAO.php");
require_once("../Modelo/DTO/NotaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

// Verifica se o utilizador está autenticado e é professor
if (!isset($_SESSION['idUtilizador']) || !isset($_SESSION['acesso'])) {
    $_SESSION['success'] = "Acesso negado! Faça login.";
    $_SESSION['icon'] = "error";
    header("Location: ../Visao/index.php");
    exit();
}

$acesso = strtoupper($_SESSION['acesso']);

// Verifica se o acesso é email de admin válido (ex: termina com @admin.estrela.com)
if (!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $acesso)) {    // Se não for admin, redireciona para página de acesso negado ou login
    $_SESSION['success'] = "Acesso negado! Apenas professores podem lançar notas.";
    $_SESSION['icon'] = "error";
    header("Location: ../Visao/index.php");
    exit();
}

$notaDAO = new NotaDAO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CADASTRAR NOTA
    if (isset($_POST['cadastrarNota'])) {
        $idAluno = intval($_POST['idAluno'] ?? 0);
        $idDisciplina = intval($_POST['idDisciplina'] ?? 0);
        $valorNota = floatval($_POST['valorNota'] ?? 0);
        $idcurso = intval($_POST['idCurso'] ?? 0);
        $dataAvaliacaoNota = $_POST['dataAvaliacaoNota'] ?? null;
        $tipoAvaliacaoNota = $_POST['tipoAvaliacaoNota'] ?? null;
        $tipoNota = $_POST['tipoNota'] ?? null;
        $trimestreNota = $_POST['trimestreNota'] ?? null;
        

        $comentariosNota = ($valorNota < 10) ? "Mal" : (($valorNota == 10) ? "Normal" : "Bom");

        if ($idAluno && $idDisciplina && $valorNota && $dataAvaliacaoNota && $tipoAvaliacaoNota) {
            $notaDTO = new NotaDTO();
            $notaDTO->setIdAluno($idAluno);
            $notaDTO->setIdDisciplina($idDisciplina);
            $notaDTO->setIdCurso($idcurso);
            $notaDTO->setValorNota($valorNota);
            $notaDTO->setDataValorNota($dataAvaliacaoNota);
            $notaDTO->setTipoAvaliacaoNota($tipoAvaliacaoNota);
            $notaDTO->setTipoNota($tipoNota);
            $notaDTO->setTrimestreNota($trimestreNota);
           

            if ($notaDAO->cadastrar($notaDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Cadastro de nota");
                $notificacaoDTO->setMensagemNotificacoes("Uma nova nota foi lançada!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = "Nota lançada com sucesso!";
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = "Erro ao lançar nota.";
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['icon'] = "warning";
        }

        header("Location: ../Visao/notaProfessorBase.php");
        exit();
    }

    // ATUALIZAR NOTA
    if (isset($_POST['actualizar'])) {
        $idNota = intval($_POST['idNota'] ?? 0);
        $idAluno = intval($_POST['idAluno'] ?? 0);
        $idDisciplina = intval($_POST['idDisciplina'] ?? 0);
        $idcurso = intval($_POST['idCurso'] ?? 0);
        $valorNota = floatval($_POST['valorNota'] ?? 0);
        $dataAvaliacaoNota = $_POST['dataAvaliacaoNota'] ?? null;
        $tipoAvaliacaoNota = $_POST['tipoAvaliacaoNota'] ?? null;
        $tipoNota = $_POST['tipoNota'] ?? null;
        $trimestreNota = $_POST['trimestreNota'] ?? null;

        $comentariosNota = ($valorNota < 10) ? "Mal" : (($valorNota == 10) ? "Normal" : "Bom");

        if ($idNota && $idAluno && $idDisciplina && $dataAvaliacaoNota && $tipoAvaliacaoNota) {
            $notaDTO = new NotaDTO();
            $notaDTO->setIdNota($idNota);
            $notaDTO->setIdAluno($idAluno);
            $notaDTO->setIdDisciplina($idDisciplina);
            $notaDTO->setIdCurso($idcurso); 
            $notaDTO->setValorNota($valorNota);
            $notaDTO->setDataValorNota($dataAvaliacaoNota);
            $notaDTO->setTipoAvaliacaoNota($tipoAvaliacaoNota);
            $notaDTO->setTrimestreNota($trimestreNota);
            $notaDTO->setTipoNota($tipoNota);
            

            if ($notaDAO->actualizar($notaDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Actualizacao de nota");
                $notificacaoDTO->setMensagemNotificacoes("Houve uma actualização de notas!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Nota atualizada com sucesso!';
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = 'Erro ao atualizar nota.';
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = 'Preencha todos os campos obrigatórios.';
            $_SESSION['icon'] = "warning";
        }

        header('Location: ../Visao/indexProfessor.php');
        exit();
    }

    // APAGAR NOTA
    if (isset($_POST['apagarNota'])) {
        $id = intval($_POST['idNota'] ?? 0);

            if ($id!=null) {
            if ($notaDAO->apagar($id)) {
                $notificacaoDTO->setTipoNotificacoes("Delete de nota");
                $notificacaoDTO->setMensagemNotificacoes("Os dados de uma nota foram Eliminados!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Nota eliminada com sucesso!';
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = 'Erro ao eliminar nota.';
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = 'ID da nota inválido.';
            $_SESSION['icon'] = "warning";
        }

        header('Location: ../Visao/notaProfessorbase.php');
        exit();
    }


    // LISTAR TODAS
if (isset($_GET['listar'])) {
    $notas = $notaDAO->listarTodos();
    $_SESSION['notas'] = $notas;
    header("Location: ../Visao/notaLista.php");
    exit();
}

// LISTAR POR ALUNO
if (isset($_GET['listarPorAluno']) && !empty($_GET['idAluno'])) {
    $notas = $notaDAO->listarPorAluno($_GET['idAluno']);
    $_SESSION['notasAluno'] = $notas;
    header("Location: ../Visao/notaAluno.php");
    exit();
}

// PESQUISAR
if (isset($_GET['pesquisar'])) {
    $palavra = $_GET['pesquisar'];
    $resultados = $notaDAO->pesquisar($palavra);
    $_SESSION['pesquisaNota'] = $resultados;
    header("Location: ../Visao/notaProfessorBase.php");
    exit();
}

// BUSCAR POR ID
if (isset($_GET['buscar']) && !empty($_GET['idNota'])) {
    $nota = $notaDAO->buscarPorId($_GET['idNota']);
    $_SESSION['notaSelecionada'] = $nota;
    header("Location: ../Visao/notaProfessorBase.php");
    exit();
}

}

// Se chegou aqui sem um POST válido
$_SESSION['error'] = 'Acesso inválido.';
$_SESSION['icon'] = "error";
header("Location: ../Visao/indexProfessor.php");
exit();
