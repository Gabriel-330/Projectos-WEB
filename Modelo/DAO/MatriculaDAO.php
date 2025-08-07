<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/MatriculaDTO.php');

class MatriculaDAO
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

    public function criarMatricula(MatriculaDTO $matricula)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO Matricula (idAluno, idTurma, idCurso, dataMatricula, estadoMatricula, classeMatricula, periodoMatricula) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $matricula->getIdAluno(),
                $matricula->getIdTurma(),
                $matricula->getIdCurso(),
                $matricula->getDataMatricula(),
                $matricula->getEstadoMatricula(),
                $matricula->getClasseMatricula(),
                $matricula->getPeriodoMatricula(),

            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar a matrícula: " . $e->getMessage());
        }
    }

    public function actualizarMatricula(MatriculaDTO $matricula)
    {
        try {
            $sql = "UPDATE Matricula SET 
                        idAluno = :idAluno, 
                        idTurma = :idTurma,
                        idCurso = :idCurso, 
                        dataMatricula = :dataMatricula, 
                        estadoMatricula = :estadoMatricula, 
                        classeMatricula = :classeMatricula, 
                        periodoMatricula = :periodoMatricula, 
                    WHERE idMatricula = :id";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":idAluno", $matricula->getIdAluno());
            $stmt->bindValue(":idTurma", $matricula->getIdTurma());
            $stmt->bindValue(":idCurso", $matricula->getIdCurso());
            $stmt->bindValue(":dataMatricula", $matricula->getDataMatricula());
            $stmt->bindValue(":estadoMatricula", $matricula->getEstadoMatricula());
            $stmt->bindValue(":classeMatricula", $matricula->getClasseMatricula());
            $stmt->bindValue(":periodoMatricula", $matricula->getPeriodoMatricula());
            $stmt->bindValue(":id", $matricula->getIdMatricula());

            return $stmt->execute();
        } catch (Exception $e) {
            die("Erro ao actualizar a matrícula: " . $e->getMessage());
            return false;
        }
    }

    public function listarTodasMatriculas()
    {
        try {
            $sql = "SELECT m.*, a.nomeAluno, c.nomeCurso, t.nomeTurma
                    FROM matricula m
                    INNER JOIN aluno a ON m.idAluno = a.idAluno
                    INNER JOIN turma t ON m.idTurma = t.idTurma
                    INNER JOIN curso c ON t.idCurso = c.idCurso";

            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearMatricula($linha);
            }

            return $resultado;
        } catch (Exception $e) {
            error_log("Erro ao listar matrículas: " . $e->getMessage());
            return false;
        }
    }
    public function listarPorAluno($idAluno)
    {
        try {
            $sql = "SELECT m.*, a.nomeAluno, c.nomeCurso, t.nomeTurma
                FROM matricula m
                INNER JOIN aluno a ON m.idAluno = a.idAluno
                INNER JOIN turma t ON m.idTurma = t.idTurma
                INNER JOIN curso c ON t.idCurso = c.idCurso
                WHERE m.idAluno = :aluno";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":aluno", $idAluno, PDO::PARAM_INT);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearMatricula($linha);
            }

            return $resultado;
        } catch (Exception $e) {
            die("Erro ao listar matrículas: " . $e->getMessage());
            return [];
        }
    }

    public function listarPorAlunoClasse($idAluno, $classe)
    {
        try {
            $sql = "SELECT m.*, a.nomeAluno, c.nomeCurso, t.nomeTurma
                FROM matricula m
                INNER JOIN aluno a ON m.idAluno = a.idAluno
                INNER JOIN turma t ON m.idTurma = t.idTurma
                INNER JOIN curso c ON t.idCurso = c.idCurso
                WHERE m.idAluno = :aluno AND m.classeMatricula = :classe";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":aluno", $idAluno, PDO::PARAM_INT);
            $stmt->bindParam(":classe", $classe, PDO::PARAM_INT);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearMatricula($linha);
            }

            return $resultado;
        } catch (Exception $e) {
            error_log("Erro ao listar matrículas: " . $e->getMessage());
            return [];
        }
    }

    public function listarMatriculaPorCPCT($classe, $periodo, $curso, $turma)
    {
        try {
            $sql = "SELECT m.*, a.nomeAluno, c.nomeCurso, t.nomeTurma
                FROM matricula m
                INNER JOIN aluno a ON m.idAluno = a.idAluno
                INNER JOIN turma t ON m.idTurma = t.idTurma
                INNER JOIN curso c ON t.idCurso = c.idCurso
                WHERE m.classeMatricula = :classe AND m.periodoMatricula = :periodo AND c.nomeCurso = :curso AND t.nomeTurma = :turma";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":classe", $classe, PDO::PARAM_INT);
            $stmt->bindParam(":periodo", $periodo, PDO::PARAM_INT);
            $stmt->bindParam(":curso", $curso, PDO::PARAM_STR);
            $stmt->bindParam(":turma", $turma, PDO::PARAM_STR); 

            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearMatricula($linha);
            }

            return $resultado;
        } catch (Exception $e) {
            error_log("Erro ao listar matrículas: " . $e->getMessage());
            return [];
        }
    }


    public function buscarMatriculaPorId($id)
    {
        try {
            $sql = "SELECT * FROM Matricula WHERE idMatricula = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearMatricula($linha);
        } catch (Exception $e) {
            echo "Erro ao buscar matrícula: " . $e->getMessage();
            return false;
        }
    }

    public function apagarMatricula($id)
    {
        try {
            $sql = "DELETE FROM Matricula WHERE idMatricula = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao apagar matrícula: " . $e->getMessage();
            return false;
        }
    }

    public function pesquisarMatriculas($palavra)
    {
        $sql = "SELECT m.*, a.nomeAluno, c.nomeCurso, t.nomeTurma
                FROM matricula m
                INNER JOIN aluno a ON m.idAluno = a.idAluno
                INNER JOIN turma t ON m.idTurma = t.idTurma
                INNER JOIN curso c ON t.idCurso = c.idCurso
                WHERE 1=1";
        $params = [];

        if (!empty($palavra)) {
            $sql .= " AND (
                a.nomeAluno LIKE ? OR 
                c.nomeCurso LIKE ? OR
                t.nomeTurma LIKE ? OR  
                m.estadoMatricula LIKE ? OR 
                m.dataMatricula LIKE ?
            )";
            for ($i = 0; $i < 5; $i++) {
                $params[] = "%$palavra%";
            }
        }

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($params);

        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [];
        foreach ($registros as $linha) {
            $resultado[] = $this->mapearMatricula($linha);
        }

        return $resultado;
    }

    private function mapearMatricula($linha)
    {
        $dto = new MatriculaDTO();
        $dto->setIdMatricula($linha['idMatricula']);
        $dto->setIdAluno($linha['idAluno']);
        $dto->setIdTurma($linha['idTurma']);
        $dto->setDataMatricula($linha['dataMatricula']);
        $dto->setEstadoMatricula($linha['estadoMatricula']);
        $dto->setIdCurso($linha['idCurso']);
        $dto->setNomeAluno($linha['nomeAluno'] ?? null);
        $dto->setNomeTurma($linha['nomeTurma'] ?? null);
        $dto->setNomeCurso($linha['nomeCurso'] ?? null);
        $dto->setClasseMatricula($linha['classeMatricula']);
        $dto->setPeriodoMatricula($linha['periodoMatricula']);

        return $dto;
    }
}
