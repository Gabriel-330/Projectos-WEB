<?php
session_start();
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/UtilizadorDAO.php");
require_once("../Modelo/DTO/UtilizadorDTO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
?>