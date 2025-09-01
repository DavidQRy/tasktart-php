<?php 
session_start();
require_once __DIR__ . "/config/conn.php"; // conexión
require_once __DIR__. "/models/User.php";

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];
$usuarioModel = new User();
$esAdmin = $usuarioModel->esAdmin($user['id_usuario']);
// Verificar que el que accede sea administrador
if (!$esAdmin) {
    echo "No tienes permisos para realizar esta acción.";
    var_dump($_SESSION);
    exit;
}

// Conexión
$db = new DataBase();
$conn = $db->connect();

// Verificar si llega el id del usuario a eliminar
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prevenir que el admin se elimine a sí mismo
    if ($id === (int)$user['id_usuario']) {
        echo "No puedes eliminar tu propio usuario.";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Usuario eliminado correctamente");
        exit;
    } else {
        echo "Error al eliminar usuario: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "ID de usuario no especificado.";
}

$conn->close();
