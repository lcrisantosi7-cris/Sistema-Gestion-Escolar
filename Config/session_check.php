<?php
// CORRECCIÓN: Evitar el error "Ignoring session_start"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no está logueado, mandar al login
if (!isset($_SESSION['user_id'])) {
    // Calculamos ruta relativa para no fallar en subcarpetas
    $ruta = "../../Views/Auth/login.php";
    header("Location: " . $ruta);
    exit();
}
?>