<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/ProfessorDAO.php");
require_once("../Modelo/DTO/ProfessorDTO.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

$conn = (new Conn())->getConexao();

// Instâncias
$senhaEncriptada = password_hash("estrelaProfessor", PASSWORD_DEFAULT);
$dao = new UtilizadorDAO();
$dto = new UtilizadorDTO();
$ProfessorDAO = new ProfessorDAO();
$ProfessorDTO = new ProfessorDTO();
$notificacaoDAO = new NotificacoesDAO();
$notificacaoDTO = new NotificacoesDTO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR ===
    if (isset($_POST["cadastrarProfessor"])) {
        try {
            $nome = trim($_POST['nomeProfessor']);
            $genero = trim($_POST['generoProfessor']);
            $contacto = trim($_POST['contactoProfessor']);
            $email = trim($_POST['emailProfessor']);
            $dataNasc = trim($_POST['dataNascProfessor']);
            $dataCont = trim($_POST['dataContProfessor']);
            $tipoCont = trim($_POST['tipoContProfessor']);
            $morada = trim($_POST['moradaProfessor']);
            $identificacao = trim($_POST['nIdentificacaoProfessor']);

            $mensagem = ($genero == "Masculino")
                ? "Foi cadastrado um novo professor: $nome"
                : "Foi cadastrada uma nova professora: $nome";

            // Verificações de duplicidade
            if ($ProfessorDAO->ExisteIdentificacao($identificacao) || $dao->ExisteIdentificacao($identificacao)) {
                $_SESSION['success'] = "Esse BI $identificacao já existe.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/professorBase.php");
                exit();
            }

            if ($ProfessorDAO->ExisteTelefone($contacto)) {
                $_SESSION['success'] = "Esse contacto $contacto já existe.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/professorBase.php");
                exit();
            }

            if ($ProfessorDAO->ExisteEmail($email)) {
                $_SESSION['success'] = "Esse e-mail $email já existe.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/professorBase.php");
                exit();
            }

            // INÍCIO DA TRANSAÇÃO
            $conn->beginTransaction();

            // 1. Utilizador
            $dto->setNomeUtilizador($nome);
            $dto->setGeneroUtilizador($genero);
            $dto->setTelefoneUtilizador($contacto);
            $dto->setEmailUtilizador($email);
            $dto->setDataNascimentoUtilizador($dataNasc);
            $dto->setMoradaUtilizador($morada);
            $dto->setNumIdentificacao($identificacao);
            $dto->setSenhaUtilizador($senhaEncriptada);
            $dto->setIdAcesso("1");
            $dto->setPerfilUtilizador("Professor");

            if (!$dao->cadastrar($dto)) throw new Exception("Erro ao cadastrar utilizador");

            // 2. Professor
            $ProfessorDTO->setIdUtilizador($dto->getIdUtilizador());
            $ProfessorDTO->setNomeProfessor($nome);
            $ProfessorDTO->setGeneroProfessor($genero);
            $ProfessorDTO->setContactoProfessor($contacto);
            $ProfessorDTO->setEmailProfessor($email);
            $ProfessorDTO->setDataDeNascimentoProfessor($dataNasc);
            $ProfessorDTO->setDataContProfessor($dataCont);
            $ProfessorDTO->setTipoContratoProfessor($tipoCont);
            $ProfessorDTO->setMoradaProfessor($morada);
            $ProfessorDTO->setnIdentificacao($identificacao);

            if (!$ProfessorDAO->cadastrar($ProfessorDTO)) throw new Exception("Erro ao cadastrar professor");

            // 3. Notificação
            $notificacaoDTO->setTipoNotificacoes("Cadastro de professor");
            $notificacaoDTO->setMensagemNotificacoes($mensagem);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) throw new Exception("Erro ao criar notificação");

            $conn->commit();

            $_SESSION['success'] = "Professor cadastrado com sucesso!";
            $_SESSION['icon'] = 'success';
            header("Location: ../Visao/professorBase.php");
            exit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $mensagemErro = $e->getMessage();

            if (str_contains($mensagemErro, '1062')) {
                if (str_contains($mensagemErro, 'telefoneUtilizador')) {
                    $_SESSION['success'] = "Já existe um utilizador com esse número de telefone.";
                } elseif (str_contains($mensagemErro, 'num_Identificacao')) {
                    $_SESSION['success'] = "Já existe um utilizador ou professor com este número de identificação.";
                } elseif (str_contains($mensagemErro, 'emailUtilizador')) {
                    $_SESSION['success'] = "Já existe um utilizador com este email.";
                } else {
                    $_SESSION['success'] = "Dados duplicados encontrados. Verifique os campos.";
                }
            } else {
                $_SESSION['success'] = "Erro inesperado ao cadastrar o professor.";
            }

            $_SESSION['icon'] = "error";
            header("Location: ../Visao/professorBase.php");
            exit();
        }
    }

    // === ACTUALIZAR ===
    elseif (isset($_POST["actualizarProfessor"])) {
        try {
            $id = $_SESSION['idUtilizador'];
            $idProfessor = $_POST['idProfessor'];
            $nome = trim($_POST['nomeProfessor']);
            $genero = trim($_POST['generoProfessor']);
            $contacto = trim($_POST['contactoProfessor']);
            $email = trim($_POST['emailProfessor']);
            $dataNasc = trim($_POST['dataNascProfessor']);
            $dataCont = trim($_POST['dataContProfessor']);
            $tipoCont = trim($_POST['tipoContProfessor']);
            $morada = trim($_POST['moradaProfessor']);
            $identificacao = trim($_POST['nIdentificacao']);

            $conn->beginTransaction();

            $ProfessorDTO->setIdProfessor($idProfessor);
            $ProfessorDTO->setIdUtilizador($id);
            $ProfessorDTO->setNomeProfessor($nome);
            $ProfessorDTO->setGeneroProfessor($genero);
            $ProfessorDTO->setContactoProfessor($contacto);
            $ProfessorDTO->setEmailProfessor($email);
            $ProfessorDTO->setDataDeNascimentoProfessor($dataNasc);
            $ProfessorDTO->setDataContProfessor($dataCont);
            $ProfessorDTO->setTipoContratoProfessor($tipoCont);
            $ProfessorDTO->setMoradaProfessor($morada);
            $ProfessorDTO->setnIdentificacao($identificacao);

            if (!$ProfessorDAO->actualizar($ProfessorDTO)) throw new Exception("Erro ao actualizar professor");

            $notificacaoDTO->setTipoNotificacoes("Actualização de professor");
            $notificacaoDTO->setMensagemNotificacoes("Professor actualizado: $nome");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) throw new Exception("Erro ao criar notificação");

            $conn->commit();

            $_SESSION['success'] = "Actualização efectuada com sucesso!";
            $_SESSION['icon'] = 'success';
            header('Location: ../Visao/professorBase.php');
            exit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $_SESSION['success'] = "Erro ao actualizar professor.";
            $_SESSION['icon'] = 'error';
            header('Location: ../Visao/professorBase.php');
            exit();
        }
    }

    // === APAGAR ===
    elseif (isset($_POST["apagarProfessor"])) {
        try {
            $idProfessor = $_POST["idProfessor"];
            $nomeProfessor = $_POST["nomeProfessor"]; // pode ser enviado via hidden

            // Buscar ID do utilizador correspondente ao professor
            $professor = $ProfessorDAO->buscarPorId($idProfessor);
            if (!$professor) {
                $_SESSION['success'] = "Professor não encontrado.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/professorBase.php");
                exit();
            }

            $idUtilizador = $professor->getIdUtilizador();

            $conn->beginTransaction();

            // Apagar professor
            if (!$ProfessorDAO->apagar($idProfessor)) {
                throw new Exception("Erro ao apagar professor.");
            }

            // Apagar utilizador correspondente
            if (!$dao->apagar($idUtilizador)) {
                throw new Exception("Erro ao apagar utilizador.");
            }

            // Criar notificação
            $notificacaoDTO->setTipoNotificacoes("Remoção de professor");
            $notificacaoDTO->setMensagemNotificacoes("O professor $nomeProfessor foi removido.");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) {
                throw new Exception("Erro ao criar notificação.");
            }

            $conn->commit();

            $_SESSION['success'] = "Professor removido com sucesso!";
            $_SESSION['icon'] = 'success';
            header("Location: ../Visao/professorBase.php");
            exit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $_SESSION['success'] = "Erro ao remover professor.";
            $_SESSION['icon'] = 'error';
            header("Location: ../Visao/professorBase.php");
            exit();
        }
    }
}
