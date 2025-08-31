<?php
session_start();
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/config/conn.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 1) {
    header("Location: login.php");
    exit;
}

$usuarioModel = new User();

// Validar que se haya pasado un ID
if (!isset($_GET['id'])) {
    die("ID de usuario no especificado.");
}

$id = intval($_GET['id']);
$usuario = $usuarioModel->getUserById($id);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Si envían el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $id_rol = $_POST['id_rol'];

    $usuarioModel->updateUser($id, $nombre, $email, $id_rol);

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>
<body>
    <h2>Editar Usuario</h2>

    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        <br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        <br>

        <label>Rol:</label>
        <select name="id_rol" required>
            <option value="1" <?php if ($usuario['id_rol'] == 1) echo 'selected'; ?>>Administrador</option>
            <option value="2" <?php if ($usuario['id_rol'] == 2) echo 'selected'; ?>>Manager</option>
            <option value="3" <?php if ($usuario['id_rol'] == 3) echo 'selected'; ?>>Developer</option>
            <option value="4" <?php if ($usuario['id_rol'] == 4) echo 'selected'; ?>>Tester</option>
        </select>
        <br>

        <button type="submit">Guardar Cambios</button>
    </form>

    <a href="dashboard.php">⬅ Volver al Dashboard</a>
</body>
</html>
