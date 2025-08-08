<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/CursoDTO.php');

class CursoDAO
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

    public function cadastrarCurso(CursoDTO $curso)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO Curso (nomeCurso) VALUES (?)");
            $stmt->execute([
                $curso->getNomeCurso()
            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar o Curso: " . $e->getMessage());
        }
    }

    public function Mostrar()
    {
        try {
            $sql = "SELECT * FROM curso";
            $stmt = $this->conexao->query($sql);
            $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $curso = [];

            foreach ($lista as $linha) {
                $curso[] = $this->ListarCurso($linha);
            }

            return $curso;
        } catch (PDOException $e) {
            echo "Erro ao mostrar curso: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarCurso(CursoDTO $curso)
    {
        try {
            $sql = "UPDATE curso SET nomeCurso = ? WHERE idCurso = ?";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $curso->getNomeCurso(),
                $curso->getIdCurso(),

            ]);
        } catch (PDOException $e) {
            error_log("Erro ao actualizar o Curso: " . $e->getMessage());
            return false;
        }
    }
    public function pesquisar($palavra)
    {
        try {
            $sql = "SELECT * FROM curso WHERE 1=1";
            $params = [];

            if (!empty($palavra)) {
                $sql .= " AND nomeCurso LIKE ?";
                $params[] = "%$palavra%";
            }

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);
            $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($lista as $linha) {
                $resultado[] = $this->ListarCurso($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pesquisar cursos: " . $e->getMessage());
            return false;
        }
    }


    public function MostrarPorID($id)
    {
        try {
            $sql = "SELECT * FROM curso WHERE idCurso = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);

            return $this->ListarCurso($linha);
        } catch (PDOException $e) {
            echo "Erro ao mostrar curso por ID: " . $e->getMessage();
            return false;
        }
    }

    public function buscarIdPorNomeCurso($nomeCurso)
    {
        try {
            $sql = "SELECT idCurso FROM curso WHERE nomeCurso = :nome";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':nome', $nomeCurso);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? $resultado['idCurso'] : null;
        } catch (PDOException $e) {
            echo "Erro ao buscar ID do curso pelo nome: " . $e->getMessage();
            return false;
        }
    }


    public function Apagar(CursoDTO $curso)
    {
        try {
            $sql = "DELETE FROM curso WHERE idCurso = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $curso->getIdCurso());

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar curso: " . $e->getMessage());
            return false;
        }
    }
    // Verifica se já existe curso com o mesmo nome, excluindo o próprio ID
    public function existeNomeOutro($nomeCurso, $idCurso)
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                  FROM curso 
                 WHERE nomeCurso = ? 
                   AND idCurso <> ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(1, $nomeCurso);
            $stmt->bindValue(2, $idCurso, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['total'] > 0);
        } catch (PDOException $e) {
            error_log("Erro ao verificar duplicidade de curso (excluindo ID): " . $e->getMessage());
            return false;
        }
    }

    // Verifica se já existe curso com o mesmo nome
    public function existeNome($nomeCurso)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM curso WHERE nomeCurso = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(1, $nomeCurso);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['total'] > 0);
        } catch (PDOException $e) {
            error_log("Erro ao verificar duplicidade de curso: " . $e->getMessage());
            return false;
        }
    }


    public function ListarCurso($linha)
    {
        $curso = new CursoDTO();
        $curso->setidCurso($linha['idCurso']);
        $curso->setNomeCurso($linha['nomeCurso']);


        return $curso;
    }
}
