<?php
class HorarioDTO
{
    private $idHorario;
    private $turma_id;
    private $disciplina_id;
    private $dia_semana;
    private $hora_inicio;
    private $hora_fim;
    private $turma;
    private $nomedisciplina;
    private $idCurso;
    private $nomeCurso;
    private $salaTurma;

    // Nome Da Disciplina:
    public function getNomeDisciplina()
    {
        return $this->nomedisciplina;
    }
    public function setNomeDisciplina($disciplina)
    {
        $this->nomedisciplina = $disciplina;
    }

    // Nome Da Turma:
    public function getTurma()
    {
        return $this->turma;
    }
    public function setTurma($turma)
    {
        $this->turma = $turma;
    }

    // Id Do Horário:
    public function getIdHorario()
    {
        return $this->idHorario;
    }
    public function setId($id)
    {
        $this->idHorario = $id;
    }

    // Id Da Turma:
    public function getTurmaId()
    {
        return $this->turma_id;
    }
    public function setTurmaId($turma_id)
    {
        $this->turma_id = $turma_id;
    }

    // Id Da Disciplina:
    public function getDisciplinaId()
    {
        return $this->disciplina_id;
    }
    public function setDisciplinaId($disciplina_id)
    {
        $this->disciplina_id = $disciplina_id;
    }

    // Dias De Semana:
    public function getDiaSemana()
    {
        return $this->dia_semana;
    }
    public function setDiaSemana($dia_semana)
    {
        $this->dia_semana = $dia_semana;
    }

    // Hora Do Ínicio:
    public function getHoraInicio()
    {
        return $this->hora_inicio;
    }
    public function setHoraInicio($hora_inicio)
    {
        $this->hora_inicio = $hora_inicio;
    }

    // Hora Do Fim:
    public function getHoraFim()
    {
        return $this->hora_fim;
    }
    public function setHoraFim($hora_fim)
    {
        $this->hora_fim = $hora_fim;
    }

    // Id Do Curso:
    public function getIdCurso()
    {
        return $this->idCurso;
    }

    public function setIdCurso($idCurso)
    {
        $this->idCurso = $idCurso;
    }

    // Nome Do Curso:
    public function getNomeCurso()
    {
        return $this->nomeCurso;
    }

    public function setNomeCurso($nomeCurso)
    {
        $this->nomeCurso = $nomeCurso;
    }

    // Sala Da Turma:
    public function getSalaTurma()
    {
        return $this->salaTurma;
    }

    public function setSalaTurma($salaTurma)
    {
        $this->salaTurma = $salaTurma;
    }
}
