<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/UtilizadorDTO.php');

class UtilizadorDAO
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

    public function cadastrar(UtilizadorDTO $utilizador)
    {
        try {
            $stmt = $this->conexao->prepare("
            INSERT INTO utilizador (
                nomeUtilizador, nIdentificacao, emailUtilizador, generoUtilizador, telefoneUtilizador, 
                moradaUtilizador, perfilUtilizador, senhaUtilizador, idAcesso
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
            $stmt->execute([
                $utilizador->getNomeUtilizador(),
                $utilizador->getNumIdentificacao(),
                $utilizador->getEmailUtilizador(),
                $utilizador->getGeneroUtilizador(),
                $utilizador->getTelefoneUtilizador(),
                $utilizador->getMoradaUtilizador(),
                $utilizador->getPerfilUtilizador(),
                $utilizador->getSenhaUtilizador(),
                $utilizador->getIdAcesso()
            ]);

            $lastId = $this->conexao->lastInsertId();
            $utilizador->setIdUtilizador($lastId);

            return $lastId;
        } catch (PDOException $e) {
            throw $e; // <-- lança para ser tratado externamente
        }
    }


    public function actualizar(UtilizadorDTO $utilizador)
    {
        try {
            // 🔧 Atualizado para incluir todos os campos
            $sql = "
                UPDATE utilizador SET 
                    nomeUtilizador = :nome,
                    nIdentificacao = :identificacao,
                    emailUtilizador = :email,
                    generoUtilizador = :genero,
                    telefoneUtilizador = :telefone,
                    moradaUtilizador = :morada,
                    perfilUtilizador = :perfil,
                    senhaUtilizador = :senha,
                    idAcesso = :idAcesso
                WHERE id = :id
            ";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":nome", $utilizador->getNomeUtilizador());
            $stmt->bindValue(":identificacao", $utilizador->getNumIdentificacao());
            $stmt->bindValue(":email", $utilizador->getEmailUtilizador());
            $stmt->bindValue(":genero", $utilizador->getGeneroUtilizador());
            $stmt->bindValue(":telefone", $utilizador->getTelefoneUtilizador());
            $stmt->bindValue(":morada", $utilizador->getMoradaUtilizador());
            $stmt->bindValue(":perfil", $utilizador->getPerfilUtilizador());
            $stmt->bindValue(":senha", $utilizador->getSenhaUtilizador());
            $stmt->bindValue(":idAcesso", $utilizador->getIdAcesso());
            $stmt->bindValue(":id", $utilizador->getIdUtilizador());

            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao atualizar os dados: " . $e->getMessage();
            return false;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT * FROM utilizador";
            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha); // 🔧 Vai mapear todos os campos
            }

            return $resultado;
        } catch (Exception $ex) {
            echo "Erro ao listar utilizadores: " . $ex->getMessage();
            return false;
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM utilizador WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearParaDTO($linha);
        } catch (Exception $e) {
            echo "Erro ao buscar utilizador: " . $e->getMessage();
            return false;
        }
    }
    public function buscarPerfilPorIdentificacao($num_identificacao)
    {
        try {
            $sql = "SELECT perfilUtilizador FROM utilizador WHERE nIdentificacao = :num_identificacao";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':num_identificacao', $num_identificacao);
            $stmt->execute();

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $dto = new UtilizadorDTO();
                $dto->setPerfilUtilizador(['perfilUtilizador']);
                return $dto->getPerfilUtilizador();
            }
        } catch (Exception $e) {
            echo "Erro ao buscar utilizador: " . $e->getMessage();
            return false;
        }
    }

    public function apagar(UtilizadorDTO $utilizador)
    {
        try {
            $sql = "DELETE FROM utilizador WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $utilizador->getIdUtilizador());
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao apagar utilizador: " . $e->getMessage();
            return false;
        }
    }

    public function autenticar($num_identificacao)
    {
        try {
            $sql = "SELECT * FROM utilizador WHERE nIdentificacao = :num_identificacao";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":num_identificacao", $num_identificacao);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->mapearParaDTO($linha) : null;
        } catch (PDOException $e) {
            die("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }

    public function ExisteIdentificacao($identificacao)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM Utilizador WHERE nIdentificacao = :identificacao";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":identificacao", $identificacao);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar IBM do Utilizador: " . $e->getMessage());
        }
    }

    public function ExisteTelefone($telefone)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM Utilizador WHERE telefoneUtilizador = :telefone";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":telefone", $telefone);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar IBM do Utilizador: " . $e->getMessage());
        }
    }

    public function ExisteEmail($email)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM Utilizador WHERE emailUtilizador = :email";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":email", $email);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar email do Utilizador: " . $e->getMessage());
        }
    }


    private function mapearParaDTO($linha)
    {
        $dto = new UtilizadorDTO();

        // 🔧 Mapear todos os campos do DTO com base no resultado do banco
        $dto->setIdUtilizador($linha['idUtilizador']);
        $dto->setNomeUtilizador($linha['nomeUtilizador']);
        $dto->setGeneroUtilizador($linha['generoUtilizador']);
        $dto->setEmailUtilizador($linha['emailUtilizador']);
        $dto->setTelefoneUtilizador($linha['telefoneUtilizador']);
        $dto->setMoradaUtilizador($linha['moradaUtilizador']);
        $dto->setPerfilUtilizador($linha['perfilUtilizador']);
        $dto->setSenhaUtilizador($linha['senhaUtilizador']);
        $dto->setIdAcesso($linha['idAcesso']);
        $dto->setNumIdentificacao($linha['nIdentificacao']);

        return $dto;
    }
}
