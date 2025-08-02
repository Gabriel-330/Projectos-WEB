<?php
class NotaDTO
{
    // === Atributos ===
    private $idNota;
    private $idAluno;
    private $idDisciplina;
    private $idCurso;
    private $idProfessor;
    private $valorNota;
    private $dataValorNota;
    private $tipoAvaliacaoNota;
    private $trimestreNota;
    private $tipoNota;
    private $nomeDisciplina;
    private $nomeCurso;
    private $nomeAluno;
    private $dataNascimentoAluno;
    private $responsavelAluno;

    // ID da Nota
    public function getIdNota()
    {
        return $this->idNota;
    }

    public function setIdNota($idNota)
    {
        $this->idNota = $idNota;
    }

    // ID do Aluno
    public function getIdAluno()
    {
        return $this->idAluno;
    }

    public function setIdAluno($idAluno)
    {
        $this->idAluno = $idAluno;
    }

    // ID da Disciplina
    public function getIdDisciplina()
    {
        return $this->idDisciplina;
    }

    public function setIdDisciplina($idDisciplina)
    {
        $this->idDisciplina = $idDisciplina;
    }

    // ID do Curso
    public function getIdCurso()
    {
        return $this->idCurso;
    }

    public function setIdCurso($idCurso)
    {
        $this->idCurso = $idCurso;
    }

      // ID Professor
    public function getIdProfessor()
    {
        return $this->idProfessor;
    }

    public function setIdProfessor($idProfessor)
    {
        $this->idProfessor = $idProfessor;
    }

    // Valor da Nota
    public function getValorNota()
    {
        return $this->valorNota;
    }

    public function setValorNota($valorNota)
    {
        $this->valorNota = $valorNota;
    }

    // Data da Avaliação
    public function getDataValorNota()
    {
        return $this->dataValorNota;
    }

    public function setDataValorNota($dataValorNota)
    {
        $this->dataValorNota = $dataValorNota;
    }

    // Tipo de Avaliação (Ex: Prova, Trabalho, etc.)
    public function getTipoAvaliacaoNota()
    {
        return $this->tipoAvaliacaoNota;
    }

    public function setTipoAvaliacaoNota($tipoAvaliacaoNota)
    {
        $this->tipoAvaliacaoNota = $tipoAvaliacaoNota;
    }

    // Trimestre da Nota
    public function getTrimestreNota()
    {
        return $this->trimestreNota;
    }

    public function setTrimestreNota($trimestreNota)
    {
        $this->trimestreNota = $trimestreNota;
    }

    // Tipo da Nota (Ex: MAC, NP1, NP2)
    public function getTipoNota()
    {
        return $this->tipoNota;
    }

    public function setTipoNota($tipoNota)
    {
        $this->tipoNota = $tipoNota;
    }

    // Nome da Disciplina
    public function getNomeDisciplina()
    {
        return $this->nomeDisciplina;
    }

    public function setNomeDisciplina($nomeDisciplina)
    {
        $this->nomeDisciplina = $nomeDisciplina;
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

    // Nome do Aluno
    public function getNomeAluno()
    {
        return $this->nomeAluno;
    }

    public function setNomeAluno($nomeAluno)
    {
        $this->nomeAluno = $nomeAluno;
    }

    // Data de Nascimento do Aluno
    public function getDataNascimentoAluno()
    {
        return $this->dataNascimentoAluno;
    }

    public function setDataNascimentoAluno($dataNascimentoAluno)
    {
        $this->dataNascimentoAluno = $dataNascimentoAluno;
    }

    // Responsável pelo Aluno
    public function getResponsavelAluno()
    {
        return $this->responsavelAluno;
    }

    public function setResponsavelAluno($responsavelAluno)
    {
        $this->responsavelAluno = $responsavelAluno;
    }
}
