<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/AlunoDAO.php");
require_once("../Modelo/DTO/AlunoDTO.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
require_once("../Modelo/DAO/MatriculaDAO.php");
require_once("../Modelo/DTO/MatriculaDTO.php");

$conn = (new Conn())->getConexao();

$senhaEncriptada = password_hash("estrelaAluno", PASSWORD_DEFAULT);
$dao = new UtilizadorDAO();
$dto = new UtilizadorDTO();
$AlunoDAO = new AlunoDAO();
$AlunoDTO = new AlunoDTO();
$notificacaoDAO = new NotificacoesDAO();
$notificacaoDTO = new NotificacoesDTO();
$matriculaDAO = new MatriculaDAO();
$matriculaDTO = new MatriculaDTO();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === CADASTRAR ALUNO ===
    if (isset($_POST["cadastrarAluno"])) {
        try {
            // Dados
            $idTurma     = $_POST['tipoTurma'];
            $Nome        = trim($_POST['nomeAluno']);
            $genero      = trim($_POST['generoAluno']);
            $responsavel = trim($_POST['responsavelAluno']);
            $contacto    = trim($_POST['contactoResponsavelAluno']);
            $dataNasc    = trim($_POST['dataNascAluno']);
            $morada      = trim($_POST['moradaAluno']);
            $idCurso     = trim($_POST['tipoCurso']);
            $classeMatricula = $_POST['classeMatricula'];
            $periodoMatricula = trim($_POST['periodoMatricula']);
            $dataMatricula = trim($_POST['dataMatricula']);
            $nIdentificacao = $_POST['nIdentificacao'];
            $anoIngresso = date("Y");

            $mensagem = ($genero == "Masculino")
                ? "Foi cadastrado um novo aluno: $Nome"
                : "Foi cadastrada uma nova aluna: $Nome";

            // Verifica duplicidade
            if ($AlunoDAO->ExisteIdentificacao($nIdentificacao) || $dao->ExisteIdentificacao($nIdentificacao)) {
                $_SESSION['success'] = "Esse BI $nIdentificacao já existe.";
                $_SESSION['icon'] = 'warning';
                header("Location: ../Visao/alunoBase.php");
                exit();
            }

            // Upload da Foto
            $fotoAlunoBD = null;
            if (isset($_FILES['fotoAluno']) && $_FILES['fotoAluno']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['fotoAluno']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['fotoAluno']['name'], PATHINFO_EXTENSION));
                $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($ext, $permitidos)) {
                    $_SESSION['success'] = 'Formato de imagem não suportado.';
                    $_SESSION['icon'] = 'error';
                    header('location: ../Visao/alunoBase.php');
                    exit();
                }

                $nomeFoto = uniqid('aluno_') . ".$ext";
                $destino = "../alunos/fotos/";
                if (!is_dir($destino)) mkdir($destino, 0755, true);

                if (!move_uploaded_file($tmp, $destino . $nomeFoto)) {
                    $_SESSION['success'] = 'Erro ao salvar a foto do aluno.';
                    $_SESSION['icon'] = 'error';
                    header('location: ../Visao/alunoBase.php');
                    exit();
                }

                $fotoAlunoBD = "alunos/fotos/" . $nomeFoto;
            } else {
                $_SESSION['success'] = 'Por favor, selecione uma foto válida.';
                $_SESSION['icon'] = 'error';
                header('location: ../Visao/alunoBase.php');
                exit();
            }

            // INÍCIO DA TRANSAÇÃO
            $conn->beginTransaction();

            // 1. Utilizador
            $dto->setNomeUtilizador($Nome);
            $dto->setMoradaUtilizador($morada);
            $dto->setNumIdentificacao($nIdentificacao);
            $dto->setTelefoneUtilizador($contacto);
            $dto->setPerfilUtilizador("Aluno");
            $dto->setGeneroUtilizador($genero);
            $dto->setSenhaUtilizador($senhaEncriptada);
            $dto->setDataNascimentoUtilizador($dataNasc);
            $dto->setIdAcesso("1");

            if (!$dao->cadastrar($dto)) throw new Exception("Erro ao cadastrar utilizador");

            // 2. Aluno
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

            if (!$AlunoDAO->cadastrar($AlunoDTO)) throw new Exception("Erro ao cadastrar aluno");

            // 3. Matrícula
            $matriculaDTO->setIdTurma($idTurma);
            $matriculaDTO->setIdCurso($idCurso);
            $matriculaDTO->setIdAluno($AlunoDTO->getIdAluno());
            $matriculaDTO->setDataMatricula($dataMatricula);
            $matriculaDTO->setEstadoMatricula("Activa");
            $matriculaDTO->setClasseMatricula($classeMatricula);
            $matriculaDTO->setPeriodoMatricula($periodoMatricula);

            if (!$matriculaDAO->criarMatricula($matriculaDTO)) throw new Exception("Erro ao cadastrar matrícula");

            // 4. Notificação
            $notificacaoDTO->setTipoNotificacoes("Cadastro de aluno");
            $notificacaoDTO->setMensagemNotificacoes($mensagem);
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);

            if (!$notificacaoDAO->criarNotificacao($notificacaoDTO)) throw new Exception("Erro ao criar notificação");

            $conn->commit();

            $_SESSION['success'] = 'Aluno cadastrado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/alunoBase.php');
            exit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $erro = $e->getMessage();

            if (str_contains($erro, '1062')) {
                if (str_contains($erro, 'telefoneUtilizador')) {
                    $_SESSION['success'] = "Já existe um utilizador com esse número de telefone.";
                } elseif (str_contains($erro, 'num_Identificacao')) {
                    $_SESSION['success'] = "Já existe um utilizador ou aluno com este número de identificação.";
                } else {
                    $_SESSION['success'] = "Dados duplicados encontrados.";
                }
            } else {
                $_SESSION['success'] = "Erro inesperado ao cadastrar aluno.";
            }

            $_SESSION['icon'] = "error";
            header('location: ../Visao/alunoBase.php');
            exit();
        }
    }

    // === ACTUALIZAR ALUNO ===
    elseif (isset($_POST["actualizarAluno"])) {
        try {
            $conn->beginTransaction();

            $id = $_SESSION['idUtilizador'];
            $idAluno = trim($_POST['idAluno']);
            $nome = trim($_POST['nomeAluno']);
            $genero = trim($_POST['generoAluno']);
            $responsavel = trim($_POST['responsavelAluno']);
            $contacto = trim($_POST['contactoResponsavelAluno']);
            $dataNasc = trim($_POST['dataNascAluno']);
            $morada = trim($_POST['moradaAluno']);
            $idCurso = trim($_POST['tipoCurso']);
            $idTurma = $_POST['tipoTurma'];
            $classeMatricula = $_POST['classeMatricula'];
            $estadoMatricula = $_POST['estadoMatricula'];
            $periodoMatricula = trim($_POST['periodoMatricula']);
            $dataMatricula = trim($_POST['dataMatricula']);
            $anoIngresso = date("Y");
            $nIdentificacao = trim($_POST['nIdentificacao']);

            // Imagem antiga (vinda do hidden)
            $fotoAlunoBD = $_POST['fotoAlunoAntiga'];

            // Se foi enviada nova imagem
            if (isset($_FILES['fotoAlunoNova']) && $_FILES['fotoAlunoNova']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['fotoAlunoNova']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['fotoAlunoNova']['name'], PATHINFO_EXTENSION));
                $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($ext, $permitidos)) {
                    $_SESSION['success'] = 'Formato inválido. Use JPG, PNG ou GIF.';
                    $_SESSION['icon'] = 'error';
                    header('location: ../Visao/alunoBase.php');
                    exit();
                }

                // Novo nome e destino
                $novoNome = uniqid('aluno_') . ".$ext";
                $destino = "../alunos/fotos/";
                if (!is_dir($destino)) mkdir($destino, 0755, true);

                if (move_uploaded_file($tmp, $destino . $novoNome)) {
                    $fotoAlunoBD = "alunos/fotos/" . $novoNome;
                }
            }

            // Preencher DTO do aluno
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
            $AlunoDTO->setnIdentificacao($nIdentificacao);

            // Atualizar aluno
            if (!$AlunoDAO->actualizar($AlunoDTO)) {
                throw new Exception("Erro ao atualizar aluno.");
            }

            // Verificar se matrícula já existe
            $matriculaExistente = $matriculaDAO->buscarPorIdAluno($idAluno);

            // Preencher DTO de matrícula
            $matriculaDTO->setIdTurma($idTurma);
            $matriculaDTO->setIdCurso($idCurso);
            $matriculaDTO->setIdAluno($idAluno);
            $matriculaDTO->setDataMatricula($dataMatricula);
            $matriculaDTO->setEstadoMatricula($estadoMatricula);
            $matriculaDTO->setClasseMatricula($classeMatricula);
            $matriculaDTO->setPeriodoMatricula($periodoMatricula);

            if ($matriculaExistente) {
                $matriculaDTO->setIdMatricula($matriculaExistente->getIdMatricula());
                if (!$matriculaDAO->actualizarMatricula($matriculaDTO)) {
                    throw new Exception("Erro ao atualizar matrícula.");
                }
            } else {
                if (!$matriculaDAO->criarMatricula($matriculaDTO)) {
                    throw new Exception("Erro ao cadastrar matrícula.");
                }
            }

            // Notificação
            $texto = $genero === "Masculino" ? "aluno" : "aluna";
            $notificacaoDTO->setTipoNotificacoes("Actualização de aluno");
            $notificacaoDTO->setMensagemNotificacoes("Os dados do(a) $texto $nome foram actualizados");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $conn->commit();

            $_SESSION['success'] = 'Aluno atualizado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/alunoBase.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = "error";
            header('location: ../Visao/alunoBase.php');
            exit();
        }
    }


    // === APAGAR ALUNO ===
    elseif (isset($_POST["apagarAluno"])) {
        try {
            $idAluno = $_POST['idAluno'];
            $aluno = $AlunoDAO->buscarPorId($idAluno);
            if (!$aluno) throw new Exception("Aluno não encontrado.");

            $idUtilizador = $aluno->getIdUtilizador();

            $conn->beginTransaction();

            if (!$AlunoDAO->apagar($idAluno)) throw new Exception("Erro ao apagar aluno. Existem dados asoociados!");
            if (!$dao->apagar($idUtilizador)) throw new Exception("Erro ao apagar utilizador.");

            $notificacaoDTO->setTipoNotificacoes("Remoção de aluno");
            $notificacaoDTO->setMensagemNotificacoes("O aluno foi removido.");
            $notificacaoDTO->setlidaNotificacoes(0);
            $notificacaoDTO->setIdUtilizador($_SESSION['idUtilizador']);
            $notificacaoDAO->criarNotificacao($notificacaoDTO);

            $conn->commit();
            $_SESSION['success'] = 'Aluno deletado com sucesso!';
            $_SESSION['icon'] = "success";
            header('location: ../Visao/alunoBase.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['success'] = $e->getMessage();
            $_SESSION['icon'] = "error";
            header('location: ../Visao/alunoBase.php');
            exit();
        }
    }
}
