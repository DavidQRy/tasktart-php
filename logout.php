<?php
require_once __DIR__.'/controllers/AuthController.php';
session_destroy();
$auth = new AuthController();
$auth->logout();

// Redirigir al login
header("Location: login.php");
exit;
?>
