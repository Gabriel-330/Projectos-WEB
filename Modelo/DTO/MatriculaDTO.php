<?php
class MatriculaDTO
{
    private $idMatricula;
    private $idAluno;
    private $idTurma;
    private $idCurso;
    private $dataMatricula;
    private $estadoMatricula;
    private $nomeAluno;
    private $nomeTurma;
    private $nomeCurso;
    private $classeMatricula;
    private $periodoMatricula;

    // ID
    public function getIdMatricula()
    {
        return $this->idMatricula;
    }
    public function setIdMatricula($id)
    {
        $this->idMatricula = $id;
    }

    // ID Aluno
    public function getIdAluno()
    {
        return $this->idAluno;
    }
    public function setIdAluno($idAluno)
    {
        $this->idAluno = $idAluno;
    }

    // ID Turma
    public function getIdTurma()
    {
        return $this->idTurma;
    }
    public function setIdTurma($idTurma)
    {
        $this->idTurma = $idTurma;
    }

      // ID Curso
    public function getIdCurso()
    {
        return $this->idCurso;
    }
    public function setIdCurso($idCurso)
    {
        $this->idCurso = $idCurso;
    }

    // Data da Matrícula
    public function getDataMatricula()
    {
        return $this->dataMatricula;
    }
    public function setDataMatricula($dataMatricula)
    {
        $this->dataMatricula = $dataMatricula;
    }

    // Estado da Matrícula
    public function getEstadoMatricula()
    {
        return $this->estadoMatricula;
    }
    public function setEstadoMatricula($estadoMatricula)
    {
        $this->estadoMatricula = $estadoMatricula;
    }

    // Nome do Aluno
    public function getNomeAluno()
    {
        return $this->nomeAluno;
    }
    public function setNomeAluno($nomeAluno)
    {
        $this->nomeAluno = $nomeAluno;
    }

    // Nome do Turma
    public function getNomeTurma()
    {
        return $this->nomeTurma;
    }
    public function setNomeTurma($nomeTurma)
    {
        $this->nomeTurma = $nomeTurma;
    }
     // Nome do Curso
    public function getNomeCurso()
    {
        return $this->nomeCurso;
    }
    public function setNomeCurso($nomeCurso)
    {
        $this->nomeCurso = $nomeCurso;
    }

       // Classe Matricula
    public function getClasseMatricula()
    {
        return $this->classeMatricula;
    }
    public function setClasseMatricula($classeMatricula)
    {
        $this->classeMatricula = $classeMatricula;
    }

       // Periodo Matricula
    public function getPeriodoMatricula()
    {
        return $this->periodoMatricula;
    }
    public function setPeriodoMatricula($periodoMatricula)
    {
        $this->periodoMatricula = $periodoMatricula;
    }
}
