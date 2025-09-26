<?php
session_start();
require_once __DIR__ . "/models/Transaccion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$id_plan = intval($_POST['id_plan']);

// Crear referencia única (simulación)
$referencia = uniqid("TXN_");

$transaccion = new Transaccion();
$transaccion->registrarPago($id_usuario, $id_plan, $referencia);

// Aquí podrías hacer que primero esté "Pendiente"
// y luego con otra acción confirmes "Completado"

header("Location: dashboard.php?msg=Pago simulado correctamente");
