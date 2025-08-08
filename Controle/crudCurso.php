<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/CursoDAO.php");
require_once("../Modelo/DTO/CursoDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$conn = (new Conn())->getConexao();
$cursoDAO = new CursoDAO();
$cursoDTO = new CursoDTO();
$notificacaoDAO = new NotificacoesDAO();
$notificacaoDTO = new NotificacoesDTO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR CURSO ===
    if (isset($_POST["criarCurso"])) {
        try {
            $nomeCurso = trim(filter_input(INPUT_POST, 'nomeCurso', FILTER_SANITIZE_STRING));

            if (empty($nomeCurso)) {
                $_SESSION['success'] = "O nome do curso não pode estar vazio.";
                $_SESSION['icon'] = 'warning';
                header('Location: ../Visao/cursoBase.php');
                exit();
            }

            // Verifica duplicidade
            if ($cursoDAO->existeNome($nomeCurso)) {
                $_SESSION['success'] = "Já existe um curso com o nome '$nomeCurso'.";
                $_SESSION['icon'] = 'warning';
                header('Location: ../Visao/cursoBase.php');
                exit();
            }

            $conn->beginTransaction();

            $cursoDTO->setNomeCurso($nomeCurso);

            if (!$cursoDAO->cadastrarCurso($cursoDTO)) {
                throw new Exception("Erro ao cadastrar curso.");
            }

            $notificacaoDTO->setTipoNotificacoes("Cadastro de curso");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo curso: $nomeCurso");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Curso cadastrado com sucesso!";
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/cursoBase.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/cursoBase.php');
            exit();
        }
    }

    // === ATUALIZAR CURSO ===
    elseif (isset($_POST["actualizarCurso"])) {
        try {
            $idCurso = filter_input(INPUT_POST, 'idCurso', FILTER_VALIDATE_INT);
            $nomeCurso = trim(filter_input(INPUT_POST, 'nomeCurso', FILTER_SANITIZE_STRING));

            if ($idCurso <= 0 || empty($nomeCurso)) {
                $_SESSION['success'] = "ID ou nome do curso inválido.";
                $_SESSION['icon'] = 'warning';
                header('Location: ../Visao/cursoBase.php');
                exit();
            }

            // Verifica duplicidade
            if ($cursoDAO->existeNomeOutro($nomeCurso, $idCurso)) {
                $_SESSION['success'] = "Já existe um curso com o nome '$nomeCurso'.";
                $_SESSION['icon'] = 'warning';
                header('Location: ../Visao/cursoBase.php');
                exit();
            }

            $conn->beginTransaction();

            $cursoDTO->setIdCurso($idCurso);
            $cursoDTO->setNomeCurso($nomeCurso);

            if (!$cursoDAO->actualizarCurso($cursoDTO)) {
                throw new Exception("Erro ao atualizar curso.");
            }

            $notificacaoDTO->setTipoNotificacoes("Atualização de curso");
            $notificacaoDTO->setMensagemNotificacoes("Curso atualizado: $nomeCurso");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Curso atualizado com sucesso!";
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/cursoBase.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/cursoBase.php');
            exit();
        }
    }

    // === APAGAR CURSO ===
    elseif (isset($_POST["apagarCurso"])) {
        try {
            $idCurso = filter_input(INPUT_POST, 'idCurso', FILTER_VALIDATE_INT);

            if ($idCurso <= 0) {
                $_SESSION['success'] = "Curso inválido para apagar.";
                $_SESSION['icon'] = 'warning';
                header('Location: ../Visao/cursoBase.php');
                exit();
            }

            $conn->beginTransaction();

            $cursoDTO->setIdCurso($idCurso);

            if (!$cursoDAO->apagar($cursoDTO)) {
                throw new Exception("Erro ao eliminar curso. Existem dados associados!");
            }

            $notificacaoDTO->setTipoNotificacoes("Eliminação de curso");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de um curso foram eliminados!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Curso eliminado com sucesso!";
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/cursoBase.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/cursoBase.php');
            exit();
        }
    }
}
