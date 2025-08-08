<?php
class DocumentoDTO{

private $idDocumento;
private $tipoDocumento;
private $estadoDocumento;
private $dataEmissaoDocumento;
private $caminhoArquivoDocumento;
private $aluno_idAluno;
private $idCurso;
private $idNota;
private $idDisciplina;
private $idTurma;
private $professor_idProfessor;
private $cursoDocumento;
private $turmaDocumento;
private $classeDocumento;
private $periodoDocumento;
private $disciplinaDocumento;

public function setCursoDocumento($cursoDocumento){
$this->cursoDocumento = $cursoDocumento;
}
public function getCursoDocumento(){
    return $this->cursoDocumento;
}
public function setTurmaDocumento($turmaDocumento){
$this->turmaDocumento=$turmaDocumento;
}
public function getTurmaDocumento(){
    return $this->turmaDocumento;
}
public function setClasseDocumento($classeDocumento){
$this->classeDocumento=$classeDocumento;
}
public function getClasseDocumento(){
    return $this->classeDocumento;
}
public function setPeriodoDocumento($periodoDocumento){
$this->periodoDocumento=$periodoDocumento;
}
public function getPeriodoDocumento(){
    return $this->periodoDocumento;
}
public function setDisciplinaDocumento($disciplinaDocumento){
$this->disciplinaDocumento=$disciplinaDocumento;
}
public function getDisciplinaDocumento(){
    return $this->disciplinaDocumento;
}
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
//Estado do documento
public function setEstadoDocumento($estadoDocumento){
$this->estadoDocumento = $estadoDocumento;
}
public function getEstadoDocumento(){
    return $this->estadoDocumento;
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
    return $this->aluno_idAluno;
}

public function setIdAluno($aluno_idAluno) {
    $this->aluno_idAluno = $aluno_idAluno;
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
    return $this->professor_idProfessor;
}

public function setIdProfessor($professor_idProfessor) {
    $this->professor_idProfessor = $professor_idProfessor;
}

}
?>