<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/NotaDAO.php");
require_once("../Modelo/DTO/NotaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['idUtilizador']) || !isset($_SESSION['acesso'])) {
    $_SESSION['success'] = "Acesso negado! Faça login.";
    $_SESSION['icon'] = "error";
    header("Location: ../Visao/index.php");
    exit();
}

$acesso = strtoupper($_SESSION['acesso']);

// Apenas professores têm permissão (por exemplo: padrão 9 dígitos + 2 letras + 3 dígitos)
if (!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $acesso)) {
    $_SESSION['success'] = "Acesso negado! Apenas professores podem lançar notas.";
    $_SESSION['icon'] = "error";
    header("Location: ../Visao/index.php");
    exit();
}

$notaDAO = new NotaDAO();
$notificacaoDAO = new NotificacoesDAO();
$notificacaoDTO = new NotificacoesDTO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // === CADASTRAR NOTA ===
    if (isset($_POST['cadastrarNota'])) {
        $idAluno = intval($_POST['idAluno'] ?? 0);
        $idProfessor = intval($_POST['idProfessor'] ?? 0);
        $idDisciplina = intval($_POST['idDisciplina'] ?? 0);
        $idCurso = intval($_POST['idCurso'] ?? 0);
        $valorNota = floatval($_POST['valorNota'] ?? 0);
        $dataAvaliacao = trim($_POST['dataAvaliacaoNota'] ?? '');
        $tipoAvaliacao = trim($_POST['tipoAvaliacaoNota'] ?? '');
        $tipoNota = trim($_POST['tipoNota'] ?? '');
        $trimestre = trim($_POST['trimestreNota'] ?? '');

        if ($idAluno && $idDisciplina && $valorNota && $dataAvaliacao && $tipoAvaliacao && $tipoNota && $trimestre) {

            if ($notaDAO->existeNota($idAluno, $idDisciplina, $idCurso, $tipoAvaliacao, $tipoNota, $trimestre)) {
                $_SESSION['success'] = "Já existe uma nota do mesmo tipo para este aluno nesta disciplina, curso, avaliação e trimestre.";
                $_SESSION['icon'] = "warning";
                header("Location: ../Visao/notaProfessorBase.php");
                exit();
            }

            $notaDTO = new NotaDTO();
            $notaDTO->setIdAluno($idAluno);
            $notaDTO->setIdProfessor($idProfessor);
            $notaDTO->setIdDisciplina($idDisciplina);
            $notaDTO->setIdCurso($idCurso);
            $notaDTO->setValorNota($valorNota);
            $notaDTO->setDataValorNota($dataAvaliacao);
            $notaDTO->setTipoAvaliacaoNota($tipoAvaliacao);
            $notaDTO->setTipoNota($tipoNota);
            $notaDTO->setTrimestreNota($trimestre);

            if ($notaDAO->cadastrar($notaDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Cadastro de Nota");
                $notificacaoDTO->setMensagemNotificacoes("Uma nova nota foi lançada.");
                $notificacaoDTO->setLidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);

                $_SESSION['success'] = "Nota lançada com sucesso!";
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = "Erro ao lançar a nota.";
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['icon'] = "warning";
        }

        header("Location: ../Visao/notaProfessorBase.php");
        exit();
    }

    // === ATUALIZAR NOTA ===
    if (isset($_POST['actualizarNota'])) {
        $idNota = intval($_POST['idNota'] ?? 0);
        $idProfessor = intval($_POST['idProfessor'] ?? 0);
        $idAluno = intval($_POST['idAluno'] ?? 0);
        $idDisciplina = intval($_POST['idDisciplina'] ?? 0);
        $idCurso = intval($_POST['idCurso'] ?? 0);
        $valorNota = floatval($_POST['valorNota'] ?? 0);
        $dataAvaliacao = trim($_POST['dataAvaliacaoNota'] ?? '');
        $tipoAvaliacao = trim($_POST['tipoAvaliacaoNota'] ?? '');
        $tipoNota = trim($_POST['tipoNota'] ?? '');
        $trimestre = trim($_POST['trimestreNota'] ?? '');

        if ($idNota && $idAluno && $idDisciplina && $dataAvaliacao && $tipoAvaliacao) {
            $notaDTO = new NotaDTO();
            $notaDTO->setIdNota($idNota);
            $notaDTO->setIdAluno($idAluno);
            $notaDTO->setIdProfessor($idProfessor);
            $notaDTO->setIdDisciplina($idDisciplina);
            $notaDTO->setIdCurso($idCurso);
            $notaDTO->setValorNota($valorNota);
            $notaDTO->setDataValorNota($dataAvaliacao);
            $notaDTO->setTipoAvaliacaoNota($tipoAvaliacao);
            $notaDTO->setTipoNota($tipoNota);
            $notaDTO->setTrimestreNota($trimestre);

            if ($notaDAO->actualizar($notaDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Atualização de Nota");
                $notificacaoDTO->setMensagemNotificacoes("Uma nota foi atualizada.");
                $notificacaoDTO->setLidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);

                $_SESSION['success'] = "Nota atualizada com sucesso!";
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = "Erro ao atualizar a nota.";
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = "Preencha todos os campos obrigatórios.";
            $_SESSION['icon'] = "warning";
        }

        header("Location: ../Visao/indexProfessor.php");
        exit();
    }

    // === APAGAR NOTA ===
    if (isset($_POST['apagarNota'])) {
        $idNota = intval($_POST['idNota'] ?? 0);

        if ($idNota > 0) {
            if ($notaDAO->apagar($idNota)) {
                $notificacaoDTO->setTipoNotificacoes("Eliminação de Nota");
                $notificacaoDTO->setMensagemNotificacoes("Uma nota foi eliminada.");
                $notificacaoDTO->setLidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);

                $_SESSION['success'] = "Nota eliminada com sucesso!";
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['success'] = "Erro ao eliminar a nota.";
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['success'] = "ID de nota inválido.";
            $_SESSION['icon'] = "warning";
        }

        header("Location: ../Visao/notaProfessorBase.php");
        exit();
    }
}
