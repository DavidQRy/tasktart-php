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
    <link rel="stylesheet" href="visual\css\style.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
     <header class="header">
         <h1>TaskTart</h1>
        <nav class ="navBar">
            <a href="">Inicio</a>
            <a href="">¿Qué somos?</a>
            <a href="">Contacto</a>
            <a href="login.php">Iniciar sesión</a>
            <a href="register.php">Registrarse</a>
        </nav>
        <button class="dark-toggle" onclick="toggleDarkMode()" title="Cambiar tema">
        🌙
</button>
    </header>

     <!-- Dark Mode Toggle -->
      <script>
        // Dark mode toggle function
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const darkToggle = document.querySelector('.dark-toggle');
            
            if (document.body.classList.contains('dark-mode')) {
                darkToggle.textContent = '☀️';
            } else {
                darkToggle.textContent = '🌙';
            }
        }
      </script>
    

<?php if (!empty($message)) : ?>
<script>
  Swal.fire({
    title: "Aviso",
    text: "<?php echo htmlspecialchars($message); ?>",
    icon: "<?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>"
  });
</script>
<?php endif; ?>


    <div class="formRegistro">
        <form method="POST" action="">
            <h1>Bienvenido a TaskTart</h1>
            <label class="dato">
                <input type="text" name="nombre" id="nombre" required placeholder="Nombre">
            </label>
            <!-- <label class="dato">
                <input type="text" name="apellido" id="apellido" required placeholder="Apellido">
            </label>
            <label class="dato">
                <input type="int" name="telefono" id="telefono" required placeholder="Teléfono">
            </label> -->
            <label class="dato">
                <input type="varchar" name="correo" id="correo" required placeholder="Correo Electrónico">
            </label>
            <label class="dato">
                <input type="password" name="password" id="contraseña" required placeholder="Contraseña">
            </label>
            <button type="submit">Registrarse</button>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia
        </form>
        
    </div>
</body>
</html>