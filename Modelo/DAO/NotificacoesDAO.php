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
        try {
            $sql = "INSERT INTO notificacoes (tipoNotificacoes, mensagemNotificacoes, lidaNotificacoes, idUtilizador) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(1, $dto->getTipoNotificacoes());
            $stmt->bindValue(2, $dto->getMensagemNotificacoes());
            $stmt->bindValue(3, $dto->getLidaNotificacoes());
            $stmt->bindValue(4, $dto->getIdUtilizador());

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao inserir notificação: " . $e->getMessage());
            return false;
        }
    }

    public function mostrarTodasNotificacoes()
    {
        return $this->buscarPorSQL("SELECT * FROM notificacoes");
    }

    public function mostrarProfessorNotificacoes()
    {
        $tipos = "'Cadastro de turma','Actualizacao de turma','Delete de turma',
                  'Cadastro de evento','Actualizacao de evento','Delete de evento',
                  'Cadastro de horario','Actualizacao de horario','Delete de horario'";
        return $this->buscarPorSQL("SELECT * FROM notificacoes WHERE tipoNotificacoes IN ($tipos)");
    }

    public function mostrarAlunoNotificacoes()
    {
        $tipos = "'Cadastro de nota','Actualizacao de nota','Delete de nota',
                  'Cadastro de evento','Actualizacao de evento','Delete de evento',
                  'Cadastro de horario','Actualizacao de horario','Delete de horario'";
        return $this->buscarPorSQL("SELECT * FROM notificacoes WHERE tipoNotificacoes IN ($tipos)");
    }

    public function mostrarNotificacoesPorTipo($tipo)
    {
        $sql = "SELECT * FROM notificacoes WHERE tipoNotificacoes = :tipo";
        $stmt = $this->con->prepare($sql);
        $stmt->bindValue(":tipo", $tipo);
        $stmt->execute();
        return $this->mapearResultado($stmt->fetchAll(PDO::FETCH_ASSOC));
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
        $sql = "UPDATE notificacoes SET lidaNotificacoes = 1 WHERE idNotificacao = ?";
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
        $perfil = $_SESSION['perfilUtilizador'] ?? '';

        if ($perfil === "Administrador") {
            $sql = "SELECT COUNT(*) as total FROM notificacoes";
        } elseif ($perfil === "Aluno") {
            $sql = "SELECT COUNT(*) as total FROM notificacoes WHERE tipoNotificacoes IN (
                    'Cadastro de nota','Actualizacao de nota','Delete de nota',
                    'Cadastro de evento','Actualizacao de evento','Delete de evento',
                    'Cadastro de horario','Actualizacao de horario','Delete de horario')";
        } elseif ($perfil === "Professor") {
            $sql = "SELECT COUNT(*) as total FROM notificacoes WHERE tipoNotificacoes IN (
                    'Cadastro de turma','Actualizacao de turma','Delete de turma',
                    'Cadastro de evento','Actualizacao de evento','Delete de evento',
                    'Cadastro de horario','Actualizacao de horario','Delete de horario')";
        } else {
            return 0;
        }

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function apagarNotificacoes($id)
    {
        try {
            $sql = "DELETE FROM notificacoes WHERE idNotificacao = :id";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar a Notificação: " . $e->getMessage());
            return false;
        }
    }

    public function buscarNotificacoes($idUtilizador, $limite = 10)
    {
        try {
            $sql = "SELECT * FROM notificacoes WHERE idUtilizador = :idUtilizador ORDER BY data DESC LIMIT :limite";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(":idUtilizador", $idUtilizador, PDO::PARAM_INT);
            $stmt->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            return $this->mapearResultado($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("Erro ao buscar notificações: " . $e->getMessage());
            return false;
        }
    }

    private function buscarPorSQL($sql)
    {
        try {
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            return $this->mapearResultado($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("Erro ao executar query: " . $e->getMessage());
            return false;
        }
    }

    private function mapearResultado(array $linhas)
    {
        return array_map(function ($linha) {
            $dto = new NotificacoesDTO();
            $dto->setIdNotificacoes($linha['idNotificacao'] ?? null);
            $dto->setTipoNotificacoes($linha['tipoNotificacoes'] ?? null);
            $dto->setMensagemNotificacoes($linha['mensagemNotificacoes'] ?? null);
            $dto->setdataNotificacoes($linha['dataNotificacoes'] ?? null);
            $dto->setlidaNotificacoes($linha['lidaNotificacoes'] ?? 0);
            $dto->setIdUtilizador($linha['idUtilizador'] ?? null);
            return $dto;
        }, $linhas);
    }
}
