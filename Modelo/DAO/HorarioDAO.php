<?php
require_once("conn.php");
require_once(__DIR__ . '/../DTO/HorarioDTO.php');

// resto do código...


class HorarioDAO
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

    public function criarHorario(HorarioDTO $horario)
    {
        try {
            $stmt = $this->conexao->prepare("INSERT INTO Horario (idTurma, idDisciplina,idCurso, diasSemanaHorario, horaInicioHorario,horaFimHorario) VALUES (?,?,?,?,?,?)");
            $stmt->execute([
                $horario->getTurmaId(),
                $horario->getDisciplinaId(),
                $horario->getIdCurso(),
                $horario->getDiaSemana(),
                $horario->getHoraInicio(),
                $horario->getHoraFim(),

            ]);
            return true;
        } catch (PDOException $e) {
            die("Erro ao cadastrar o Horário: " . $e->getMessage());
        }
    }
    public function actualizarHorario(HorarioDTO $horario)
    {
        try {
            $sql = "UPDATE Horario SET 
                    idTurma = :idTurma, 
                    idDisciplina = :idDisciplina,
                    idCurso = :idCurso, 
                    diasSemanaHorario = :diasSemana, 
                    horaInicioHorario = :horaInicio, 
                    horaFimHorario = :horaFim
                WHERE idHorario = :id";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":idTurma", $horario->getTurmaId());
            $stmt->bindValue(":idDisciplina", $horario->getDisciplinaId());
            $stmt->bindValue(":idCurso", $horario->getIdCurso());
            $stmt->bindValue(":diasSemana", $horario->getDiaSemana());
            $stmt->bindValue(":horaInicio", $horario->getHoraInicio());
            $stmt->bindValue(":horaFim", $horario->getHoraFim());
            $stmt->bindValue(":id", $horario->getIdHorario());

            return $stmt->execute();
        } catch (Exception $e) {
            die("Erro ao actualizar o horário: " . $e->getMessage()) ;
            return false;
        }
    }
    public function listarTodosHorarios()
    {
        try {
            $sql = "SELECT h.*, d.nomeDisciplina, t.nomeTurma, t.salaTurma, c.nomeCurso
                FROM horario h
                INNER JOIN disciplina d ON h.idDisciplina = d.idDisciplina
                INNER JOIN turma t ON h.idTurma = t.idTurma
                INNER JOIN curso c ON t.idCurso = c.idCurso";  // Correção aqui

            $stmt = $this->conexao->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($registros as $linha) {
                $resultado[] = $this->mapearHorario($linha);
            }

            return $resultado;
        } catch (Exception $e) {
            error_log("Erro ao listar horários: " . $e->getMessage());
            return false;
        }
    }



    public function buscarHorarioPorId($id)
    {
        try {
            $sql = "SELECT * FROM Horario WHERE idHorario = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();

            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->mapearHorario($linha);
        } catch (Exception $e) {
            echo "Erro ao buscar horário: " . $e->getMessage();
            return false;
        }
    }


   public function apagarHorario(HorarioDTO $horario)
{
    try {
        $sql = "DELETE FROM horario WHERE idHorario = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id", $horario->getIdHorario());
        return $stmt->execute();
    } catch (Exception $e) {
        echo "Erro ao apagar horário: " . $e->getMessage();
        return false;
    }
}

    public function pesquisarHorarios($palavra)
    {
        $sql = "SELECT h.*, d.nomeDisciplina, t.nomeTurma, t.salaTurma, c.nomeCurso
            FROM horario h
            INNER JOIN disciplina d ON h.idDisciplina = d.idDisciplina
            INNER JOIN turma t ON h.idTurma = t.idTurma
            INNER JOIN curso c ON t.idCurso = c.idCurso
            WHERE 1=1";

        $params = [];

        if (!empty($palavra)) {
            $sql .= " AND (
            h.diasSemanaHorario LIKE ? OR 
            h.salaHorario LIKE ? OR 
            h.horaInicioHorario LIKE ? OR 
            h.horaFimHorario LIKE ? OR
            d.nomeDisciplina LIKE ? OR 
            t.nomeTurma LIKE ? OR
            c.nomeCurso LIKE ?
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
            $resultado[] = $this->mapearHorario($linha);
        }

        return $resultado;
    }


   private function mapearHorario($linha)
{
    $dto = new HorarioDTO();
    $dto->setId($linha['idHorario']);
    $dto->setTurmaId($linha['idTurma']);
    $dto->setDisciplinaId($linha['idDisciplina']);
    $dto->setDiaSemana($linha['diasSemanaHorario']);
    $dto->setHoraInicio($linha['horaInicioHorario']);
    $dto->setHoraFim($linha['horaFimHorario']);

    if (isset($linha['nomeDisciplina'])) $dto->setNomeDisciplina($linha['nomeDisciplina']);
    if (isset($linha['nomeTurma'])) $dto->setTurma($linha['nomeTurma']);
    if (isset($linha['salaTurma'])) $dto->setSalaTurma($linha['salaTurma']);
    if (isset($linha['nomeCurso'])) $dto->setNomeCurso($linha['nomeCurso']);
    if (isset($linha['idCurso'])) $dto->setIdCurso($linha['idCurso']);

    return $dto;
}

}
