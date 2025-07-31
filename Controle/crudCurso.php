<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/CursoDAO.php");
require_once("../Modelo/DTO/CursoDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

$cursoDAO = new CursoDAO();
$cursoDTO = new CursoDTO();

// Criar curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["criarCurso"])) {
        $nomeCurso = trim($_POST['nomeCurso'] ?? '');

        if (!empty($nomeCurso)) {
            $cursoDTO->setNomeCurso($nomeCurso);

            if ($cursoDAO->cadastrarCurso($cursoDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Cadastro de curso");
                $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo curso: " . $nomeCurso);
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Curso cadastrado com sucesso!';
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar curso.';
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['error'] = 'O nome do curso não pode estar vazio.';
            $_SESSION['icon'] = "warning";
        }

        header('Location: ../Visao/cursoBase.php');
        exit();
    }

    // Apagar curso
    if (isset($_POST['apagarCurso'])) {
        $id = intval($_POST['idCurso'] ?? 0);

        if ($id > 0) {
            $cursoDTO->setIdCurso($id);

            if ($cursoDAO->apagar($cursoDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Delete de curso");
                $notificacaoDTO->setMensagemNotificacoes("Os dados de um curso foram Eliminados!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Curso eliminado com sucesso!';
                $_SESSION['icon'] = "success";
                header('Location: ../Visao/cursoBase.php');
                exit();
            } else {
                $_SESSION['error'] = 'Erro ao eliminar curso.';
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['error'] = 'Curso inválido para apagar.';
            $_SESSION['icon'] = "warning";
            header('Location: ../Visao/cursoBase.php');
            exit();
        }

        header('Location: ../Visao/cursoBase.php');
        exit();
    }

    // Atualizar curso
    if (isset($_POST['actualizarCurso'])) {
        $id = intval($_POST['idCurso'] ?? 0);
        $nomeCurso = trim($_POST['nomeCurso'] ?? '');

        if ($id > 0 && !empty($nomeCurso)) {
            $cursoDTO->setIdCurso($id);
            $cursoDTO->setNomeCurso($nomeCurso);

            if ($cursoDAO->actualizarCurso($cursoDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Actualizacao de curso");
                $notificacaoDTO->setMensagemNotificacoes("Curso actualizado: " . $nomeCurso);
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Curso atualizado com sucesso!';
                $_SESSION['icon'] = "success";
            } else {
                $_SESSION['error'] = 'Erro ao atualizar curso.';
                $_SESSION['icon'] = "error";
            }
        } else {
            $_SESSION['error'] = 'ID ou nome do curso inválido.';
            $_SESSION['icon'] = "warning";
        }

        header('Location: ../Visao/cursoBase.php');
        exit();
    }
}
