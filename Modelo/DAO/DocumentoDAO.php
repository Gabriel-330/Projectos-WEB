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
                        tipoDocumento,
                        dataEmissaoDocumento,
                        caminhoArquivoDocumento,
                        idAluno,
                        idCurso,
                        idNota,
                        idDisciplina,
                        idTurma,
                        idProfessor
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([
                $doc->getTipoDocumento(),
                $doc->getDataEmissaoDocumento(),
                $doc->getCaminhoArquivoDocumento(),
                $doc->getIdAluno(),
                $doc->getIdCurso(),
                $doc->getIdNota(),
                $doc->getIdDisciplina(),
                $doc->getIdTurma(),
                $doc->getIdProfessor()
            ]);

            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar documento: " . $e->getMessage());
            return false;
        }
    }
public function mostrarDocumentoPorCPCT()
{
    try {
        $sql = "SELECT cursoDocumento,turmaDocumento,classeDocumento,periodoDocumento FROM documento;";
        $stmt = $this->conexao->query($sql);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];

        foreach ($registros as $linha) {
            $dto = new DocumentoDTO();
            $dto->setCursoDocumento($linha['cursoDocumento']);
            $dto->setTurmaDocumento($linha['turmaDocumento']);
            $dto->setClasseDocumento($linha['classeDocumento']);
            $dto->setPeriodoDocumento($linha['periodoDocumento']);

            $resultado[] = $dto;
        }

        return $resultado;
    } catch (PDOException $e) {
        error_log("Erro ao mostrar os documentos: " . $e->getMessage());
        return false;
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
            $sql = "SELECT * FROM documento WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $linha ? $this->mapearParaDTO($linha) : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar documento: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(DocumentoDTO $doc)
    {
        try {
            $sql = "UPDATE documento SET
                        tipoDocumento = ?,
                        dataEmissaoDocumento = ?,
                        caminhoArquivoDocumento = ?,
                        idAluno = ?,
                        idCurso = ?,
                        idNota = ?,
                        idDisciplina = ?,
                        idTurma = ?,
                        idProfessor = ?
                    WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $doc->getTipoDocumento(),
                $doc->getDataEmissaoDocumento(),
                $doc->getCaminhoArquivoDocumento(),
                $doc->getIdAluno(),
                $doc->getIdCurso(),
                $doc->getIdNota(),
                $doc->getIdDisciplina(),
                $doc->getIdTurma(),
                $doc->getIdProfessor(),
                $doc->getIdDocumento() // supondo que o DTO tem o campo "id"
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao actualizar documento: " . $e->getMessage());
            return false;
        }
    }

    public function apagar(DocumentoDTO $doc)
    {
        try {
            $sql = "DELETE FROM documento WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $doc->getIdDocumento());
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao apagar documento: " . $e->getMessage());
            return false;
        }
    }

    private function mapearParaDTO($linha)
    {
        $dto = new DocumentoDTO();

        if (isset($linha['id'])) $dto->setIdDocumento($linha['id']);
        if (isset($linha['tipoDocumento'])) $dto->setTipoDocumento($linha['tipoDocumento']);
        if (isset($linha['dataEmissaoDocumento'])) $dto->setDataEmissaoDocumento($linha['dataEmissaoDocumento']);
        if (isset($linha['caminhoArquivoDocumento'])) $dto->setCaminhoArquivoDocumento($linha['caminhoArquivoDocumento']);
        if (isset($linha['idAluno'])) $dto->setIdAluno($linha['idAluno']);
        if (isset($linha['idCurso'])) $dto->setIdCurso($linha['idCurso']);
        if (isset($linha['idNota'])) $dto->setIdNota($linha['idNota']);
        if (isset($linha['idDisciplina'])) $dto->setIdDisciplina($linha['idDisciplina']);
        if (isset($linha['idTurma'])) $dto->setIdTurma($linha['idTurma']);
        if (isset($linha['idProfessor'])) $dto->setIdProfessor($linha['idProfessor']);

        return $dto;
    }
}
