<?php

class EventosDTO {
    private $idEvento;
    private $tituloEvento;
    private $dataEvento;
    private $horaInicioEvento;
    private $horaFimEvento;
    private $localEvento;
    private $responsavelEvento;
    private $tipoEvento;
    private $idCurso;
    private $idUtilizador;

    // Id Do Utilizador:
    public function getIdUtilizador() {
        return $this->idUtilizador;
    }

    public function setIdUtilizador($id) {
        $this->idUtilizador = $id;
    }

    // Id Do Evento:
    public function getIdEvento() {
        return $this->idEvento;
    }

    public function setIdEvento($id) {
        $this->idEvento = $id;
    }

    // Título do Evento:
    public function getTituloEvento() {
        return $this->tituloEvento;
    }

    public function setTituloEvento($tituloEvento) {
        $this->tituloEvento = $tituloEvento;
    }

    // Data do Evento:
    public function getDataEvento() {
        return $this->dataEvento;
    }

    public function setDataEvento($dataEvento) {
        $this->dataEvento = $dataEvento;
    }

    // Hora de Início:
    public function getHoraInicioEvento() {
        return $this->horaInicioEvento;
    }

    public function setHoraInicioEvento($horaInicioEvento) {
        $this->horaInicioEvento = $horaInicioEvento;
    }

    // Hora de Fim:
    public function getHoraFimEvento() {
        return $this->horaFimEvento;
    }

    public function setHoraFimEvento($horaFimEvento) {
        $this->horaFimEvento = $horaFimEvento;
    }

    // Local do Evento:
    public function getLocalEvento() {
        return $this->localEvento;
    }

    public function setLocalEvento($localEvento) {
        $this->localEvento = $localEvento;
    }

    // Responsável:
    public function getResponsavelEvento() {
        return $this->responsavelEvento;
    }

    public function setResponsavelEvento($responsavelEvento) {
        $this->responsavelEvento = $responsavelEvento;
    }

    // Tipo de Evento:
    public function getTipoEvento() {
        return $this->tipoEvento;
    }

    public function setTipoEvento($tipoEvento) {
        $this->tipoEvento = $tipoEvento;
    }

    // ID do Curso:
    public function getIdCurso() {
        return $this->idCurso;
    }

    public function setIdCurso($idCurso) {
        $this->idCurso = $idCurso;
    }
}
