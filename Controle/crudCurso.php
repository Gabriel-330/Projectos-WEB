<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/CursoDAO.php");
require_once("../Modelo/DTO/CursoDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$cursoDAO = new CursoDAO();
$cursoDTO = new CursoDTO();
$notificacaoDAO = new NotificacoesDAO();
$notificacaoDTO = new NotificacoesDTO();

/**
 * Cria uma notificação com os dados fornecidos.
 */
function criarNotificacao($tipo, $mensagem, $idUtilizador, $notificacaoDTO, $notificacaoDAO)
{
    $notificacaoDTO->setTipoNotificacoes($tipo);
    $notificacaoDTO->setMensagemNotificacoes($mensagem);
    $notificacaoDTO->setlidaNotificacoes(0);
    $notificacaoDTO->setIdUtilizador($idUtilizador);
    $notificacaoDAO->criarNotificacao($notificacaoDTO);
}

function redirecionar($mensagem, $tipo = 'success')
{
    $_SESSION[$tipo === 'success' ? 'success' : 'error'] = $mensagem;
    $_SESSION['icon'] = $tipo;
    header('Location: ../Visao/cursoBase.php');
    exit();
}

// === TRATAMENTO POST ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR CURSO ===
    if (isset($_POST["criarCurso"])) {
        $nomeCurso = trim(filter_input(INPUT_POST, 'nomeCurso', FILTER_SANITIZE_STRING));

        if (!empty($nomeCurso)) {
            $cursoDTO->setNomeCurso($nomeCurso);

            if ($cursoDAO->cadastrarCurso($cursoDTO)) {
                criarNotificacao("Cadastro de curso", "Foi cadastrado um novo curso: $nomeCurso", $_SESSION['idUtilizador'], $notificacaoDTO, $notificacaoDAO);
                redirecionar("Curso cadastrado com sucesso!", "success");
            } else {
                redirecionar("Erro ao cadastrar curso.", "error");
            }
        } else {
            redirecionar("O nome do curso não pode estar vazio.", "warning");
        }
    }

    // === ATUALIZAR CURSO ===
    if (isset($_POST['actualizarCurso'])) {
        $idCurso = filter_input(INPUT_POST, 'idCurso', FILTER_VALIDATE_INT);
        $nomeCurso = trim(filter_input(INPUT_POST, 'nomeCurso', FILTER_SANITIZE_STRING));

        if ($idCurso > 0 && !empty($nomeCurso)) {
            $cursoDTO->setIdCurso($idCurso);
            $cursoDTO->setNomeCurso($nomeCurso);

            if ($cursoDAO->actualizarCurso($cursoDTO)) {
                criarNotificacao("Atualização de curso", "Curso atualizado: $nomeCurso", $_SESSION['idUtilizador'], $notificacaoDTO, $notificacaoDAO);
                redirecionar("Curso atualizado com sucesso!", "success");
            } else {
                redirecionar("Erro ao atualizar curso.", "error");
            }
        } else {
            redirecionar("ID ou nome do curso inválido.", "warning");
        }
    }

    // === APAGAR CURSO ===
    if (isset($_POST['apagarCurso'])) {
        $idCurso = filter_input(INPUT_POST, 'idCurso', FILTER_VALIDATE_INT);

        if ($idCurso > 0) {
            $cursoDTO->setIdCurso($idCurso);

            if ($cursoDAO->apagar($cursoDTO)) {
                criarNotificacao("Eliminação de curso", "Os dados de um curso foram eliminados!", $_SESSION['idUtilizador'], $notificacaoDTO, $notificacaoDAO);
                redirecionar("Curso eliminado com sucesso!", "success");
            } else {
                redirecionar("Erro ao eliminar curso.", "error");
            }
        } else {
            redirecionar("Curso inválido para apagar.", "warning");
        }
    }
}
