<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/EventosDTO.php');

// resto do código...


class EventosDAO
{
    private $conexao;

    public function __construct()
    {
        $conexaoObj = new Conn();
        $this->conexao = $conexaoObj->getConexao();

        if ($this->conexao === null) {
            die("Erro: Falha ao obter a conexão com o banco de dados.");
        }
    }
    public function contarTodos()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM evento";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar disciplina: " . $e->getMessage());
            return 0;
        }
    }
    public function cadastrar(EventosDTO $eventosDTO)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO Evento (tituloEvento,dataEvento, horaInicio,horaFim, localEvento,responsavelEvento,tipoEvento, idCurso,idUtilizador) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->execute([
                $eventosDTO->getTituloEvento(),
                $eventosDTO->getDataEvento(),
                $eventosDTO->getHoraInicioEvento(),
                $eventosDTO->getHoraFimEvento(),
                $eventosDTO->getLocalEvento(),
                $eventosDTO->getResponsavelEvento(),
                $eventosDTO->getTipoEvento(),
                $eventosDTO->getIdCurso(),
                $eventosDTO->getIdUtilizador()

            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar Evento: " . $e->getMessage());
        }
    }

    public function actualizar(EventosDTO $eventosDTO)
    {
        try {
            $sql = "UPDATE evento SET tituloEvento = :tituloEvento, dataEvento = :dataEvento, horaInicio = :horaInicio, horaFim = :horaFim, localEvento = :localEvento, responsavelEvento = :responsavelEvento, tipoEvento = :tipoEvento, curso_id = :curso_id WHERE idEvento
             = :id";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":tituloEvento", $eventosDTO->getTituloEvento());
            $stmt->bindValue(":dataEvento", $eventosDTO->getDataEvento());
            $stmt->bindValue(":horarioInicio", $eventosDTO->getHoraInicioEvento());
            $stmt->bindValue(":horarioFim", $eventosDTO->getHoraFimEvento());
            $stmt->bindValue(":localEvento", $eventosDTO->getLocalEvento());
            $stmt->bindValue(":responsavelEvento", $eventosDTO->getResponsavelEvento());
            $stmt->bindValue(":curso_id", $eventosDTO->getIdCurso());
            $stmt->bindValue(":id", $eventosDTO->getIdEvento());

            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao atualizar os dados: " . $e->getMessage();
            return false;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT * FROM evento";
            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->listarEvento($linha);
            }

            return $resultado;
        } catch (Exception $ex) {
            echo "Erro ao listar evento: " . $ex->getMessage();
            return false;
        }
    }

    public function pesquisar($palavra)
    {
        try {
            $sql = "SELECT * FROM evento WHERE 1=1";
            $params = [];

            if (!empty($palavra)) {
                $sql .= " AND (
                tituloEvento LIKE ? OR 
                dataEvento LIKE ? OR 
                tipoEvento LIKE ? OR 
                responsavelEvento LIKE ? OR 
                localEvento LIKE ? OR 
                horaInicio LIKE ?
            )";

                $params = array_fill(0, 6, "%$palavra%");
            }

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->listarEvento($linha); // OU listarEvento(), se preferires
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pesquisar eventos: " . $e->getMessage());
            return false;
        }
    }


    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM evento WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->listarEvento($linha);
        } catch (Exception $e) {
            echo "Erro ao buscar evento: " . $e->getMessage();
            return false;
        }
    }


    public function apagar(UtilizadorDTO $utilizador)
    {
        try {
            $sql = "DELETE FROM utilizador WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $utilizador->getId());
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao apagar utilizador: " . $e->getMessage();
            return false;
        }
    }



    private function listarEvento($linha)
    {
        $dto = new EventosDTO();
        $dto->setId($linha['idEvento']);
        $dto->setTituloEvento($linha['tituloEvento']);
        $dto->setDataEvento($linha['dataEvento']);
        $dto->setHoraInicioEvento($linha['horaInicio']);
        $dto->setHoraFimEvento($linha['horaFim']);
        $dto->setLocalEvento($linha['localEvento']);
        $dto->setResponsavelEvento($linha['responsavelEvento']);
        $dto->setTipoEvento($linha['tipoEvento']);
        $dto->setIdCurso($linha['idCurso']);

        return $dto;
    }
}
