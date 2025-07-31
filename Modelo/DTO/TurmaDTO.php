<?php
class TurmaDTO
{
    private $idTurma;
    private $nomeTurma;
    private $salaTurma;
    private $idCurso;
    private $nomeCurso;
    private $idProfessor;
    private $idAluno;
    private $nomeProfessor;

    // Id Turma
    public function getIdTurma()
    {
        return $this->idTurma;
    }
    public function setIdTurma($idTurma)
    {
        $this->idTurma = $idTurma;
    }

    // nome da Turma

    public function getNomeTurma()
    {
        return $this->nomeTurma;
    }
    public function setNomeTurma($nomeTurma)
    {
        $this->nomeTurma = $nomeTurma;
    }
    
    // Sala Turma
    public function getSalaTurma()
    {
        return $this->salaTurma;
    }
    public function setSalaTurma($salaTurma)
    {
        $this->salaTurma = $salaTurma;
    }

    // Id Curso
    public function setIdCurso($idCurso)
    {
        $this->idCurso = $idCurso;
    }

    public function getIdCurso()
    {
        return $this->idCurso;
    }


    //Id Professor//Id Professor
    public function getIdProfessor()
    {
        return $this->idProfessor;
    }
    // Setters
    public function setIdProfessor($idProfessor)
    {
        $this->idProfessor = $idProfessor;
    }
    //Id Aluno
    public function getIdAluno()
    {
        return $this->idAluno;
    }
    public function setIdAluno($idAluno)
    {
        $this->idAluno = $idAluno;
    }

    // Nome Curso
    public function setNomeCurso($nomeCurso)
    {
        $this->nomeCurso = $nomeCurso;
    }
    public function getNomeCurso()
    {
        return $this->nomeCurso;
    }

    // Nome Professor
    public function setNomeProfessor($nomeProfessor)
    {
        $this->nomeProfessor = $nomeProfessor;
    }
    public function getNomeProfessor()
    {
        return $this->nomeProfessor;
    }
}
