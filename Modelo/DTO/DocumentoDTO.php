<?php
class DocumentoDTO{

private $idDocumento;
private $tipoDocumento;
private $dataEmissaoDocumento;
private $caminhoArquivoDocumento;
private $idAluno;
private $idCurso;
private $idNota;
private $idDisciplina;
private $idTurma;
private $idProfessor;

// Id Do Documento:
public function getIdDocumento() {
    return $this->idDocumento;
}

public function setIdDocumento($id) {
    $this->idDocumento = $id;
}

// Tipo De Documento:
public function getTipoDocumento() {
    return $this->tipoDocumento;
}

public function setTipoDocumento($tipoDocumento) {
    $this->tipoDocumento = $tipoDocumento;
}

// Data Da Emissão Do Documento:
public function getDataEmissaoDocumento() {
    return $this->dataEmissaoDocumento;
}

public function setDataEmissaoDocumento($dataEmissaoDocumento) {
    $this->dataEmissaoDocumento = $dataEmissaoDocumento;
}

// Caminho Do Arquivo Do Documento:
public function getCaminhoArquivoDocumento() {
    return $this->caminhoArquivoDocumento;
}

public function setCaminhoArquivoDocumento($caminhoArquivoDocumento) {
    $this->caminhoArquivoDocumento = $caminhoArquivoDocumento;
}

// Id Do Aluno:
public function getIdAluno() {
    return $this->idAluno;
}

public function setIdAluno($idAluno) {
    $this->idAluno = $idAluno;
}

// Id Do Curso:
public function getIdCurso() {
    return $this->idCurso;
}

public function setIdCurso($idCurso) {
    $this->idCurso = $idCurso;
}

// Id Da Nota:
public function getIdNota() {
    return $this->idNota;
}

public function setIdNota($idNota) {
    $this->idNota = $idNota;
}

// Id Da Disciplina:
public function getIdDisciplina() {
    return $this->idDisciplina;
}

public function setIdDisciplina($idDisciplina) {
    $this->idDisciplina = $idDisciplina;
}

// Id Da Turma:
public function getIdTurma() {
    return $this->idTurma;
}

public function setIdTurma($idTurma) {
    $this->idTurma = $idTurma;
}

// Id Do Professor:
public function getIdProfessor() {
    return $this->idProfessor;
}

public function setIdProfessor($idProfessor) {
    $this->idProfessor = $idProfessor;
}

}
?>