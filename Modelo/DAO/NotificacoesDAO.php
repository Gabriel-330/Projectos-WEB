<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/NotificacoesDTO.php');



class NotificacoesDAO
{
    private $con;

    public function __construct()
    {
        $conexaoObj = new Conn();
        $this->con = $conexaoObj->getConexao();

        if ($this->con === null) {
            die("Erro: Falha ao obter a conexão com o banco de dados.");
        }
    }

    public function criarNotificacao(NotificacoesDTO $dto)
    {
        $sql = "INSERT INTO notificacoes (tipoNotificacoes, mensagemNotificacoes, dataNotificacoes, lidaNotificacoes, idUtilizador)
                VALUES (?, ?, NOW(), 0, ?)";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            $dto->getTipoNotificacoes(),
            $dto->getMensagemNotificacoes(),
            $dto->getIdUtilizador()
        ]);
    }
    public function mostrarTodasNotificacoes()
    {
        try {
            $sql = "SELECT * FROM Notificacoes";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pegar os dados: " . $e->getMessage());
            return false;
        }
    }
    public function mostrarProfessorNotificacoes()
    {
        try {
            $sql = "SELECT * FROM Notificacoes WHERE tipoNotificacoes='Cadastro de turma'OR tipoNotificacoes='Actualizacao de turma' OR tipoNotificacoes='Delete de turma' 
            OR tipoNotificacoes='Cadastro de evento' OR tipoNotificacoes='Actualizacao de evento' OR tipoNotificacoes='Delete de evento'
            OR tipoNotificacoes='Cadastro de horario' OR tipoNotificacoes='Actualizacao de horario' OR tipoNotificacoes='Delete de horario'";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pegar os dados: " . $e->getMessage());
            return false;
        }
    }
    public function mostrarAlunoNotificacoes()
    {
        try {
            $sql = "SELECT * FROM Notificacoes WHERE tipoNotificacoes='Cadastro de nota'OR tipoNotificacoes='Actualizacao de nota' OR tipoNotificacoes='Delete de nota' 
            OR tipoNotificacoes='Cadastro de evento' OR tipoNotificacoes='Actualizacao de evento' OR tipoNotificacoes='Delete de evento'
            OR tipoNotificacoes='Cadastro de horario' OR tipoNotificacoes='Actualizacao de horario' OR tipoNotificacoes='Delete de horario'";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pegar os dados: " . $e->getMessage());
            return false;
        }
    }
    public function mostrarNotificacoesPorTipo($tipo)
    {
        try {
            $sql = "SELECT *  FROM Notificacoes WHERE tipoNotificacoes=:tipo";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(":tipo", $tipo);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pegar os dados: " . $e->getMessage());
            return false;
        }
    }
    public function listarNotificacoes($idUtilizador)
    {
        $sql = "SELECT * FROM notificacoes WHERE idUtilizador = ? ORDER BY data DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$idUtilizador]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function marcarComoLida($idNotificacao)
    {
        $sql = "UPDATE notificacoes SET lida = 1 WHERE idNotificacao = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$idNotificacao]);
    }

    public function contarNaoLidas($idUtilizador)
    {
        $sql = "SELECT COUNT(*) as total FROM notificacoes WHERE idUtilizador = ? AND lidaNotificacoes = 0";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$idUtilizador]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function contarNotificacaoes()
    {
        if ($_SESSION['perfilUtilizador'] == "Administrador") {
            $sql = "SELECT COUNT(*) as total FROM notificacoes ";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } elseif ($_SESSION['perfilUtilizador'] == "Aluno") {
            $sql = "SELECT COUNT(*) as total FROM notificacoes WHERE tipoNotificacoes='Cadastro de nota'OR tipoNotificacoes='Actualizacao de nota' OR tipoNotificacoes='Delete de nota' 
            OR tipoNotificacoes='Cadastro de evento' OR tipoNotificacoes='Actualizacao de evento' OR tipoNotificacoes='Delete de evento'
            OR tipoNotificacoes='Cadastro de horario' OR tipoNotificacoes='Actualizacao de horario' OR tipoNotificacoes='Delete de horario'";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } elseif ($_SESSION['perfilUtilizador'] == "Professor") {
            $sql = "SELECT COUNT(*) as total FROM notificacoes WHERE tipoNotificacoes='Cadastro de turma'OR tipoNotificacoes='Actualizacao de turma' OR tipoNotificacoes='Delete de turma' 
            OR tipoNotificacoes='Cadastro de evento' OR tipoNotificacoes='Actualizacao de evento' OR tipoNotificacoes='Delete de evento'
            OR tipoNotificacoes='Cadastro de horario' OR tipoNotificacoes='Actualizacao de horario' OR tipoNotificacoes='Delete de horario'";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        }
    }

    public function apagarNotificacoes($id)
    {
        try {
            $sql = "DELETE FROM Notificacoes WHERE idNotificacoes = :id";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar a Notificação: " . $e->getMessage());
            return false;
        }
    }
    private function mapearParaDTO(array $linha)
    {
        $dto = new NotificacoesDTO();

        $dto->setIdNotificacoes($linha['idNotificacao'] ?? null);
        $dto->setTipoNotificacoes($linha['tipoNotificacoes'] ?? null);
        $dto->setMensagemNotificacoes($linha['mensagemNotificacoes'] ?? null);
        $dto->setdataNotificacoes($linha['dataNotificacoes'] ?? null);
        $dto->setlidaNotificacoes($linha['lidaNotificacoes'] ?? 0);
        $dto->setIdUtilizador($linha['idUtilizador'] ?? null);



        return $dto;
    }
    public function buscarNotificacoes($idUtilizador, $limite = 10)
    {
        try {
            $sql = "SELECT * FROM notificacoes WHERE idUtilizador = :idUtilizador ORDER BY data DESC LIMIT :limite";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(":idUtilizador", $idUtilizador, PDO::PARAM_INT);
            $stmt->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            $linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $notificacoes = [];
            foreach ($linhas as $linha) {
                $notificacoes[] = $this->mapearParaDTO($linha);
            }

            return $notificacoes;
        } catch (PDOException $e) {
            die("Erro ao buscar notificações: " . $e->getMessage());
            return false;
        }
    }
}
