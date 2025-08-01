<?php
require_once('conn.php');
require_once('../Modelo/DTO/DisciplinaDTO.php');

class DisciplinaDAO
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

    public function cadastrar(DisciplinaDTO $disciplina)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO disciplina (nomeDisciplina,classeDisciplina, idCurso,idProfessor) VALUES (?, ?,?, ?)");
            $stmt->execute([
                $disciplina->getNomeDisciplina(),
                $disciplina->getClasseDisciplina(),
                $disciplina->getIdCurso(),
                $disciplina->getidProfessor()
            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar disciplina: " . $e->getMessage());
        }
    }

    public function actualizar(DisciplinaDTO $disciplina)
    {
        try {
            $sql = "UPDATE disciplina SET nomeDisciplina = :nome,classeDisciplina = :disciplina, idCurso = :idCurso, idProfessor =: idProfessor WHERE idDisciplina = :id";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":nome", $disciplina->getNomeDisciplina());
            $stmt->bindValue(":disciplina", $disciplina->getClasseDisciplina());
            $stmt->bindValue(":idCurso", $disciplina->getIdCurso());
            $stmt->bindValue(":idProfessor", $disciplina->getidProfessor());
            $stmt->bindValue(":id", $disciplina->getIdDisciplina());

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao atualizar disciplina: " . $e->getMessage();
            return false;
        }
    }

    public function contarTodas()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM disciplina";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar disciplina: " . $e->getMessage());
            return 0;
        }
    }


    public function listarTodos()
    {
        try {
            $sql = "SELECT d.*, c.nomeCurso, p.nomeProfessor
                FROM disciplina d
                INNER JOIN curso c ON d.idCurso = c.idCurso
                INNER JOIN professor p ON d.idProfessor = p.idProfessor";
            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            echo "Erro ao listar disciplinas: " . $e->getMessage();
            return false;
        }
    }
    public function listarNomeDisciplinaPorCurso($curso)
    {
        try {
            $sql = "SELECT nomeDisciplina FROM disciplina WHERE idCurso = :idCurso LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":idCurso", $curso, PDO::PARAM_INT);
            $stmt->execute();

            $nome = $stmt->fetchColumn(); // Retorna só o nome da primeira disciplina

            if ($nome !== false) {
                $dto = new DisciplinaDTO();
                $dto->setNomeDisciplina($nome);
                return $dto;
            }

            return null; // Nenhuma disciplina encontrada
        } catch (PDOException $e) {
            echo "Erro ao listar disciplina: " . $e->getMessage();
            return null;
        }
    }


    public function listarTodosCursoClasse($curso, $classe)
    {
        try {
            $sql = "SELECT d.*, c.nomeCurso, p.nomeProfessor
                FROM disciplina d
                INNER JOIN curso c ON d.idCurso = c.idCurso
                INNER JOIN professor p ON d.idProfessor = p.idProfessor
                WHERE c.nomeCurso = :curso AND d.classeDisciplina = :classe";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":curso", $curso, PDO::PARAM_INT);
            $stmt->bindParam(":classe", $classe, PDO::PARAM_STR);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            echo "Erro ao listar disciplinas: " . $e->getMessage();
            return false;
        }
    }


    public function pesquisar($palavra)
    {
        try {
            $sql = "SELECT d.*, c.nomeCurso  
                FROM disciplina d
                INNER JOIN curso c ON d.idCurso = c.id
                WHERE 1=1";
            $params = [];

            if (!empty($palavra)) {
                $sql .= " AND (
                d.nomeDisciplina LIKE ? OR
                c.nomeCurso LIKE ?
            )";

                for ($i = 0; $i < 2; $i++) {
                    $params[] = "%$palavra%";
                }
            }

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha); // substitui pelo nome real do teu método de mapeamento
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao pesquisar disciplinas: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM disciplina WHERE idDisciplina = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearParaDTO($linha);
        } catch (PDOException $e) {
            echo "Erro ao buscar disciplina: " . $e->getMessage();
            return false;
        }
    }

    public function apagar($id)
    {
        try {
            $sql = "DELETE FROM disciplina WHERE idDisciplina = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Erro ao apagar disciplina: " . $e->getMessage());
            return false;
        }
    }

    private function mapearParaDTO($linha)
    {
        $dto = new DisciplinaDTO();
        $dto->setIdDisciplina($linha['idDisciplina']);
        $dto->setNomeDisciplina($linha['nomeDisciplina']);
        $dto->setClasseDisciplina($linha['classeDisciplina']);
        $dto->setNomeProfessor($linha['nomeProfessor']);
        $dto->setIdCurso($linha['idCurso']);
        $dto->setNomeCurso($linha['nomeCurso']);
        $dto->setidProfessor($linha['idProfessor']);
        return $dto;
    }
}
