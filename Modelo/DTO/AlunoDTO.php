<?php
class AlunoDTO
{
    private $idAluno;
    private $nomeAluno;
    private $nIdentificacao;
    private $moradaAluno;
    private $dataNascimentoAluno;
    private $generoAluno;
    private $fotoAluno;
    private $responsavelAluno;
    private $contactoResponsavelAluno;
    private $anoIngressoAluno;
    private $idUtilizador;
    private $idCurso;
    private $idTurma;
    private $nomeCurso;
    private $nomeUtilizador;
    private $nomeTurma;


    //Id Aluno
    public function getIdAluno()
    {
        return $this->idAluno;
    }

    public function setIdAluno($idAluno)
    {
        $this->idAluno = $idAluno;
    }

    //Nome do Aluno
    public function getNomeAluno()
    {
        return $this->nomeAluno;
    }

    public function setNomeAluno($nomeAluno)
    {
        $this->nomeAluno = $nomeAluno;
    }

    //Indentificacao do Aluno
    public function getnIdentificacao()
    {
        return $this->nIdentificacao;
    }

    public function setnIdentificacao($nIdentificacao)
    {
        $this->nIdentificacao = $nIdentificacao;
    }
    //Morada do Aluno
    public function getMoradaAluno()
    {
        return $this->moradaAluno;
    }

    public function setMoradaAluno($moradaAluno)
    {
        $this->moradaAluno    = $moradaAluno;
    }

    //Data de Nascimento do Aluno
    public function getDataNascimentoAluno()
    {
        return $this->dataNascimentoAluno;
    }

    public function setDataNascimentoAluno($dataNascimentoAluno)
    {
        $this->dataNascimentoAluno = $dataNascimentoAluno;
    }


    // Gênero aluno
    public function getGeneroAluno()
    {
        return $this->generoAluno;
    }
    public function setGeneroAluno($generoAluno)
    {
        $this->generoAluno = $generoAluno;
    }


    // Foto do aluno
    public function getFotoAluno()
    {
        return $this->fotoAluno;
    }
    public function setFotoAluno($fotoAluno)
    {
        $this->fotoAluno = $fotoAluno;
    }
    // Responsável 
    public function getResponsavelAluno()
    {
        return $this->responsavelAluno;
    }
    public function setResponsavelAluno($responsavelAluno)
    {
        $this->responsavelAluno = $responsavelAluno;
    }


    //Contacto do responsável do  aluno
    public function getContactoResponsavelAluno()
    {
        return $this->contactoResponsavelAluno;
    }
    public function setContatoResponsavelAluno($contactoResponsavelAluno)
    {
        $this->contactoResponsavelAluno = $contactoResponsavelAluno;
    }


    // Ano de ingresso
    public function getAnoIngressoAluno()
    {
        return $this->anoIngressoAluno;
    }
    public function setAnoIngressoAluno($anoIngressoAluno)
    {
        $this->anoIngressoAluno = $anoIngressoAluno;
    }


    //Id Utilizador
    public function getIdUtilizador()
    {
        return $this->idUtilizador;
    }
    public function setIdUtilizador($idUtilizador)
    {
        $this->idUtilizador = $idUtilizador;
    }


    // ID do curso
    public function getIdCurso()
    {
        return $this->idCurso;
    }
    public function setIdCurso($idCurso)
    {
        $this->idCurso = $idCurso;
    }

    // Nome Curso
    public function getNomeCurso()
    {
        return $this->nomeCurso;
    }
    public function setNomeCurso($nomeCurso)
    {
        $this->nomeCurso = $nomeCurso;
    }

    // ID do curso
    public function getNomeUtilizador()
    {
        return $this->nomeUtilizador;
    }
    public function setNomeUtilizador($nomeUtilizador)
    {
        $this->nomeUtilizador = $nomeUtilizador;
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

    // Nome Turma
    public function getNomeTurma()
    {
        return $this->nomeTurma;
    }
    public function setNomeTurma($nomeTurma)
    {
        $this->nomeTurma = $nomeTurma;
    }
}
