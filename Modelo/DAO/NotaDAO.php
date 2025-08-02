<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/NotaDTO.php');

class NotaDAO
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
            $sql = "SELECT COUNT(*) AS total FROM nota";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar notas: " . $e->getMessage());
            return 0;
        }
    }

    public function cadastrar(NotaDTO $notaDTO)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO nota (idAluno, idDisciplina,idCurso,idProfessor, valorNota, dataAvaliacaoNota, tipoAvaliacaoNota,tipoNota, trimestreNota) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->execute([
                $notaDTO->getIdAluno(),
                $notaDTO->getIdDisciplina(),
                $notaDTO->getIdCurso(),
                $notaDTO->getIdProfessor(),
                $notaDTO->getValorNota(),
                $notaDTO->getDataValorNota(),
                $notaDTO->getTipoAvaliacaoNota(),
                $notaDTO->getTipoNota(),
                $notaDTO->getTrimestreNota()

            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar nota: " . $e->getMessage());
        }
    }

    public function actualizar(NotaDTO $notaDTO)
    {
        try {
            $sql = "UPDATE nota SET idAluno = :idAluno, idDisciplina = :idDisciplina, idCurso = :idCurso, idProfessor = :idProfessor, valorNota = :valorNota, dataValorNota = :dataValorNota, tipoAvaliacaoNota = :tipoAvaliacaoNota, tipoNota = :tipoNota, trimestreNota = :trimestreNota WHERE idNota = :idNota";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":idAluno", $notaDTO->getIdAluno());
            $stmt->bindValue(":idDisciplina", $notaDTO->getIdDisciplina());
            $stmt->bindValue(":idCurso", $notaDTO->getIdCurso());
            $stmt->bindValue(":idProfessor", $notaDTO->getIdProfessor());
            $stmt->bindValue(":valorNota", $notaDTO->getValorNota());
            $stmt->bindValue(":dataValorNota", $notaDTO->getDataValorNota());
            $stmt->bindValue(":tipoAvaliacaoNota", $notaDTO->getTipoAvaliacaoNota());
            $stmt->bindValue(":tipoNota", $notaDTO->getTipoNota());
            $stmt->bindValue(":trimestreNota", $notaDTO->getTrimestreNota());
            $stmt->bindValue(":idNota", $notaDTO->getIdNota());

            return $stmt->execute();
        } catch (Exception $e) {
            echo "Erro ao atualizar nota: " . $e->getMessage();
            return false;
        }
    }


    public function listarPorAluno($idAluno)
    {
        try {
            $sql = "SELECT nota.*, disciplina.nomeDisciplina, curso.nomeCurso, aluno.nomeAluno, aluno.dataNascimentoAluno, aluno.responsavelAluno
                FROM nota 
                INNER JOIN disciplina ON nota.idDisciplina = disciplina.idDisciplina
                INNER JOIN aluno ON nota.idAluno = aluno.idAluno
                INNER JOIN professor ON nota.idProfessor = professor.idProfessor
                INNER JOIN curso ON nota.idCurso = curso.idCurso
                WHERE nota.idAluno = :idAluno";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':idAluno', $idAluno, PDO::PARAM_INT);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearNota($linha);
            }

            return $resultado;
        } catch (Exception $ex) {
            echo "Erro ao listar notas do aluno: " . $ex->getMessage();
            return false;
        }
    }


    public function listarPorDC($disciplina, $curso)
    {
        try {
            $sql = "SELECT nota.*, disciplina.nomeDisciplina, curso.nomeCurso, aluno.nomeAluno, aluno.dataNascimentoAluno, aluno.responsavelAluno
                FROM nota 
                INNER JOIN disciplina ON nota.idDisciplina = disciplina.idDisciplina
                INNER JOIN aluno ON nota.idAluno = aluno.idAluno
                INNER JOIN professor ON nota.idProfessor = professor.idProfessor
                INNER JOIN curso ON nota.idCurso = curso.idCurso
                WHERE disciplina.nomeDisciplina = :nomeDisciplina AND curso.nomeCurso = :nomeCurso";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':nomeDisciplina', $disciplina, PDO::PARAM_INT);
            $stmt->bindParam(':nomeCurso', $curso, PDO::PARAM_INT);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearNota($linha);
            }

            return $resultado;
        } catch (Exception $ex) {
            echo "Erro ao listar notas do aluno: " . $ex->getMessage();
            return false;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT nota.*, disciplina.nomeDisciplina, curso.nomeCurso, aluno.nomeAluno, aluno.dataNascimentoAluno, aluno.responsavelAluno
                    FROM nota 
                    INNER JOIN disciplina ON nota.idDisciplina = disciplina.idDisciplina
                    INNER JOIN aluno ON nota.idAluno = aluno.idAluno
                    INNER JOIN professor ON nota.idProfessor = professor.idProfessor
                    INNER JOIN curso ON nota.idCurso = curso.idCurso";
            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearNota($linha);
            }

            return $resultado;
        } catch (Exception $ex) {
            echo "Erro ao listar notas: " . $ex->getMessage();
            return false;
        }
    }

    public function buscarPorId($idNota)
    {
        try {
            $sql = "SELECT nota.*, disciplina.nomeDisciplina, curso.nomeCurso, aluno.nomeAluno, aluno.dataNascimentoAluno, aluno.responsavelAluno
                    FROM nota 
                    INNER JOIN disciplina ON nota.idDisciplina = disciplina.idDisciplina
                    INNER JOIN aluno ON nota.idAluno = aluno.idAluno
                    INNER JOIN professor ON nota.idProfessor = professor.idProfessor
                    INNER JOIN curso ON nota.idCurso = curso.idCurso
                    WHERE nota.idNota = :idNota";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':idNota', $idNota);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearNota($linha);
        } catch (Exception $e) {
            die("Erro ao buscar nota: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorProfessor($idProfessor)
    {
        try {
            $sql = "SELECT nota.*, disciplina.nomeDisciplina, curso.nomeCurso, aluno.nomeAluno, aluno.dataNascimentoAluno, aluno.responsavelAluno
                FROM nota 
                INNER JOIN disciplina ON nota.idDisciplina = disciplina.idDisciplina
                INNER JOIN aluno ON nota.idAluno = aluno.idAluno
                INNER JOIN professor ON nota.idProfessor = professor.idProfessor
                INNER JOIN curso ON nota.idCurso = curso.idCurso
                WHERE nota.idProfessor = :idProfessor";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':idProfessor', $idProfessor);
            $stmt->execute();

            $resultados = [];
            while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resultados[] = $this->mapearNota($linha);
            }

            return $resultados;
        } catch (Exception $e) {
            die("Erro ao buscar notas por professor: " . $e->getMessage());
            return false;
        }
    }

    public function apagar($idNota)
    {
        try {
            $sql = "DELETE FROM nota WHERE idNota = :idNota";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":idNota", $idNota);
            return $stmt->execute();
        } catch (Exception $e) {
            die("Erro ao apagar nota: " . $e->getMessage());
            return false;
        }
    }

    public function pesquisar($palavra)
    {
        $sql = "SELECT nota.*, disciplina.nomeDisciplina, curso.nomeCurso, aluno.nomeAluno, aluno.dataNascimentoAluno, aluno.responsavelAluno
                    FROM nota 
                    INNER JOIN disciplina ON nota.idDisciplina = disciplina.idDisciplina
                    INNER JOIN aluno ON nota.idAluno = aluno.idAluno
                    INNER JOIN curso ON nota.idCurso = curso.idCurso
                WHERE 1=1";
        $params = [];

        if (!empty($palavra)) {
            $sql .= " AND (nota.tipoAvaliacaoNota LIKE ? OR nota.comentariosNota LIKE ? OR disciplina.nomeDisciplina LIKE ?)";
            $params[] = "%$palavra%";
            $params[] = "%$palavra%";
            $params[] = "%$palavra%";
        }

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($params);

        $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [];
        foreach ($notas as $linha) {
            $resultado[] = $this->mapearNota($linha);
        }
        return $resultado;
    }

    private function mapearNota($linha)
    {
        $dto = new NotaDTO();
        $dto->setIdNota($linha['idNota']);
        $dto->setIdCurso($linha['idCurso']);
        $dto->setIdAluno($linha['idAluno']);
        $dto->setIdProfessor($linha['idProfessor']);
        $dto->setIdDisciplina($linha['idDisciplina']);
        $dto->setValorNota($linha['valorNota']);
        $dto->setDataValorNota($linha['dataAvaliacaoNota']);
        $dto->setTipoNota($linha['tipoNota']);
        $dto->setTrimestreNota($linha['trimestreNota']);
        $dto->setTipoAvaliacaoNota($linha['tipoAvaliacaoNota']);
        $dto->setNomeDisciplina($linha['nomeDisciplina']);
        $dto->setNomeCurso($linha['nomeCurso']);
        $dto->setNomeAluno($linha['nomeAluno']);
        $dto->setDataNascimentoAluno($linha['dataNascimentoAluno']);
        $dto->setResponsavelAluno($linha['responsavelAluno']);

        return $dto;
    }
}
