<?php
session_start();
require_once __DIR__.'/models/User.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];

// Verificar si el usuario es administrador
$usuarioModel = new User();
$esAdmin = $usuarioModel->esAdmin($user['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - TaskManager</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f4f4f4; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <h2>Bienvenido <?php echo htmlspecialchars($user['nombre']); ?> 👋</h2>
    <p>Has iniciado sesión correctamente.</p>

    <a href="logout.php">Cerrar sesión</a>

    <?php if ($esAdmin): ?>
        <h3>📋 Lista de Usuarios</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Fecha Registro</th>
                    <th>Rol(es)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $usuarios = $usuarioModel->obtenerUsuariosConRoles();
                foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo $u['id_usuario']; ?></td>
                        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td><?php echo $u['fecha_registro']; ?></td>
                        <td><?php echo $u['roles'] ?? 'Sin rol'; ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-edit">Editar</a>
                            <a href="eliminar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-delete" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
