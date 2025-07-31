<?php
class ProfessorDTO
{
    private $idProfessor;
    private $nomeProfessor;
    private $emailProfessor;
    private $moradaProfessor;
    private $dataDeNascimentoProfessor;
    private $dataContProfessor;
    private $tipoContratoProfessor;
    private $generoProfessor;
    private $idUtilizador;
    private $nIdentificacao;
    private $contactoProfessor;
    private $nomeUtilizador;
    
    //Nome Do Utilizador:
    public function getnomeUtilizador()
    {
        return $this->nomeUtilizador;
    }
    public function setnomeUtilizador($nomeUtilizador)
    {
        $this->nomeUtilizador = $nomeUtilizador;
    }

    // Número De Identificação:
    public function getnIdentificacao()
    {
        return $this->nIdentificacao;
    }
    public function setnIdentificacao($nIdentificacao)
    {
        $this->nIdentificacao = $nIdentificacao;
    }

    //ID Do Professor:
    public function getIdProfessor()
    {
        return $this->idProfessor;
    }
    public function setIdProfessor($id)
    {
        $this->idProfessor = $id;
    }

    //ID Do Utilizador:
    public function getIdUtilizador()
    {
        return $this->idUtilizador;
    }
    public function setIdUtilizador($id)
    {
        $this->idUtilizador = $id;
    }

    // Nome do professor
    public function getNomeProfessor()
    {
        return $this->nomeProfessor;
    }
    public function setNomeProfessor($nomeProfessor)
    {
        $this->nomeProfessor = $nomeProfessor;
    }

    // Genero Do Professor:
    public function getGeneroProfessor()
    {
        return $this->generoProfessor;
    }
    public function setGeneroProfessor($generoProfessor)
    {
        $this->generoProfessor = $generoProfessor;
    }

    // E-mail Do Professor:
    public function getEmailProfessor()
    {
        return $this->emailProfessor;
    }
    public function setEmailProfessor($emailProfessor)
    {
        $this->emailProfessor = $emailProfessor;
    }

    // Contacto Do Professor:
    public function getContactoProfessor()
    {
        return $this->contactoProfessor;
    }
    public function setContactoProfessor($contactoProfessor)
    {
        $this->contactoProfessor = $contactoProfessor;
    }

    // Morada Do Professor:
    public function getMoradaProfessor()
    {
        return $this->moradaProfessor;
    }
    public function setMoradaProfessor($moradaProfessor)
    {
        $this->moradaProfessor = $moradaProfessor;
    }

    // Data De Nascimento:
    public function getDataDeNascimentoProfessor()
    {
        return $this->dataDeNascimentoProfessor;
    }
    public function setDataDeNascimentoProfessor($dataDeNascimentoProfessor)
    {
        $this->dataDeNascimentoProfessor = $dataDeNascimentoProfessor;
    }

    // Data Do Contrato:
    public function getDataContProfessor()
    {
        return $this->dataContProfessor;
    }
    public function setDataContProfessor($dataContProfessor)
    {
        $this->dataContProfessor = $dataContProfessor;
    }

    // Tipo De Contrato:
    public function getTipoContratoProfessor()
    {
        return $this->tipoContratoProfessor;
    }
    public function setTipoContratoProfessor($tipoContratoProfessor)
    {
        $this->tipoContratoProfessor = $tipoContratoProfessor;
    }
}
