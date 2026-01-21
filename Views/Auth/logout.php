<?php
// Este archivo solo sirve para llamar al método de cerrar sesión
require_once '../../Controllers/Auth/AuthController.php';

$auth = new AuthController();
$auth->logout();
?>