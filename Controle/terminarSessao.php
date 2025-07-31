<?php
session_start();

// Define a mensagem de sucesso antes de destruir a sessão
$_SESSION['success'] = "Sessão terminada com sucesso!";
$_SESSION['icon'] = "success";

// Copia os dados da mensagem para variáveis locais
$successMessage = $_SESSION['success'];
$successIcon = $_SESSION['icon'];

// Destrói a sessão
session_destroy();

// Usa cookies para preservar a mensagem após destruir a sessão
setcookie("success", $successMessage, time() + 10, "/");
setcookie("icon", $successIcon, time() + 10, "/");

// Redireciona para a página de destino
header("Location: ../Visao/index.php");
exit();
?>