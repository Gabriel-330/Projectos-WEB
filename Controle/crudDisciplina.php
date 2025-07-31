<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/DisciplinaDAO.php");
require_once("../Modelo/DTO/DisciplinaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

$disciplinaDAO = new DisciplinaDAO();
$disciplinaDTO = new DisciplinaDTO();

// Função auxiliar para criar notificação
function criarNotificacao($tipo, $mensagem, $notificacaoDTO, $notificacaoDAO)
{
    $notificacaoDTO->setTipoNotificacoes($tipo);
    $notificacaoDTO->setMensagemNotificacoes($mensagem);
    $notificacaoDTO->setlidaNotificacoes(0);
    $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
    $notificacaoDAO->criarNotificacao($notificacaoDTO);
}

// Função auxiliar para redirecionamento
function redirecionar($mensagem, $tipo, $local)
{
    $_SESSION[$tipo === 'success' ? 'success' : 'error'] = $mensagem;
    $_SESSION['icon'] = $tipo;
    header("Location: $local");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // === CADASTRAR DISCIPLINA ===
    if (isset($_POST["cadastrarDisciplina"])) {
        $nome = trim(filter_input(INPUT_POST, 'nomeDisciplina', FILTER_SANITIZE_STRING));
        $classe = trim(filter_input(INPUT_POST, 'classeDisciplina', FILTER_SANITIZE_STRING));
        $curso = trim(filter_input(INPUT_POST, 'cursoDisciplina', FILTER_SANITIZE_NUMBER_INT));
        $professor = trim(filter_input(INPUT_POST, 'professorDisciplina', FILTER_SANITIZE_NUMBER_INT));

        if ($nome && $classe && $curso && $professor) {
            $disciplinaDTO->setNomeDisciplina($nome);
            $disciplinaDTO->setClasseDisciplina($classe);
            $disciplinaDTO->setIdCurso($curso);
            $disciplinaDTO->setIdProfessor($professor);

            if ($disciplinaDAO->cadastrar($disciplinaDTO)) {
                criarNotificacao("Cadastro de disciplina", "Foi cadastrada uma nova disciplina: $nome", $notificacaoDTO, $notificacaoDAO);
                redirecionar("Disciplina cadastrada com sucesso!", "success", "../Visao/disciplinaBase.php");
            } else {
                redirecionar("Erro ao cadastrar disciplina.", "error", "../Visao/criarDisciplina.php");
            }
        } else {
            redirecionar("Todos os campos são obrigatórios.", "warning", "../Visao/criarDisciplina.php");
        }
    }

    // === ACTUALIZAR DISCIPLINA ===
    elseif (isset($_POST['actualizarDisciplina'])) {
        $id = filter_input(INPUT_POST, 'idDisciplina', FILTER_VALIDATE_INT);
        $nome = trim(filter_input(INPUT_POST, 'nomeDisciplina', FILTER_SANITIZE_STRING));
        $classe = trim(filter_input(INPUT_POST, 'classeDisciplina', FILTER_SANITIZE_STRING));
        $curso = trim(filter_input(INPUT_POST, 'cursoDisciplina', FILTER_SANITIZE_NUMBER_INT));
        $professor = trim(filter_input(INPUT_POST, 'professorDisciplina', FILTER_SANITIZE_NUMBER_INT));

        if ($id && $nome && $classe && $curso && $professor) {
            $disciplinaDTO->setIdDisciplina($id);
            $disciplinaDTO->setNomeDisciplina($nome);
            $disciplinaDTO->setClasseDisciplina($classe);
            $disciplinaDTO->setIdCurso($curso);
            $disciplinaDTO->setIdProfessor($professor);

            if ($disciplinaDAO->actualizar($disciplinaDTO)) {
                criarNotificacao("Actualização de disciplina", "Disciplina actualizada: $nome", $notificacaoDTO, $notificacaoDAO);
                redirecionar("Disciplina atualizada com sucesso!", "success", "../Visao/disciplinaBase.php");
            } else {
                redirecionar("Erro ao atualizar a disciplina.", "error", "../Visao/disciplinaBase.php");
            }
        } else {
            redirecionar("Todos os campos são obrigatórios.", "warning", "../Visao/disciplinaBase.php");
        }
    }

    // === APAGAR DISCIPLINA ===
    elseif (isset($_POST['apagarDisciplina'])) {
        $id = filter_input(INPUT_POST, 'idDisciplina', FILTER_VALIDATE_INT);

        if ($id) {
            if ($disciplinaDAO->apagar($id)) {
                criarNotificacao("Eliminação de disciplina", "Dados de uma disciplina foram eliminados.", $notificacaoDTO, $notificacaoDAO);
                redirecionar("Disciplina eliminada com sucesso!", "success", "../Visao/disciplinaBase.php");
            } else {
                redirecionar("Erro ao eliminar a disciplina.", "error", "../Visao/disciplinaBase.php");
            }
        } else {
            redirecionar("Selecione uma disciplina válida para eliminar.", "warning", "../Visao/disciplinaBase.php");
        }
    }
}
