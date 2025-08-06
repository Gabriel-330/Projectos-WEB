<?php
require_once('conn.php');
require_once('../Modelo/DTO/TurmaDTO.php');

class TurmaDAO
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

    public function cadastrar(TurmaDTO $turma)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO turma (nomeTurma,salaTurma, idCurso, idProfessor) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $turma->getNomeTurma(),
                $turma->getSalaTurma(),
                $turma->getIdCurso(),
                $turma->getIdProfessor()
            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar turma: " . $e->getMessage());
        }
    }

    public function contarTodas()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM turma";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar turmas: " . $e->getMessage());
            return 0;
        }
    }


    public function actualizar(TurmaDTO $turma)
    {
        try {
            $sql = "UPDATE turma SET nomeTurma = :nome, salaTurma = :sala, idCurso = :idCurso, idProfessor = :idProfessor WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":nome", $turma->getNomeTurma());
            $stmt->bindValue(":sala", $turma->getSalaTurma());
            $stmt->bindValue(":idCurso", $turma->getIdCurso());
            $stmt->bindValue(":idProfessor", $turma->getIdProfessor());
            $stmt->bindValue(":id", $turma->getIdTurma());

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao atualizar turma: " . $e->getMessage();
            return false;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT t.*, c.nomeCurso, p.nomeProfessor
                FROM turma t
                INNER JOIN curso c ON t.idCurso = c.idCurso
                INNER JOIN professor p ON t.idProfessor = p.idProfessor";

            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];

            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }

            return $resultado;
        } catch (PDOException $e) {
            echo "Erro ao listar turmas: " . $e->getMessage();
            return false;
        }
    }

    public function pesquisarTurma($palavra)
    {
        $sql = "SELECT t.*, c.nomeCurso, p.nomeProfessor
                FROM turma t
                INNER JOIN curso c ON t.idCurso = c.id
                INNER JOIN professor p ON t.idProfessor = p.id where 1=1";
        $params = [];

        if (!empty($palavra)) {
            $sql .= " AND (
            t.nomeTurma LIKE ? OR 
            t.salaTurma LIKE ? OR 
            c.nomeCurso LIKE ? OR 
            p.nomeProfessor LIKE ? OR
        )";
            $params[] = "%$palavra%";
            $params[] = "%$palavra%";
            $params[] = "%$palavra%";
            $params[] = "%$palavra%";
        }

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_CLASS, 'TurmaDTO');
    }



    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM turma WHERE idTurma = :idTurma";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':idTurma', $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearParaDTO($linha);
        } catch (PDOException $e) {
            echo "Erro ao buscar turma: " . $e->getMessage();
            return false;
        }
    }

    public function apagar(TurmaDTO $turma)
    {
        try {
            $sql = "DELETE FROM turma WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $turma->getIdTurma());
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao apagar turma: " . $e->getMessage();
            return false;
        }
    }

    public function verificarTabelaCursoVazia()
    {
        try {
            $stmt = $this->conexao->prepare("SELECT COUNT(*) AS total FROM curso");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['tabela_vazia'] = ($row['total'] == 0);
        } catch (PDOException $e) {
            $_SESSION['tabela_vazia'] = null;
            error_log("Erro ao verificar tabela curso: " . $e->getMessage());
        }
    }
    public function existeTurma($nomeTurma, $idCurso)
    {
        $sql = "SELECT COUNT(*) FROM turma WHERE nomeTurma = :nomeTurma AND idCurso = :idCurso";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":nomeTurma", $nomeTurma);
        $stmt->bindParam(":idCurso", $idCurso);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    private function mapearParaDTO($linha)
    {
        $dto = new TurmaDTO();
        $dto->setIdTurma($linha['idTurma']);
        $dto->setNomeTurma($linha['nomeTurma']);
        $dto->setSalaTurma($linha['salaTurma']);
        $dto->setIdCurso($linha['idCurso']);
        $dto->setIdProfessor($linha['idProfessor']);

        return $dto;
    }
}
