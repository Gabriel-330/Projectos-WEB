<?php
class acessoDTO{
   private $idAcesso;
   private $acesso;
   
   //Id Acesso
   public function setIdAcesso($idAcesso)
    {
        $this->idAcesso = $idAcesso;
    }


    public function getIdAcesso()
    {
        return $this->idAcesso;
    }

    //Acesso
    public function setAcesso($acesso)
    {
        $this->acesso = $acesso;
    }


    public function getAcesso()
    {
        return $this->acesso;
    }
}
?>