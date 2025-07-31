<?php
class CursoDTO {
    private $idCurso;
    private $nomeCurso;

    
//Id  Curso
    public function getIdCurso() { return $this->idCurso; }
    public function setidCurso($idCurso) { $this->idCurso = $idCurso; }

    //Nome do Curso
    public function getNomeCurso() { return $this->nomeCurso; }
    public function setNomeCurso($nomeCurso) { $this->nomeCurso = $nomeCurso; }

}
