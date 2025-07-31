<?php
class UtilizadorDTO {
    private $idUtilizador;
    private $nomeUtilizador;
    private $emailUtilizador;
    private $telefoneUtilizador;
    private $moradaUtilizador;
    private $perfilUtilizador;
    private $senhaUtilizador;
    private $idAcesso;
    private $nIdentificacao;
    private $generoUtilizador;
    private $dataNascimentoUtilizador;



    //Id Do Utilizador:
    public function getIdUtilizador() {
        return $this->idUtilizador;
    }
    public function setIdUtilizador($idUtilizador) {
        $this->idUtilizador = $idUtilizador;
    }
    //Nome Do Utilizador:
        public function getNomeUtilizador()
    {
        return $this->nomeUtilizador;
    }

    public function setNomeUtilizador($nome)
    {
        $this->nomeUtilizador = $nome;
    }

    // Email Do Utilizador:
        public function getEmailUtilizador()
    {
        return $this->emailUtilizador;
    }

    public function setEmailUtilizador($email)
    {
        $this->emailUtilizador = $email;
    }

    // Telefone Do Utilizador:
    public function getTelefoneUtilizador()
    {
        return $this->telefoneUtilizador;
    }

    public function setTelefoneUtilizador($telefone)
    {
        $this->telefoneUtilizador = $telefone;
    }

    // Morada Do Utilizador:
    public function getMoradaUtilizador()
    {
        return $this->moradaUtilizador;
    }
    public function setMoradaUtilizador($morada)
    {
        $this->moradaUtilizador = $morada;
    }

    // Perfil Do Utilizador:
    public function getPerfilUtilizador()
    {
        return $this->perfilUtilizador;
    }

    public function setPerfilUtilizador($perfil)
    {
        $this->perfilUtilizador = $perfil;
    }

    // Senha Do Utilizador:
    public function getSenhaUtilizador()
    {
        return $this->senhaUtilizador;
    }

    public function setSenhaUtilizador($senha)
    {
        $this->senhaUtilizador = $senha;
    }

    // Id Acesso
    public function getIdAcesso()
    {
        return $this->idAcesso;
    }

    public function setIdAcesso($idAcesso)
    {
        $this->idAcesso = $idAcesso;
    }

    //Número De Identificação:
    public function getNumIdentificacao()
    {
        return $this->nIdentificacao;
    }

    public function setNumIdentificacao($num)
    {
        $this->nIdentificacao = $num;
    }

    // Género Do Utilizador:
    public function getGeneroUtilizador() {
        return $this->generoUtilizador;
    }
    public function setGeneroUtilizador($generoUtilizador) {
        $this->generoUtilizador = $generoUtilizador;
    }

    //Data De Nascimento:
    public function getDataNascimentoUtilizador() {
        return $this->dataNascimentoUtilizador;
    }
    public function setDataNascimentoUtilizador($data) {
        $this->dataNascimentoUtilizador = $data;
    }




}

