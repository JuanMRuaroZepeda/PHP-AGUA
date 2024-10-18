<?php
session_start(); // Inicia la sesión

// Destruir todas las variables de sesión
$_SESSION = [];

// Si se desea destruir la sesión por completo
session_destroy();

// Redirigir al login después de cerrar sesión
header('Location: index.php');
exit();
?>