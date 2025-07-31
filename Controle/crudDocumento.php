<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/DocumentoDAO.php");
require_once("../Modelo/DTO/DocumentoDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CREATE
    if (isset($_POST["criarDocumento"])) {
        $idAluno = $_POST['idAluno'];
        $tipoDocumento = $_POST['tipoDocumento'];
        $numeroDocumento = $_POST['numeroDocumento'];
        $documentoDAO = new DocumentoDAO();
        $documentoDTO = new DocumentoDTO();
        $documentoDTO->setIdAluno($_POST['idAluno']);
        $documentoDTO->setTipoDocumento($_POST['tipoDocumento']);
       // $documentoDTO->setNumeroDocumento($_POST['numeroDocumento']);

        if ($documentoDAO->cadastrar($documentoDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Cadastro de documento");
            $notificacaoDTO->setMensagemNotificacoes("Foi solicitado um documento: ".$tipoDocumento);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Documento cadastrado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/documentoBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao cadastrar documento';
            $_SESSION['icon'] = "error";
        }
    }

    // UPDATE
    if (isset($_POST["atualizarDocumento"])) {
        $id = $_POST['id'];
        $idAluno = $_POST['idAluno'];
        $tipoDocumento = $_POST['tipoDocumento'];
        $numeroDocumento = $_POST['numeroDocumento'];
        $documentoDAO = new DocumentoDAO();
        $documentoDTO = new DocumentoDTO();
        $documentoDTO->setIdDocumento($id);
        $documentoDTO->setIdAluno($_POST['idAluno']);
        $documentoDTO->setTipoDocumento($_POST['tipoDocumento']);
        //$documentoDTO->setNumeroDocumento($_POST['numeroDocumento']);

        if ($documentoDAO->actualizar($documentoDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de documento");
            $notificacaoDTO->setMensagemNotificacoes("Curso actualizado: ".$tipoDocumento);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Documento atualizado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/documentoBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao atualizar documento';
            $_SESSION['icon'] = "error";
        }
    }

    // DELETE
    if (isset($_POST["deletarDocumento"])) {
        $id = $_POST['id'];
        $documentoDAO = new DocumentoDAO();

        if ($documentoDAO->apagar($id)) {
            $notificacaoDTO->setTipoNotificacoes("Delete de documento");
                $notificacaoDTO->setMensagemNotificacoes("Os dados de um documento foram Eliminados!");
                $notificacaoDTO->setlidaNotificacoes(0);
                $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
                $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Documento deletado com sucesso!';
            $_SESSION['icon'] = "success";
        } else {
            $_SESSION['error'] = 'Erro ao deletar documento';
            $_SESSION['icon'] = "error";
        }
        header('location: ../Visao/documentoBase.php');
        exit();
    }
}
?>
