<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/DocumentoDAO.php");
require_once("../Modelo/DTO/DocumentoDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();
$documentoDAO = new DocumentoDAO();
$documentoDTO = new DocumentoDTO();

// Função utilitária para criar notificações
function criarNotificacao($tipo, $mensagem, $notificacaoDTO, $notificacaoDAO)
{
    $notificacaoDTO->setTipoNotificacoes($tipo);
    $notificacaoDTO->setMensagemNotificacoes($mensagem);
    $notificacaoDTO->setlidaNotificacoes(0);
    $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
    $notificacaoDAO->criarNotificacao($notificacaoDTO);
}

// Função utilitária para redirecionar com mensagem
function redirecionar($mensagem, $tipo, $local)
{
    $_SESSION[$tipo === 'success' ? 'success' : 'error'] = $mensagem;
    $_SESSION['icon'] = $tipo;
    header("location: $local");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // === CADASTRAR DOCUMENTO ===
    if (isset($_POST["criarDocumento"])) {
        $idAluno = filter_input(INPUT_POST, 'idAluno', FILTER_VALIDATE_INT);
        $tipoDocumento = trim(filter_input(INPUT_POST, 'tipoDocumento', FILTER_SANITIZE_STRING));
        //$numeroDocumento = trim(filter_input(INPUT_POST, 'numeroDocumento', FILTER_SANITIZE_STRING)); // Comentado propositalmente

        if ($idAluno && $tipoDocumento) {
            $documentoDTO->setIdAluno($idAluno);
            $documentoDTO->setTipoDocumento($tipoDocumento);
            //$documentoDTO->setNumeroDocumento($numeroDocumento);

            if ($documentoDAO->cadastrar($documentoDTO)) {
                criarNotificacao("Cadastro de documento", "Foi solicitado um documento: $tipoDocumento", $notificacaoDTO, $notificacaoDAO);
                redirecionar("Documento cadastrado com sucesso!", "success", "../Visao/documentoBase.php");
            } else {
                redirecionar("Erro ao cadastrar documento.", "error", "../Visao/documentoBase.php");
            }
        } else {
            redirecionar("Preencha todos os campos obrigatórios.", "warning", "../Visao/documentoBase.php");
        }
    }

    // === ATUALIZAR DOCUMENTO ===
    if (isset($_POST["atualizarDocumento"])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $idAluno = filter_input(INPUT_POST, 'idAluno', FILTER_VALIDATE_INT);
        $tipoDocumento = trim(filter_input(INPUT_POST, 'tipoDocumento', FILTER_SANITIZE_STRING));
        //$numeroDocumento = trim(filter_input(INPUT_POST, 'numeroDocumento', FILTER_SANITIZE_STRING));

        if ($id && $idAluno && $tipoDocumento) {
            $documentoDTO->setIdDocumento($id);
            $documentoDTO->setIdAluno($idAluno);
            $documentoDTO->setTipoDocumento($tipoDocumento);
            //$documentoDTO->setNumeroDocumento($numeroDocumento);

            if ($documentoDAO->actualizar($documentoDTO)) {
                criarNotificacao("Actualização de documento", "Documento atualizado: $tipoDocumento", $notificacaoDTO, $notificacaoDAO);
                redirecionar("Documento atualizado com sucesso!", "success", "../Visao/documentoBase.php");
            } else {
                redirecionar("Erro ao atualizar documento.", "error", "../Visao/documentoBase.php");
            }
        } else {
            redirecionar("Preencha todos os campos obrigatórios.", "warning", "../Visao/documentoBase.php");
        }
    }

    // === DELETAR DOCUMENTO ===
    if (isset($_POST["deletarDocumento"])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            if ($documentoDAO->apagar($id)) {
                criarNotificacao("Eliminação de documento", "Os dados de um documento foram eliminados.", $notificacaoDTO, $notificacaoDAO);
                redirecionar("Documento deletado com sucesso!", "success", "../Visao/documentoBase.php");
            } else {
                redirecionar("Erro ao deletar documento.", "error", "../Visao/documentoBase.php");
            }
        } else {
            redirecionar("Documento inválido para eliminar.", "warning", "../Visao/documentoBase.php");
        }
    }
}
