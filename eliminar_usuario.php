<?php 
session_start();
require_once __DIR__ . "/config/conn.php"; 
require_once __DIR__. "/models/User.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];
$usuarioModel = new User();
$esAdmin = $usuarioModel->esAdmin($user['id_usuario']);

if (!$esAdmin) {
    header("Location: dashboard.php?msg=No tienes permisos para realizar esta acción.");
    exit;
}

$db = new DataBase();
$conn = $db->connect();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id === (int)$user['id_usuario']) {
        header("Location: dashboard.php?msg=❌ No puedes eliminar tu propio usuario.");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=✅ Usuario eliminado correctamente.");
    } else {
        header("Location: dashboard.php?msg=❌ Error al eliminar usuario.");
    }

    $stmt->close();
} else {
    header("Location: dashboard.php?msg=ID de usuario no especificado.");
}

$conn->close();
