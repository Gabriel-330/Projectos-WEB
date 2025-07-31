<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/AlunoDAO.php");
require_once("../Modelo/DTO/AlunoDTO.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();
$dao = new UtilizadorDAO();
$dto = new UtilizadorDTO();
$AlunoDAO = new AlunoDAO();
$AlunoDTO = new AlunoDTO();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $senhaEncriptada = password_hash("estrelaAluno", PASSWORD_DEFAULT);
    // CREATE
    if (isset($_POST["cadastrarAluno"])) {

        $idTurma = $_POST['tipoTurma'];
        $Nome = trim($_POST['nomeAluno']);
        $genero = trim($_POST['generoAluno']);
        $responsavel = trim($_POST['responsavelAluno']);
        $contacto = trim($_POST['contactoResponsavelAluno']);
        $dataNasc = trim($_POST['dataNascAluno']);
        $morada = trim($_POST['moradaAluno']);
        $idCurso = trim($_POST['tipoCurso']);
        $anoIngresso = date("Y");
        $nIdentificacao = ($_POST['nIdentificacao']);

        // Trata o upload da foto
        $fotoAlunoBD = null;

        if (isset($_FILES['fotoAluno']) && $_FILES['fotoAluno']['error'] === UPLOAD_ERR_OK) {
            $arquivoTmp = $_FILES['fotoAluno']['tmp_name'];
            $nomeArquivo = basename($_FILES['fotoAluno']['name']);
            $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

            // Validar tipo de arquivo
            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($extensao, $tiposPermitidos)) {
                $_SESSION['success'] = 'Formato de imagem não suportado. Use JPG, PNG ou GIF.';
                $_SESSION['icon'] = 'error';
                header('location: ../Visao/alunoBase.php');
                exit();
            }

            $novoNome = uniqid('aluno_') . "." . $extensao;
            $pastaDestino = "../alunos/fotos/";

            if (!is_dir($pastaDestino)) {
                mkdir($pastaDestino, 0755, true);
            }

            $caminhoCompleto = $pastaDestino . $novoNome;

            if (move_uploaded_file($arquivoTmp, $caminhoCompleto)) {
                $fotoAlunoBD = "alunos/fotos/" . $novoNome;
            } else {
                $_SESSION['success'] = 'Erro ao salvar a foto do aluno.';
                $_SESSION['icon'] = 'error';
                header('location: ../Visao/alunoBase.php');
                exit();
            }
        } else {
            $_SESSION['success'] = 'Por favor, selecione uma foto válida.';
            $_SESSION['icon'] = 'error';
            header('location: ../Visao/alunoBase.php');
            exit();
        }


        // Verificar duplicidade da Identificação
        if ($AlunoDAO->ExisteIdentificacao($nIdentificacao)) {
            $_SESSION['success'] = 'Esse BI ' . $nIdentificacao . ' já existe.';
            $_SESSION['icon'] = 'warning';
            header("Location: ../Visao/professorBase.php");
            exit();
        }


        $dto->setNomeUtilizador($Nome);
        $dto->setMoradaUtilizador($morada);
        $dto->setNumIdentificacao($nIdentificacao);
        $dto->setTelefoneUtilizador($contacto);
        $dto->setPerfilUtilizador("Aluno");
        $dto->setGeneroUtilizador($genero);
        $dto->setSenhaUtilizador($senhaEncriptada);
        $dto->setDataNascimentoUtilizador($dataNasc);
        $dto->setIdAcesso("1");
        $dao->cadastrar($dto);

        $AlunoDTO->setIdUtilizador($dto->getIdUtilizador());
        $AlunoDTO->setNomeAluno($Nome);
        $AlunoDTO->setGeneroAluno($genero);
        $AlunoDTO->setResponsavelAluno($responsavel);
        $AlunoDTO->setContatoResponsavelAluno($contacto);
        $AlunoDTO->setDataNascimentoAluno($dataNasc);
        $AlunoDTO->setMoradaAluno($morada);
        $AlunoDTO->setIdCurso($idCurso);
        $AlunoDTO->setIdTurma($idTurma);
        $AlunoDTO->setAnoIngressoAluno($anoIngresso);
        $AlunoDTO->setFotoAluno($fotoAlunoBD);
        $AlunoDTO->setnIdentificacao($nIdentificacao);

        if ($AlunoDAO->cadastrar($AlunoDTO)) {

            //Notificações
            $notificacaoDTO->setTipoNotificacoes("Cadastro de aluno");
            $notificacaoDTO->setMensagemNotificacoes("Foi cadastrado um novo aluno!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Aluno cadastrado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/alunoBase.php');
            exit();
        } else {
            $_SESSION['success'] = 'Erro ao cadastrar aluno';
            $_SESSION['icon'] = "error";
            header('location: ../Visao/criarAluno.php');
            exit();
        }
    }


    // UPDATE
    if (isset($_POST["actualizarAluno"])) {

        $AlunoDAO = new AlunoDAO();
        $AlunoDTO = new AlunoDTO();

        $id = $_SESSION['idUtilizador'];
        $idTurma = $_POST['tipoTurma'];
        $idAluno = trim($_POST['idAluno']);
        $nome = trim($_POST['nomeAluno']);
        $genero = trim($_POST['generoAluno']);
        $responsavel = trim($_POST['responsavelAluno']);
        $contacto = trim($_POST['contactoResponsavelAluno']);
        $dataNasc = trim($_POST['dataNascAluno']);
        $morada = trim($_POST['moradaAluno']);
        $idCurso = trim($_POST['tipoCurso']);
        $anoIngresso = date("Y");
        $nIdentificacao = trim($_POST['nIdentificacao']);


        $fotoAlunoBD = null;
        $fotoActual = $AlunoDAO->buscarFotoPorId($idAluno);

        if (isset($_FILES['fotoAluno']) && $_FILES['fotoAluno']['error'] === UPLOAD_ERR_OK) {
            $arquivoTmp = $_FILES['fotoAluno']['tmp_name'];
            $nomeArquivo = basename($_FILES['fotoAluno']['name']);
            $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

            // Validar tipo de arquivo
            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($extensao, $tiposPermitidos)) {
                $_SESSION['success'] = 'Formato de imagem não suportado. Use JPG, PNG ou GIF.';
                $_SESSION['icon'] = 'error';
                header('location: ../Visao/AlunoBase.php');
                exit();
            }

            $novoNome = uniqid('aluno_') . "." . $extensao;
            $pastaDestino = "../alunos/fotos/";

            if (!is_dir($pastaDestino)) {
                mkdir($pastaDestino, 0755, true);
            }

            $caminhoCompleto = $pastaDestino . $novoNome;

            if (move_uploaded_file($arquivoTmp, $caminhoCompleto)) {
                $fotoAlunoBD = "alunos/fotos/" . $novoNome;
            } else {
                $_SESSION['error'] = 'Erro ao salvar a foto do aluno.';
                $_SESSION['icon'] = 'error';
                header('location: ../Visao/AlunoBase.php');
                exit();
            }
        } else {
            $fotoAlunoBD = $fotoActual;
        }



        $AlunoDTO->setIdAluno($idAluno);
        $AlunoDTO->setIdUtilizador($id);
        $AlunoDTO->setNomeAluno($nome);
        $AlunoDTO->setGeneroAluno($genero);
        $AlunoDTO->setResponsavelAluno($responsavel);
        $AlunoDTO->setContatoResponsavelAluno($contacto);
        $AlunoDTO->setDataNascimentoAluno($dataNasc);
        $AlunoDTO->setMoradaAluno($morada);
        $AlunoDTO->setIdCurso($idCurso);
        $AlunoDTO->setIdTurma($idTurma);
        $AlunoDTO->setAnoIngressoAluno($anoIngresso);
        $AlunoDTO->setFotoAluno($fotoAlunoBD);

        if ($AlunoDAO->actualizar($AlunoDTO)) {
            if ($genero == "Masculino") {
                $mensagem = "Os dados do aluno: " . $nome . " foram actualizados";
            } elseif ($genero == "Femenino") {
                $mensagem = "Os dados da aluna: " . $nome . " foram actualizados";
            }
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de aluno");
            $notificacaoDTO->setMensagemNotificacoes($mensagem);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Aluno atualizado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/alunoBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao atualizar aluno';
            $_SESSION['icon'] = "error";
            header('location: ../Visao/alunoBase.php');
            exit();
        }
    }

    // DELETE
    if (isset($_POST["apagarAluno"])) {
        $id = $_POST['idAluno'];
        $alunoDAO = new AlunoDAO();

        if ($alunoDAO->apagar($id)) {
            $notificacaoDTO->setTipoNotificacoes("Delete de aluno");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de um aluno foram removidos!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Aluno deletado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/alunoBase.php');
            exit();
        } else {
            $_SESSION['error'] = 'Erro ao deletar aluno';
            $_SESSION['icon'] = "error";
            header('location: ../Visao/alunoBase.php');
            exit();
        }
        header('location: ../Visao/alunoBase.php');
        exit();
    }
}
