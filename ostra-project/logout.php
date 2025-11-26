<?php
session_start();

// Destrói todas as variáveis de sessão
session_unset();

// Destrói a sessão
session_destroy();

// Redireciona para a página inicial (ou login)
header("Location: index.php");
exit();
?>
