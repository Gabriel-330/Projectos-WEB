<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/DisciplinaDAO.php");
require_once("../Modelo/DTO/DisciplinaDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$conn = (new Conn())->getConexao();
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();
$disciplinaDAO = new DisciplinaDAO();
$disciplinaDTO = new DisciplinaDTO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // === CADASTRAR DISCIPLINA ===
    if (isset($_POST["cadastrarDisciplina"])) {
        try {
            $nome = trim(filter_input(INPUT_POST, 'nomeDisciplina', FILTER_SANITIZE_STRING));
            $classe = trim(filter_input(INPUT_POST, 'classeDisciplina', FILTER_SANITIZE_STRING));
            $curso = filter_input(INPUT_POST, 'cursoDisciplina', FILTER_VALIDATE_INT);
            $professor = filter_input(INPUT_POST, 'professorDisciplina', FILTER_VALIDATE_INT);

            if (!$nome || !$classe || !$curso || !$professor) {
                $_SESSION['success'] = "Todos os campos são obrigatórios.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/disciplinaBase.php");
                exit();
            }

            // Verificar duplicidade
            if ($disciplinaDAO->existeNome($nome, $classe, $curso)) {
                $_SESSION['success'] = "Já existe uma disciplina '$nome' na classe '$classe' para este curso.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/disciplinaBase.php");
                exit();
            }

            $conn->beginTransaction();

            $disciplinaDTO->setNomeDisciplina($nome);
            $disciplinaDTO->setClasseDisciplina($classe);
            $disciplinaDTO->setIdCurso($curso);
            $disciplinaDTO->setIdProfessor($professor);

            if (!$disciplinaDAO->cadastrar($disciplinaDTO)) {
                throw new Exception("Erro ao cadastrar disciplina.");
            }

            $notificacaoDTO->setTipoNotificacoes("Cadastro de disciplina");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrada uma nova disciplina: $nome");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Disciplina cadastrada com sucesso!";
            $_SESSION['icon'] = 'success';
            header("Location: ../Visao/disciplinaBase.php");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = 'error';
            header("Location: ../Visao/criarDisciplina.php");
            exit();
        }
    }

    // === ACTUALIZAR DISCIPLINA ===
    elseif (isset($_POST['actualizarDisciplina'])) {
        try {
            $id = filter_input(INPUT_POST, 'idDisciplina', FILTER_VALIDATE_INT);
            $nome = trim(filter_input(INPUT_POST, 'nomeDisciplina', FILTER_SANITIZE_STRING));
            $classe = trim(filter_input(INPUT_POST, 'classeDisciplina', FILTER_SANITIZE_STRING));
            $curso = filter_input(INPUT_POST, 'cursoDisciplina', FILTER_VALIDATE_INT);
            $professor = filter_input(INPUT_POST, 'professorDisciplina', FILTER_VALIDATE_INT);

            if (!$id || !$nome || !$classe || !$curso || !$professor) {
                $_SESSION['success'] = "Todos os campos são obrigatórios.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/disciplinaBase.php");
                exit();
            }

            // Verificar duplicidade (excluindo a própria disciplina)
            if ($disciplinaDAO->existeNomeOutro($nome, $classe, $curso, $id)) {
                $_SESSION['success'] = "Já existe uma disciplina '$nome' na classe '$classe' para este curso.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/disciplinaBase.php");
                exit();
            }

            $conn->beginTransaction();

            $disciplinaDTO->setIdDisciplina($id);
            $disciplinaDTO->setNomeDisciplina($nome);
            $disciplinaDTO->setClasseDisciplina($classe);
            $disciplinaDTO->setIdCurso($curso);
            $disciplinaDTO->setIdProfessor($professor);

            if (!$disciplinaDAO->actualizar($disciplinaDTO)) {
                throw new Exception("Erro ao atualizar disciplina.");
            }

            $notificacaoDTO->setTipoNotificacoes("Actualização de disciplina");
            $notificacaoDTO->setMensagemNotificacoes("Disciplina actualizada: $nome");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Disciplina actualizada com sucesso!";
            $_SESSION['icon'] = 'success';
            header("Location: ../Visao/disciplinaBase.php");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = 'error';
            header("Location: ../Visao/disciplinaBase.php");
            exit();
        }
    }

    // === APAGAR DISCIPLINA ===
    elseif (isset($_POST['apagarDisciplina'])) {
        try {
            $id = filter_input(INPUT_POST, 'idDisciplina', FILTER_VALIDATE_INT);

            if (!$id) {
                $_SESSION['success'] = "Selecione uma disciplina válida para eliminar.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/disciplinaBase.php");
                exit();
            }

            $conn->beginTransaction();

            if (!$disciplinaDAO->apagar($id)) {
                throw new Exception("Erro ao eliminar disciplina. Existem dados associados!");
            }

            $notificacaoDTO->setTipoNotificacoes("Eliminação de disciplina");
            $notificacaoDTO->setMensagemNotificacoes("Dados de uma disciplina foram eliminados.");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Disciplina eliminada com sucesso!";
            $_SESSION['icon'] = 'success';
            header("Location: ../Visao/disciplinaBase.php");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = 'error';
            header("Location: ../Visao/disciplinaBase.php");
            exit();
        }
    }
}
