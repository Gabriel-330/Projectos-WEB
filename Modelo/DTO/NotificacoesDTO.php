<?php

class NotificacoesDTO {
    private $idNotificacoes;
    private $tipoNotificacoes;
    private $mensagemNotificacoes;
    private $dataNotificacoes;
    private $lidaNotificacoes;
    private $idUtilizador;

    // Id Das Notificações:
    public function getIdNotificacoes() {
        return $this->idNotificacoes;
    }

    public function setIdNotificacoes($idNotificacoes) {
        $this->idNotificacoes = $idNotificacoes;
    }

    // Tipo De Noticações:
    public function getTipoNotificacoes() {
        return $this->tipoNotificacoes;
    }

    public function setTipoNotificacoes($tipoNotificacoes) {
        $this->tipoNotificacoes = $tipoNotificacoes;
    }

    // Mensagem e Notificações:
    public function getMensagemNotificacoes() {
        return $this->mensagemNotificacoes;
    }

    public function setMensagemNotificacoes($mensagemNotificacoes) {
        $this->mensagemNotificacoes = $mensagemNotificacoes;
    }

    // Data Das Notificações:
    public function getDataNotificacoes() {
        return $this->dataNotificacoes;
    }

    public function setDataNotificacoes($dataNotificacoes) {
        $this->dataNotificacoes = $dataNotificacoes;
    }

    // Ler Notificações:
    public function getLidaNotificacoes() {
        return $this->lidaNotificacoes;
    }

    public function setLidaNotificacoes($lidaNotificacoes) {
        $this->lidaNotificacoes = $lidaNotificacoes;
    }

    // Id Do Utilizador
    public function getIdUtilizador() {
        return $this->idUtilizador;
    }

    public function setIdUtilizador($idUtilizador) {
        $this->idUtilizador = $idUtilizador;
    }
}
