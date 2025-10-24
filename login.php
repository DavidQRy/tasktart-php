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
        <nav class="navBar">
            <a href="#home">Inicio</a>
            <a href="#features">Servicios</a>
            <a href="#about">Nosotros</a>
            <a href="#contact">Contacto</a>
            <a href="login.php" style="background-color: white; color:black;">Iniciar sesión</a>
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

<!--Formulario de registro Back y Front!-->

<div class="formRegistro">
   
<?php if (!empty($message)) : ?>

 <script>
    Swal.fire({
    title: "Aviso",
    text: "<?php echo htmlspecialchars($message); ?>",
    icon: "<?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>"
    });
 </script>

<?php endif; ?>

<form method="POST" action="">

    <h1>Bienvenido a TaskTart</h1>

    <label class="dato">
        <input type="email" name="correo" id="correo" required placeholder="Correo Electrónico">
    </label>

    <label class="dato">
    <input type="password" name="password" id="password" required placeholder="Contraseña (8 Digitos)">
    </label>

    <button type="submit">Ingresar</button>

        <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
    
    </form>

    </div>
    
</body>
</html>
