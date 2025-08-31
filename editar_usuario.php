<?php
session_start();
require_once __DIR__ . '/models/User.php';

$usuarioModel = new User();

// Verificamos que haya sesión y que el usuario sea admin
if (!isset($_SESSION['usuario']) || !$usuarioModel->esAdmin((int)$_SESSION['usuario']['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Validar que se haya pasado un ID
if (!isset($_GET['id'])) {
    die("ID de usuario no especificado.");
}

$id = intval($_GET['id']);
$usuario = $usuarioModel->getUserById($id);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Roles disponibles y roles actuales del usuario
$roles = $usuarioModel->obtenerRoles();
$currentRoles = $usuarioModel->obtenerRolesPorUsuario($id);
$currentRoleId = null;
if (!empty($currentRoles)) {
    $currentRoleId = (int)$currentRoles[0]['id_rol']; // asumimos rol único en la UI
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_rol = isset($_POST['id_rol']) && $_POST['id_rol'] !== '' ? intval($_POST['id_rol']) : null;

    if ($nombre === '' || $correo === '') {
        $error = "Nombre y correo son obligatorios.";
    } else {
        $ok1 = $usuarioModel->updateUsuario($id, $nombre, $correo, $password !== '' ? $password : null);
        $ok2 = $usuarioModel->setUserRole($id, $id_rol);

        if ($ok1 && $ok2) {
            $message = "Usuario actualizado correctamente.";
            // Actualizar datos en la página
            $usuario = $usuarioModel->getUserById($id);
            $currentRoles = $usuarioModel->obtenerRolesPorUsuario($id);
            $currentRoleId = !empty($currentRoles) ? (int)$currentRoles[0]['id_rol'] : null;
        } else {
            $error = "Ocurrió un error al actualizar. Revisa logs.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
        label { display:block; margin-top:8px; }
        input, select { padding:6px; width:300px; }
        .msg-success { color: green; }
        .msg-error { color: red; }
    </style>
</head>
<body>
    <h2>Editar Usuario (ID <?php echo $usuario['id_usuario']; ?>)</h2>

    <?php if ($message): ?>
        <p class="msg-success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

        <label>Correo:</label>
        <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>

        <label>Cambiar contraseña (opcional):</label>
        <input type="password" name="password" placeholder="Dejar vacío para mantener la contraseña">

        <label>Rol:</label>
        <select name="id_rol">
            <option value="">-- Sin rol --</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?php echo $r['id_rol']; ?>" <?php if ($currentRoleId !== null && $currentRoleId == $r['id_rol']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($r['nombre_rol']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <button type="submit">Guardar Cambios</button>
    </form>

    <p><a href="dashboard.php">⬅ Volver al Dashboard</a></p>
</body>
</html>
