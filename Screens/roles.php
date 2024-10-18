<?php
session_start(); // Inicia la sesión

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no hay información de sesión, redirigir al login
    header('Location: ../index.php'); // O donde desees redirigir
    exit();
}

// Obtener información del usuario desde la sesión
$usuario = $_SESSION['usuario'];

// Redirigir a diferentes páginas según el id_rol
if ($usuario['id_rol'] == 1) {
    header('Location: Super_Administrador/Super_admin.php');
    exit();
} elseif ($usuario['id_rol'] == 2) {
    header('Location: Trabajador/Trabajador.php');
    exit();
} elseif ($usuario['id_rol'] == 3) {
    header('Location: Cliente/Cliente.php');
    exit();
} elseif ($usuario['id_rol'] == 4) {
    header('Location: Invitado/Invitado.php');
    exit();
} else {
    echo 'Rol no identificado.';
    exit();
}
