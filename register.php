<?php
require_once __DIR__.'/controllers/AuthController.php';

$auth = new AuthController();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = $auth->register($_POST);

    if ($ok) {
        $message = "✅ Usuario registrado correctamente. Ahora puedes iniciar sesión.";
    } else {
        $message = "❌ Error al registrar usuario. Intenta de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - TaskManager</title>
</head>
<body>
    <h2>Registro de Usuario</h2>

    <?php if (!empty($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>
