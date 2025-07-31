<?php
class DisciplinaDTO
{
    private $idDisciplina;
    private $nomeDisciplina;
    private $classeDisciplina;
    private $idCurso;
    private $idProfessor;
    private $nomeProfessor;
    private $nomeCurso;

    // ID Disciplina
    public function getIdDisciplina()
    {
        return $this->idDisciplina;
    }
    public function setIdDisciplina($id)
    {
        $this->idDisciplina = $id;
    }

     // Classe Disciplina
    public function getclasseDisciplina()
    {
        return $this->classeDisciplina;
    }
    public function setclasseDisciplina($id)
    {
        $this->classeDisciplina = $id;
    }

    // Nome Curso
    public function getNomeCurso()
    {
        return $this->nomeCurso;
    }
    public function setNomeCurso($id)
    {
        $this->nomeCurso = $id;
    }

    // Nome Professor
    public function getNomeProfessor()
    {
        return $this->nomeProfessor;
    }
    public function setNomeProfessor($id)
    {
        $this->nomeProfessor = $id;
    }

    //Nome Disciplina
    public function getNomeDisciplina()
    {
        return $this->nomeDisciplina;
    }

    public function setNomeDisciplina($nomeDisciplina)
    {
        $this->nomeDisciplina = $nomeDisciplina;
    }


    //iD Curso
    public function getIdCurso()
    {
        return $this->idCurso;
    }

    public function setIdCurso($idCurso)
    {
        $this->idCurso = $idCurso;
    }
    //Id professor
    public function getIdProfessor()
    {
        return $this->idProfessor;
    }

    public function setIdProfessor($idProfessor)
    {
        $this->idProfessor = $idProfessor;
    }
}
