<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/ProfessorDTO.php');

class ProfessorDAO
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
//Adcionei o método cadastrar para inserir um novo professor no banco de dados
    public function cadastrar(ProfessorDTO $professor)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO professor (nomeProfessor, nIdentificacaoProfessor,contactoProfessor, generoProfessor, emailProfessor, moradaProfessor, dataNascProfessor, dataContProfessor, tipoContratoProfessor,idUtilizador) VALUES (?, ?, ?, ?, ?, ?,?,?,?, ?)");
            $stmt->execute([
                $professor->getNomeProfessor(),
                $professor->getnIdentificacao(),
                $professor->getContactoProfessor(),
                $professor->getGeneroProfessor(),
                $professor->getEmailProfessor(),
                $professor->getMoradaProfessor(),
                $professor->getDataDeNascimentoProfessor(),
                $professor->getDataContProfessor(),
                $professor->getTipoContratoProfessor(),
                $professor->getIdUtilizador(),

            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar professor: " . $e->getMessage());
        }
    }
    public function contarTodos()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM professor";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar professores: " . $e->getMessage());
            return 0;
        }
    }


    public function actualizar(ProfessorDTO $professor)
    {
        try {
            $sql = "UPDATE professor SET nomeProfessor = :nome, nIdentificacaoProfessor = :nIdentificacao, contactoProfessor = :contacto, generoProfessor = :genero, emailProfessor = :email, moradaProfessor = :morada, dataNascProfessor = :dataNasc, dataContProfessor = :dataCont, tipoContratoProfessor = :contrato WHERE idProfessor = :id";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":nome", $professor->getNomeProfessor());
            $stmt->bindValue(":nIdentificacao", $professor->getnIdentificacao());
            $stmt->bindValue(":contacto", $professor->getContactoProfessor());
            $stmt->bindValue(":genero", $professor->getGeneroProfessor());
            $stmt->bindValue(":email", $professor->getEmailProfessor());
            $stmt->bindValue(":morada", $professor->getMoradaProfessor());
            $stmt->bindValue(":dataNasc", $professor->getDataDeNascimentoProfessor());
            $stmt->bindValue(":dataCont", $professor->getDataContProfessor());
            $stmt->bindValue(":contrato", $professor->getTipoContratoProfessor());
            $stmt->bindValue(":id", $professor->getIdProfessor());

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao atualizar professor: " . $e->getMessage();
            return false;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT * FROM professor";
            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            echo "Erro ao listar professores: " . $e->getMessage();
            return false;
        }
    }

    public function pesquisar($palavra)
    {
        try {
            $sql = "SELECT * FROM professor WHERE 1=1";
            $params = [];

            if (!empty($palavra)) {
                $sql .= " AND (
                nomeProfessor LIKE ? OR 
                generoProfessor LIKE ? OR 
                emailProfessor LIKE ? OR 
                moradaProfessor LIKE ? OR 
                dataNascProfessor LIKE ? OR 
                dataContProfessor LIKE ? OR 
                tipoContratoProfessor LIKE ?
            )";

                $params = array_fill(0, 7, "%$palavra%");
            }

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pesquisar professores: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM professor WHERE idProfessor = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearParaDTO($linha);
        } catch (PDOException $e) {
            die("Erro ao buscar professor por ID: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorUtilizador($id)
    {
        try {
            $sql = "SELECT idProfessor FROM professor WHERE idUtilizador = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $linha['idProfessor'] : null;
        } catch (PDOException $e) {
            die("Erro ao buscar ID do professor por utilizador: " . $e->getMessage());
            return null;
        }
    }


    public function apagar($id)
    {
        try {
            $sql = "DELETE FROM professor WHERE idProfessor = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar professor: " . $e->getMessage());
            return false;
        }
    }

    public function ExisteIdentificacao($identificacao)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM Professor WHERE nIdentificacaoProfessor = :identificacao";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":identificacao", $identificacao);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar IBM do Professor: " . $e->getMessage());
        }
    }

    public function ExisteEmail($email)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM Professor WHERE emailProfessor = :email";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":email", $email);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar IBM do Professor: " . $e->getMessage());
        }
    }

    public function ExisteTelefone($telefone)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM Professor WHERE contactoProfessor = :contacto";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":contacto", $telefone);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar IBM do Professor: " . $e->getMessage());
        }
    }


    private function mapearParaDTO($linha)
    {
        $dto = new ProfessorDTO();
        $dto->setIdProfessor($linha['idProfessor']);
        $dto->setNomeProfessor($linha['nomeProfessor']);
        $dto->setGeneroProfessor($linha['generoProfessor']);
        $dto->setEmailProfessor($linha['emailProfessor']);
        $dto->setIdProfessor($linha['idProfessor']);
        $dto->setMoradaProfessor($linha['moradaProfessor']);
        $dto->setDataDeNascimentoProfessor($linha['dataNascProfessor']);
        $dto->setDataContProfessor($linha['dataContProfessor']);
        $dto->setTipoContratoProfessor($linha['tipoContratoProfessor']);
        $dto->setnIdentificacao($linha['nIdentificacaoProfessor']);
        $dto->setContactoProfessor($linha['contactoProfessor']);
        $dto->setIdUtilizador($linha['idUtilizador']);



        return $dto;
    }
}
