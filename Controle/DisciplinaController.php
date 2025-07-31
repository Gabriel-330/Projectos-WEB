<?php
session_start(); // Inicia a sessão

require_once("../Modelo/DAO/conn.php");
require_once ('../Modelo/DAO/DisciplianDAO.php');
require_once ('../Modelo/DTO/DisciplinaDTO.php');


if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["cadastrar"])) {
    $disciplina = new DisciplinaDTO();
    $nome=$_POST['nome'];
    $id_curso=$_POST['cursoId'];
    $disciplina->setNomeDisciplina($nome);
    $disciplina->setIdCurso($id_curso);
    $dao=new DisciplinaDAO();
    $dao->cadastrar($disciplina);
    header("Location: ../Visao/discplinaBase.php");
    exit();
}