<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/DocumentoDTO.php');

class DocumentoDAO
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

    public function cadastrar(DocumentoDTO $doc)
    {
        try {
            $sql = "INSERT INTO documento (
                        idAluno,
                        idProfessor,
                        tipoDocumento,
                        estadoDocumento,
                        dataEmissaoDocumento,
                        cursoDocumento,
                        turmaDocumento,
                        classeDocumento,
                        periodoDocumento,
                        disciplinaDocumento
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([
                $doc->getIdAluno(),
                $doc->getIdProfessor(),
                $doc->getTipoDocumento(),
                $doc->getEstadoDocumento(),
                $doc->getDataEmissaoDocumento(),
                $doc->getCursoDocumento(),
                $doc->getTurmaDocumento(),
                $doc->getClasseDocumento(),
                $doc->getPeriodoDocumento(),
                $doc->getDisciplinaDocumento()
            ]);

            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar documento: " . $e->getMessage());
        }
    }

    public function mostrarDocumentoPorCPCT($id)
    {
        try {
            $sql = "SELECT cursoDocumento, turmaDocumento, classeDocumento, periodoDocumento, disciplinaDocumento 
                FROM documento 
                WHERE idDocumento = :id";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute(); // <- ESSENCIAL

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $dto = new DocumentoDTO();
                $dto->setCursoDocumento($linha['cursoDocumento']);
                $dto->setTurmaDocumento($linha['turmaDocumento']);
                $dto->setClasseDocumento($linha['classeDocumento']);
                $dto->setPeriodoDocumento($linha['periodoDocumento']);
                $dto->setDisciplinaDocumento($linha['disciplinaDocumento']);
                $resultado[] = $dto;
            }

            return $resultado;
        } catch (PDOException $e) {
            die("Erro ao mostrar os documentos: " . $e->getMessage());
        }
    }

    public function contarTodos()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM documento";
            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar documentos: " . $e->getMessage());
            return 0;
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT * FROM documento";
            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao listar documentos: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM documento WHERE idDocumento = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->mapearParaDTO($linha) : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar documento: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorProfessor($id)
    {
        try {
            $sql = "SELECT * FROM documento WHERE idProfessor = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao listar documentos: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorAluno($id)
    {
        try {
            $sql = "SELECT * FROM documento WHERE idAluno = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearParaDTO($linha);
            }
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao listar documentos: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(DocumentoDTO $doc)
    {
        try {
            $sql = "UPDATE documento SET
                        idAluno = ?,
                        idProfessor = ?,
                        tipoDocumento = ?,
                        estadoDocumento = ?,
                        dataEmissaoDocumento = ?,
                        cursoDocumento = ?,
                        turmaDocumento = ?,
                        classeDocumento = ?,
                        periodoDocumento = ?,
                        disciplinaDocumento = ?
                    WHERE idDocumento = ?";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $doc->getIdAluno(),
                $doc->getIdProfessor(),
                $doc->getTipoDocumento(),
                $doc->getEstadoDocumento(),
                $doc->getDataEmissaoDocumento(),
                $doc->getCursoDocumento(),
                $doc->getTurmaDocumento(),
                $doc->getClasseDocumento(),
                $doc->getPeriodoDocumento(),
                $doc->getDisciplinaDocumento(),
                $doc->getIdDocumento()
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao actualizar documento: " . $e->getMessage());
            return false;
        }
    }

    public function apagar(DocumentoDTO $doc)
    {
        try {
            $sql = "DELETE FROM documento WHERE idDocumento = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $doc->getIdDocumento(), PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar documento: " . $e->getMessage());
            return false;
        }
    }

    public function AceitarDocumento($id)
    {
        try {
            $stmt = $this->conexao->prepare("UPDATE Documento SET estadoDocumento = 'Aceite' WHERE idDocumento = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            echo "Erro ao aceitar Documento: " . $e->getMessage();
            return false;
        }
    }
    public function ValidarDocumento($id)
    {
        try {
            $stmt = $this->conexao->prepare("UPDATE Documento SET estadoDocumento = 'Validado' WHERE idDocumento = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            echo "Erro ao aceitar Documento: " . $e->getMessage();
            return false;
        }
    }
    public function RecusarDocumento($id)
    {
        try {
            $stmt = $this->conexao->prepare("UPDATE Documento SET estadoDocumento = 'Recusado' WHERE idDocumento = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            echo "Erro ao aceitar Documento: " . $e->getMessage();
            return false;
        }
    }

    private function mapearParaDTO($linha)
    {
        $dto = new DocumentoDTO();

        if (isset($linha['idDocumento'])) $dto->setIdDocumento($linha['idDocumento']);
        if (isset($linha['idAluno'])) $dto->setIdAluno($linha['idAluno']);
        if (isset($linha['idProfessor'])) $dto->setIdProfessor($linha['idProfessor']);
        if (isset($linha['tipoDocumento'])) $dto->setTipoDocumento($linha['tipoDocumento']);
        if (isset($linha['estadoDocumento'])) $dto->setEstadoDocumento($linha['estadoDocumento']);
        if (isset($linha['dataEmissaoDocumento'])) $dto->setDataEmissaoDocumento($linha['dataEmissaoDocumento']);
        if (isset($linha['cursoDocumento'])) $dto->setCursoDocumento($linha['cursoDocumento']);
        if (isset($linha['turmaDocumento'])) $dto->setTurmaDocumento($linha['turmaDocumento']);
        if (isset($linha['classeDocumento'])) $dto->setClasseDocumento($linha['classeDocumento']);
        if (isset($linha['periodoDocumento'])) $dto->setPeriodoDocumento($linha['periodoDocumento']);
        if (isset($linha['disciplinaDocumento'])) $dto->setDisciplinaDocumento($linha['disciplinaDocumento']);

        return $dto;
    }
}
