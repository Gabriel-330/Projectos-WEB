<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/TurmaDAO.php");
require_once("../Modelo/DTO/TurmaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

$TurmaDAO = new TurmaDAO();
$TurmaDTO = new TurmaDTO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CADASTRAR TURMA
    if (isset($_POST["cadastrarTurma"])) {

        $nomeTurma = trim($_POST['nomeTurma']);
        $cursoTurma = trim($_POST['cursoTurma']);
        $directorTurma = trim($_POST['directorTurma']);
        $nSalaTurma = trim($_POST['nSalaTurma']);

        $TurmaDTO->setSalaTurma($nSalaTurma);
        $TurmaDTO->setNomeTurma($nomeTurma);
        $TurmaDTO->setIdCurso($cursoTurma);
        $TurmaDTO->setIdProfessor($directorTurma);
        

        if ($TurmaDAO->cadastrar($TurmaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de turma");
                $notificacaoDTO->setMensagemNotificacoes("Foi cadastrada uma nova turma: ".$nomeTurma);
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Turma cadastrada com sucesso!';
            $_SESSION['icon'] = "success";
            header('Location: ../Visao/turmaBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar turma.';
            $_SESSION['icon'] = "error";
            header('Location: ../Visao/criarTurma.php');
            exit();
        }
    }

    // ATUALIZAR TURMA
    if (isset($_POST['actualizar'])) {
        $idTurma = $_POST['idTurma'];
        $nomeTurma = trim($_POST['nomeTurma']);
        $cursoTurma = trim($_POST['cursoTurma']);
        $directorTurma = trim($_POST['directorTurma']);
        $nSalaTurma = trim($_POST['nSalaTurma']);

        $TurmaDTO->setIdTurma($idTurma);
        $TurmaDTO->setNomeTurma($nomeTurma);
        $TurmaDTO->setIdCurso($cursoTurma);
        $TurmaDTO->setIdProfessor($directorTurma);
        $TurmaDTO->setSalaTurma($nSalaTurma);

        if ($TurmaDAO->actualizar($TurmaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de turma");
            $notificacaoDTO->setMensagemNotificacoes("Turma actualizada: ".$nomeTurma);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Turma atualizada com sucesso!';
            $_SESSION['icon'] = "success";
            header('Location: ../Visao/turmaBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao atualizar turma.';
            $_SESSION['icon'] = "error";
            header('Location: ../Visao/editarTurma.php?id=' . $idTurma);
            exit();
        }
    }

    // APAGAR TURMA
    if (isset($_POST['apagar'])) {
        if (!empty($_POST['idTurma'])) {
            $idTurma = $_POST['idTurma'];
            $TurmaDTO->setIdTurma($idTurma);

            if ($TurmaDAO->apagar($TurmaDTO)) {
                $notificacaoDTO->setTipoNotificacoes("Delete de Turma");
                $notificacaoDTO->setMensagemNotificacoes("Os dados de uma Turma foram Eliminados!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Turma apagada com sucesso!';
                $_SESSION['icon'] = "success";
                header('Location: ../Visao/turmaBase.php');
                exit();
            } else {
                $_SESSION['error'] = 'Erro ao apagar turma.';
                $_SESSION['icon'] = "error";
                header('Location: ../Visao/turmaBase.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'Selecione a turma a ser apagada.';
            $_SESSION['icon'] = "warning";
            header('Location: ../Visao/turmaBase.php');
            exit();
        }
    }


    // LISTAR
if (isset($_GET['listar'])) {
    $turmas = $turmaDAO->listarTodos();
    $_SESSION['listaTurmas'] = $turmas;
    header("Location: ../Visao/turmaLista.php");
    exit();
}

// PESQUISAR
if (isset($_GET['pesquisar'])) {
    $palavra = $_GET['pesquisar'];
    $resultado = $turmaDAO->pesquisarTurma($palavra);
    $_SESSION['turmasPesquisadas'] = $resultado;
    header("Location: ../Visao/turmaPesquisa.php");
    exit();
}

// BUSCAR POR ID
if (isset($_GET['buscar']) && !empty($_GET['idTurma'])) {
    $turma = $turmaDAO->buscarPorId($_GET['idTurma']);
    $_SESSION['turmaSelecionada'] = $turma;
    header("Location: ../Visao/turmaDetalhes.php");
    exit();
}

/*APAGAR
if (isset($_GET['apagar']) && !empty($_GET['idTurma'])) {
    $dto = new TurmaDTO();
    $dto->setId($_GET['idTurma']);
    if ($turmaDAO->apagar($dto)) {
        $_SESSION['msg'] = "Turma apagada com sucesso!";
    } else {
        $_SESSION['msg'] = "Erro ao apagar turma.";
    }
    header("Location: ../Visao/turmaLista.php");
    exit();
}
*/
// CONTAR
if (isset($_GET['contar'])) {
    $total = $turmaDAO->contarTodas();
    $_SESSION['totalTurmas'] = $total;
    header("Location: ../Visao/turmaEstatistica.php");
    exit();
}
}
