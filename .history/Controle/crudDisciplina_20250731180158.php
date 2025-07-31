<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/DisciplinaDAO.php");
require_once("../Modelo/DTO/DisciplinaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

$DisciplinaDAO = new DisciplinaDAO();
$DisciplinaDTO = new DisciplinaDTO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CADASTRAR DISCIPLINA
    if (isset($_POST["cadastrarDisciplina"])) {
        $nomeDisciplina = trim($_POST['nomeDisciplina']);
        $classeDisciplina = trim($_POST['classeDisciplina']);
        $cursoDisciplina = trim($_POST['cursoDisciplina']);
        $professorDisciplina = trim($_POST['professorDisciplina']);

        $DisciplinaDTO->setNomeDisciplina($nomeDisciplina);
        $DisciplinaDTO->setClasseDisciplina($classeDisciplina);
        $DisciplinaDTO->setIdCurso($cursoDisciplina);
        $DisciplinaDTO->setidProfessor($professorDisciplina);

        if ($DisciplinaDAO->cadastrar($DisciplinaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de disciplina");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado uma nova disciplina: " . $nomeDisciplina);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            
            $_SESSION['success'] = 'Disciplina cadastrada com sucesso!';
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/disciplinaBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar disciplina.';
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/criarDisciplina.php');
            exit();
        }
    }

    // APAGAR DISCIPLINA
    elseif (isset($_POST['apagarDisciplina'])) {

        $id = $_POST['idDisciplina'];
        if ($id != null) {
            if ($DisciplinaDAO->apagar($id)) {
                $notificacaoDTO->setTipoNotificacoes("Delete de disciplina");
                $notificacaoDTO->setMensagemNotificacoes("Os dados de uma disciplina foram Eliminados!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
                $_SESSION['success'] = 'Disciplina eliminada com sucesso!';
                $_SESSION['icon'] = 'success';
                header('Location: ../Visao/disciplinaBase.php');
                exit();
            } else {
                $_SESSION['error'] = 'Erro ao eliminar a disciplina.';
                $_SESSION['icon'] = 'error';
                header('Location: ../Visao/disciplinaBase.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'Por favor, selecione uma disciplina para eliminar.';
            $_SESSION['icon'] = 'warning';
            header('Location: ../Visao/disciplinaBase.php');
            exit();
        }

        //ACTUALIZAR 
    } elseif (isset($_POST['actualizarDisciplina'])) {
        $id = $_POST['idDisciplina'];
        $nomeDisciplina = trim($_POST['nomeDisciplina']);
        $classeDisciplina = trim($_POST['classeDisciplina']);
        $cursoDisciplina = trim($_POST['cursoDisciplina']);
        $professorDisciplina = trim($_POST['professorDisciplina']);

        $DisciplinaDTO->setIdDisciplina($id);
        $DisciplinaDTO->setNomeDisciplina($nomeDisciplina);
        $DisciplinaDTO->setNomeDisciplina($nomeDisciplina);
        $DisciplinaDTO->setIdCurso($cursoDisciplina);
        $DisciplinaDTO->setIdProfessor($professorDisciplina);

        if ($DisciplinaDAO->actualizar($DisciplinaDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de disciplina");
            $notificacaoDTO->setMensagemNotificacoes("Disciplina actualizada: " . $nomeDisciplina);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Disciplina atualizada com sucesso!';
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/disciplinaBase.php');
            exit();
        } else {
            $_SESSION['sucess'] = 'Erro ao atualizar a disciplina.';
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/disciplinaBase.php');
            exit();
        }
    }
}
