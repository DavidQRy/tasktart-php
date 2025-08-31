<?php
require_once __DIR__.'/controllers/AuthController.php';

$auth = new AuthController();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $auth->login($_POST);

    if ($user) {
        // Redirigir al dashboard (ejemplo)
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "❌ Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - TaskManager</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Ingresar</button>
    </form>

    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</body>
</html>
