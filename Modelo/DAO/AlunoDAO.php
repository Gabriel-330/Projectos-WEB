<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/AlunoDTO.php');

class AlunoDAO
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

    public function cadastrar(AlunoDTO $aluno)
    {
        try {
            $sql = "INSERT INTO aluno (nomeAluno,nIdentificacaoAluno, moradaAluno, dataNascimentoAluno, generoAluno, fotoAluno, responsavelAluno, contactoResponsavelAluno, anoIngressoAluno,idUtilizador,idCurso, idTurma)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([
                $aluno->getNomeAluno(),
                $aluno->getnIdentificacao(),
                $aluno->getMoradaAluno(),
                $aluno->getDataNascimentoAluno(),
                $aluno->getGeneroAluno(),
                $aluno->getFotoAluno(),
                $aluno->getResponsavelAluno(),
                $aluno->getContactoResponsavelAluno(),
                $aluno->getAnoIngressoAluno(),
                $aluno->getIdUtilizador(),
                $aluno->getIdCurso(),
                $aluno->getIdTurma()


            ]);
            // Recuperar o último ID inserido
            $lastId = $this->conexao->lastInsertId();
            $aluno->setIdAluno($lastId);

            return $lastId; // opcionalmente retorna
        } catch (PDOException $e) {
            die("Erro ao cadastrar aluno: " . $e->getMessage());
            return false;
        }
    }



    public function contarMatriculados()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM matricula WHERE estadoMatricula IN ('ATIVA', 'ACTIVA')";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar alunos matriculados: " . $e->getMessage());
            return 0;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT a.*, c.nomeCurso, u.nomeUtilizador, t.nomeTurma
                FROM aluno a
                INNER JOIN curso c ON a.idCurso = c.idCurso 
                INNER JOIN turma t ON a.idTurma = t.idTurma 
                INNER JOIN utilizador u ON a.idUtilizador = u.idUtilizador";

            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao listar alunos: " . $e->getMessage());
            return false;
        }
    }

    public function pesquisar($palavra)
    {
        $sql = "SELECT a.*, c.nomeCurso, t.nomeTurma
            FROM aluno a
            INNER JOIN curso c ON a.idCurso = c.idCurso
            INNER JOIN turma t ON a.idCurso = t.idTurma
            WHERE 1=1";
        $params = [];

        if (!empty($palavra)) {
            $sql .= " AND (
            a.nomeAluno LIKE ? OR
            a.moradaAluno LIKE ? OR
            a.dataNascimentoAluno LIKE ? OR
            a.generoAluno LIKE ? OR
            c.nomeCurso LIKE ? OR
            a.responsavelAluno LIKE ? OR
            t.nomeTurma LIKE ? OR
            a.anoIngressoAluno LIKE ?
        )";

            for ($i = 0; $i < 7; $i++) {
                $params[] = "%$palavra%";
            }
        }

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($params);

        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [];

        foreach ($registros as $linha) {
            $resultado[] = $this->mapearParaDTO($linha);
        }

        return $resultado;
    }

    public function buscarFotoPorId($id)
    {
        try {
            $sql = "SELECT fotoAluno FROM aluno WHERE idAluno = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($linha) {
                return $linha['fotoAluno'];
            } else {
                return null; // aluno não encontrado ou sem foto
            }
        } catch (PDOException $e) {
            die("Erro ao buscar foto do aluno: " . $e->getMessage());
            return false;
        }
    }


    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT a.*, c.nomeCurso, u.nomeUtilizador, t.nomeTurma
                FROM aluno a
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN turma t ON a.idTurma = c.idTurma 
                INNER JOIN utilizador u on a.idUtilizador = u.idUtilizador WHERE idAluno = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearParaDTO($linha);
        } catch (PDOException $e) {
            die("Erro ao buscar aluno: " . $e->getMessage());
            return false;
        }
    }
    public function retornarIdPorUtilizador($id)
    {
        try {
            $sql = "SELECT a.idAluno
                FROM aluno a
                WHERE a.idUtilizador = :id
                LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($linha) {
                return $linha['idAluno']; // apenas o ID
            }

            return null;
        } catch (PDOException $e) {
            die("Erro ao buscar aluno: " . $e->getMessage());
        }
    }


    public function actualizar(AlunoDTO $aluno)
    {
        try {
            $sql = "UPDATE aluno 
                SET nomeAluno = ?, 
                    nIdentificacaoAluno = ?, 
                    moradaAluno = ?, 
                    dataNascimentoAluno = ?, 
                    generoAluno = ?, 
                    fotoAluno = ?, 
                    responsavelAluno = ?, 
                    contactoResponsavelAluno = ?, 
                    anoIngressoAluno = ?, 
                    idCurso = ?,
                    idTurma = ?
                WHERE idAluno = ?";

            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $aluno->getNomeAluno(),
                $aluno->getnIdentificacao(),
                $aluno->getMoradaAluno(),
                $aluno->getDataNascimentoAluno(),
                $aluno->getGeneroAluno(),
                $aluno->getFotoAluno(),
                $aluno->getResponsavelAluno(),
                $aluno->getContactoResponsavelAluno(),
                $aluno->getAnoIngressoAluno(),
                $aluno->getIdCurso(),
                $aluno->getIdTurma(),
                $aluno->getIdAluno()
            ]);
        } catch (PDOException $e) {
            die("Erro ao atualizar aluno: " . $e->getMessage());
            return false;
        }
    }


    public function apagar($id)
    {
        try {
            $sql = "DELETE FROM aluno WHERE idAluno = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar aluno: " . $e->getMessage());
            return false;
        }
    }

    public function ExisteIdentificacao($identificacao)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM aluno WHERE nIdentificacaoAluno = :identificacao";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":identificacao", $identificacao);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar identificacacao do Aluno: " . $e->getMessage());
        }
    }




    private function mapearParaDTO($linha)
    {
        $dto = new AlunoDTO();
        $dto->setIdAluno($linha['idAluno']);
        $dto->setNomeAluno($linha['nomeAluno']);
        $dto->setnIdentificacao($linha['nIdentificacaoAluno']);
        $dto->setMoradaAluno($linha['moradaAluno']);
        $dto->setDataNascimentoAluno($linha['dataNascimentoAluno']);
        $dto->setGeneroAluno($linha['generoAluno']);
        $dto->setFotoAluno($linha['fotoAluno']);
        $dto->setResponsavelAluno($linha['responsavelAluno']);
        $dto->setContatoResponsavelAluno($linha['contactoResponsavelAluno']);
        $dto->setAnoIngressoAluno($linha['anoIngressoAluno']);
        $dto->setIdUtilizador($linha['idUtilizador']);
        $dto->setIdCurso($linha['idCurso']);
        $dto->setIdTurma($linha['idTurma']);
        $dto->setNomeCurso($linha['nomeCurso']);
        $dto->setNomeUtilizador($linha['nomeUtilizador']);
        $dto->setNomeTurma($linha['nomeTurma']);

        if (isset($linha['fotoAluno'])) {
            $dto->setFotoAluno($linha['fotoAluno']);
        }

        if (isset($linha['anoIngressoAluno'])) {
            $dto->setAnoIngressoAluno($linha['anoIngressoAluno']);
        }

        return $dto;
    }
}
