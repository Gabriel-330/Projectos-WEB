<?php
session_start();

require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/DocumentoDAO.php");
require_once("../Modelo/DTO/DocumentoDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DAO/ProfessorDAO.php");
require_once("../Modelo/DAO/AlunoDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();
$professorDAO   = new ProfessorDAO();
$alunoDAO       = new AlunoDAO();

$idProfessor = $professorDAO->buscarPorUtilizador($_SESSION["idUtilizador"]);
$idAluno     = $alunoDAO->buscarPorUtilizador($_SESSION["idUtilizador"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* =====================================================
       PROFESSOR
    ======================================================*/

    // === CADASTRAR DOCUMENTO PROFESSOR ===
    if (isset($_POST["solicitarDocumentoProfessor"])) {

        $tipo        = trim($_POST["tipoDocumento"] ?? '');
        $estado      = trim($_POST["estadoDocumento"] ?? '');
        $dataEmissao = date("Y-m-d H:i:s");

        if (empty($tipo) || empty($estado)) {
            $_SESSION['error'] = "Todos os campos são obrigatórios.";
            $_SESSION['icon']  = "error";
            header("Location: ../Visao/documentoProfessorBase.php");
            exit();
        }

        $docDTO = new DocumentoDTO();
        $docDTO->setIdAluno($idAluno);
        $docDTO->setIdProfessor($idProfessor);
        $docDTO->setTipoDocumento($tipo);
        $docDTO->setEstadoDocumento($estado);
        $docDTO->setDataEmissaoDocumento($dataEmissao);

        $docDAO = new DocumentoDAO();

        if ($docDAO->cadastrar($docDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de documento");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo documento.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Documento solicitado com sucesso!";
            $_SESSION['icon']    = "success";
        } else {
            $_SESSION['error'] = "Erro ao solicitar documento.";
            $_SESSION['icon']  = "error";
        }

        header("Location: ../Visao/documentoProfessorBase.php");
        exit();
    }

    // === ATUALIZAR DOCUMENTO PROFESSOR ===
    elseif (isset($_POST["actualizarDocumentoProfessor"])) {

        $id          = trim($_POST["idDocumento"] ?? '');
        $tipo        = trim($_POST["tipoDocumento"] ?? '');
        $estado      = trim($_POST["estadoDocumento"] ?? '');
        $dataEmissao = date("Y-m-d H:i:s");

        if (empty($id) || empty($tipo) || empty($estado)) {
            $_SESSION['error'] = "Todos os campos são obrigatórios.";
            $_SESSION['icon']  = "error";
            header("Location: ../Visao/documentoProfessorBase.php");
            exit();
        }

        $docDTO = new DocumentoDTO();
        $docDTO->setIdDocumento($id);
        $docDTO->setIdAluno($idAluno);
        $docDTO->setIdProfessor($idProfessor);
        $docDTO->setTipoDocumento($tipo);
        $docDTO->setEstadoDocumento($estado);
        $docDTO->setDataEmissaoDocumento($dataEmissao);

        $docDAO = new DocumentoDAO();

        if ($docDAO->actualizar($docDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Atualização de documento");
            $notificacaoDTO->setMensagemNotificacoes("Um documento foi atualizado.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Documento atualizado com sucesso!";
            $_SESSION['icon']    = "success";
        } else {
            $_SESSION['error'] = "Erro ao atualizar documento.";
            $_SESSION['icon']  = "error";
        }

        header("Location: ../Visao/documentoProfessorBase.php");
        exit();
    }

    // === APAGAR DOCUMENTO PROFESSOR ===
    elseif (isset($_POST["apagarDocumentoProfessor"])) {

        $id = $_POST["idDocumento"] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "ID de documento inválido.";
            $_SESSION['icon']  = "error";
            header("Location: ../Visao/documentoProfessorBase.php");
            exit();
        }

        $docDTO = new DocumentoDTO();
        $docDTO->setIdDocumento($id);

        $docDAO = new DocumentoDAO();

        if ($docDAO->apagar($docDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Eliminação de documento");
            $notificacaoDTO->setMensagemNotificacoes("Um documento foi eliminado.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Documento apagado com sucesso!";
            $_SESSION['icon']    = "success";
        } else {
            $_SESSION['error'] = "Erro ao apagar documento.";
            $_SESSION['icon']  = "error";
        }

        header("Location: ../Visao/documentoProfessorBase.php");
        exit();
    }


    /* =====================================================
       ALUNO
    ======================================================*/

    // === CADASTRAR DOCUMENTO ALUNO ===
    elseif (isset($_POST["solicitarDocumentoAluno"])) {

        $tipo        = trim($_POST["tipoDocumento"] ?? '');
        $directorTurma        = trim($_POST["idProfessor"] ?? '');
        $estado      = trim($_POST["estadoDocumento"] ?? '');
        $dataEmissao = date("Y-m-d H:i:s");

        if (empty($tipo) || empty($estado)) {
            $_SESSION['error'] = "Todos os campos são obrigatórios.";
            $_SESSION['icon']  = "error";
            header("Location: ../Visao/documentoAlunoBase.php");
            exit();
        }

        $docDTO = new DocumentoDTO();
        $docDTO->setIdAluno($idAluno);
        $docDTO->setIdProfessor($directorTurma);
        $docDTO->setTipoDocumento($tipo);
        $docDTO->setEstadoDocumento($estado);
        $docDTO->setDataEmissaoDocumento($dataEmissao);

        $docDAO = new DocumentoDAO();

        if ($docDAO->cadastrar($docDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de documento");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo documento.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Documento solicitado com sucesso!";
            $_SESSION['icon']    = "success";
        } else {
            $_SESSION['error'] = "Erro ao solicitar documento.";
            $_SESSION['icon']  = "error";
        }

        header("Location: ../Visao/documentoAlunoBase.php");
        exit();
    }

    // === ATUALIZAR DOCUMENTO ALUNO ===
    elseif (isset($_POST["actualizarDocumentoAluno"])) {

        $id          = trim($_POST["idDocumento"] ?? '');
        $directorTurma        = trim($_POST["idProfessor"] ?? '');
        $tipo        = trim($_POST["tipoDocumento"] ?? '');
        $estado      = trim($_POST["estadoDocumento"] ?? '');
        $dataEmissao = date("Y-m-d H:i:s");

        if (empty($id) || empty($tipo) || empty($estado)) {
            $_SESSION['error'] = "Todos os campos são obrigatórios.";
            $_SESSION['icon']  = "error";
            header("Location: ../Visao/documentoAlunoBase.php");
            exit();
        }

        $docDTO = new DocumentoDTO();
        $docDTO->setIdDocumento($id);
        $docDTO->setIdAluno($idAluno);
        $docDTO->setIdProfessor($directorTurma);
        $docDTO->setTipoDocumento($tipo);
        $docDTO->setEstadoDocumento($estado);
        $docDTO->setDataEmissaoDocumento($dataEmissao);

        $docDAO = new DocumentoDAO();

        if ($docDAO->actualizar($docDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Atualização de documento");
            $notificacaoDTO->setMensagemNotificacoes("Um documento foi atualizado.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Documento atualizado com sucesso!";
            $_SESSION['icon']    = "success";
        } else {
            $_SESSION['error'] = "Erro ao atualizar documento.";
            $_SESSION['icon']  = "error";
        }

        header("Location: ../Visao/documentoAlunoBase.php");
        exit();
    }

    // === APAGAR DOCUMENTO ALUNO ===
    elseif (isset($_POST["apagarDocumentoAluno"])) {

        $id = $_POST["idDocumento"] ?? null;

        if (empty($id)) {
            $_SESSION['error'] = "ID de documento inválido.";
            $_SESSION['icon']  = "error";
            header("Location: ../Visao/documentoAlunoBase.php");
            exit();
        }

        $docDTO = new DocumentoDTO();
        $docDTO->setIdDocumento($id);

        $docDAO = new DocumentoDAO();

        if ($docDAO->apagar($docDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Eliminação de documento");
            $notificacaoDTO->setMensagemNotificacoes("Um documento foi eliminado.");
            $notificacaoDTO->setLidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $_SESSION['success'] = "Documento cancelado com sucesso!";
            $_SESSION['icon']    = "success";
        } else {
            $_SESSION['error'] = "Erro ao apagar documento.";
            $_SESSION['icon']  = "error";
        }

        header("Location: ../Visao/documentoAlunoBase.php");
        exit();
    }
}
