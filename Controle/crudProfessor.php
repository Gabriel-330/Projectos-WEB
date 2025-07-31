<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/ProfessorDAO.php");
require_once("../Modelo/DTO/ProfessorDTO.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
$notificacaoDTO = new NotificacoesDTO();
$notificacaoDAO = new NotificacoesDAO();
$dao = new UtilizadorDAO();
$dto = new UtilizadorDTO();

$ProfessorDAO = new ProfessorDAO();
$ProfessorDTO = new ProfessorDTO();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $senhaEncriptada = password_hash("estrelaProfessor", PASSWORD_DEFAULT);

    //Cadastrar
    if (isset($_POST["cadastrarProfessor"])) {

        $nomeUtilizador = $_SESSION['nomeUtilizador'];
        $contactoProfessor = trim($_POST['contactoProfessor']);
        $nome = trim($_POST['nomeProfessor']);
        $genero = trim($_POST['generoProfessor']);
        $email = trim($_POST['emailProfessor']);
        $dataNasc = trim($_POST['dataNascProfessor']);
        $dataCont = trim($_POST['dataContProfessor']);
        $tipoCont = trim($_POST['tipoContProfessor']);
        $morada = trim($_POST['moradaProfessor']);
        $identificacao = trim($_POST['nIdentificacaoProfessor']);
        $mensagem;


        // Verificar duplicidade da Identificação
        if ($ProfessorDAO->ExisteIdentificacao($identificacao)) {
            $_SESSION['success'] = 'Esse BI ' . $identificacao . ' já existe.';
            $_SESSION['icon'] = 'warning';
            header("Location: ../Visao/professorBase.php");
            exit();
        }

        // Verificar duplicidade do Telefone
        if ($ProfessorDAO->ExisteTelefone($contactoProfessor)) {
            $_SESSION['success'] = 'Esse contacto ' . $contactoProfessor . ' já existe.';
            $_SESSION['icon'] = 'warning';
            header("Location: ../Visao/professorBase.php");
            exit();
        }

        // Verificar duplicidade do Email
        if ($ProfessorDAO->ExisteEmail($email)) {
            $_SESSION['success'] = 'Esse E-mail ' . $email . ' já existe.';
            $_SESSION['icon'] = 'warning';
            header("Location: ../Visao/professorBase.php");
            exit();
        }

        if ($genero == "Masculino") {
            $mensagem = "Foi cadastrado um novo professor: " . $nome;
        } elseif ($genero == "Femenino") {
            $mensagem = "Foi cadastrada uma nova professora: " . $nome;
        }

        // Verificar duplicidade
        if ($dao->ExisteIdentificacao($identificacao)) {
            $_SESSION['success'] = 'Esse BI ' . $identificacao . ' já existe.';
            $_SESSION['icon'] = 'warning';
            header("Location: ../Visao/professorBase.php");
            exit();
        }
        // Preencher DTO para utilizador
        $dto->setNomeUtilizador($nome);
        $dto->setGeneroUtilizador($genero);
        $dto->setTelefoneUtilizador($contactoProfessor);
        $dto->setEmailUtilizador($email);
        $dto->setDataNascimentoUtilizador($dataNasc);
        $dto->setMoradaUtilizador($morada);
        $dto->setNumIdentificacao($identificacao);
        $dto->setSenhaUtilizador($senhaEncriptada);
        $dto->setIdAcesso("1");
        $dto->setPerfilUtilizador("Professor");
        $dao->cadastrar($dto);

        //DTO para Professor
        $ProfessorDTO->setIdUtilizador($dto->getIdUtilizador());
        $ProfessorDTO->setNomeProfessor($nome);
        $ProfessorDTO->setGeneroProfessor($genero);
        $ProfessorDTO->setContactoProfessor($contactoProfessor);
        $ProfessorDTO->setEmailProfessor($email);
        $ProfessorDTO->setDataDeNascimentoProfessor($dataNasc);
        $ProfessorDTO->setDataContProfessor($dataCont);
        $ProfessorDTO->setTipoContratoProfessor($tipoCont);
        $ProfessorDTO->setMoradaProfessor($morada);
        $ProfessorDTO->setnIdentificacao($identificacao);


        // Tentar cadastrar
        if ($ProfessorDAO->cadastrar($ProfessorDTO)) {

            $notificacaoDTO->setTipoNotificacoes("Cadastro de professor");
            $notificacaoDTO->setMensagemNotificacoes($mensagem);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Professor cadastrado com sucesso!';
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/professorBase.php');
            exit();
        } else {
            $_SESSION['success'] = 'Erro ao cadastrar professor.';
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/criarProfessor.php');
            exit();
        }

        //APAGAR
    } elseif (isset($_POST["apagarProfessor"])) {
        $id = $_POST['idProfessor'];
        // Criar instâncias DAO e DTO
        $ProfessorDAO = new ProfessorDAO();
        $ProfessorDTO = new ProfessorDTO();
        // Preencher DTO
        $ProfessorDTO->setIdProfessor($id);
        // Tentar cadastrar
        if ($ProfessorDAO->apagar($ProfessorDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Delete de professor");
            $notificacaoDTO->setMensagemNotificacoes("Os dados de um professor foram Eliminados!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Professor eliminado com sucesso!';
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/professorBase.php');
            exit();
        } else {
            $_SESSION['success'] = 'Erro ao eliminar professor.';
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/professorBase.php');
            exit();
        }

        //ACTUALIAR
    } elseif (isset($_POST["actualizarProfessor"])) {

        $id = $_SESSION['idUtilizador'];
        $nome = trim($_POST['nomeProfessor']);
        $genero = trim($_POST['generoProfessor']);
        $email = trim($_POST['emailProfessor']);
        $dataNasc = trim($_POST['dataNascProfessor']);
        $dataCont = trim($_POST['dataContProfessor']);
        $tipoCont = trim($_POST['tipoContProfessor']);
        $morada = trim($_POST['moradaProfessor']);
        $identificacao = trim($_POST['nIdentificacao']);
        $idProfessor = $_POST['idProfessor'];

        // Criar instâncias DAO e DTO
        $ProfessorDAO = new ProfessorDAO();
        $ProfessorDTO = new ProfessorDTO();

        // Preencher DTO
        $ProfessorDTO->setIdProfessor($idProfessor);
        $ProfessorDTO->setIdUtilizador($id);
        $ProfessorDTO->setNomeProfessor($nome);
        $ProfessorDTO->setGeneroProfessor($genero);
        $ProfessorDTO->setContactoProfessor($contactoProfessor);
        $ProfessorDTO->setEmailProfessor($email);
        $ProfessorDTO->setDataDeNascimentoProfessor($dataNasc);
        $ProfessorDTO->setDataContProfessor($dataCont);
        $ProfessorDTO->setTipoContratoProfessor($tipoCont);
        $ProfessorDTO->setMoradaProfessor($morada);
        $ProfessorDTO->setnIdentificacao($identificacao);

        // Tentar cadastrar
        if ($ProfessorDAO->actualizar($ProfessorDTO)) {
            $notificacaoDTO->setTipoNotificacoes("Actualizacao de professor");
            $notificacaoDTO->setMensagemNotificacoes("Professor actualizado: " . $nome . "!");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);
            $_SESSION['success'] = 'Actualização efectuada com sucesso!';
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/professorBase.php');
            exit();
        } else {
            $_SESSION['success'] = 'Erro ao cadastrar professor.';
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/professorBase.php');
            exit();
        }
    }
}
