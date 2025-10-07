<?php
session_start();
require_once __DIR__.'/models/Proyecto.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];
$proyectoModel = new Proyecto();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_proyecto'])) {
    $proyectoModel->unirseAProyecto($user['id_usuario'], $_POST['id_proyecto']);
    header("Location: dashboard.php?msg=Te has unido al proyecto correctamente");
    exit;
}

header("Location: dashboard.php?msg=Error al unirse al proyecto");
exit;
