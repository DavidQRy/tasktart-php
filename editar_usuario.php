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
    header("Location: dashboard.php?msg=ID de usuario no especificado.");
    exit;
}

$id = intval($_GET['id']);
$usuario = $usuarioModel->getUserById($id);

if (!$usuario) {
    header("Location: dashboard.php?msg=Usuario no encontrado.");
    exit;
}

// Roles disponibles y roles actuales del usuario
$roles = $usuarioModel->obtenerRoles();
$currentRoles = $usuarioModel->obtenerRolesPorUsuario($id);
$currentRoleId = !empty($currentRoles) ? (int)$currentRoles[0]['id_rol'] : null;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_rol = isset($_POST['id_rol']) && $_POST['id_rol'] !== '' ? intval($_POST['id_rol']) : null;

    if ($nombre === '' || $correo === '') {
        $error = "⚠ Nombre y correo son obligatorios.";
    } else {
        $ok1 = $usuarioModel->updateUsuario($id, $nombre, $correo, $password !== '' ? $password : null);
        $ok2 = $usuarioModel->setUserRole($id, $id_rol);

        if ($ok1 && $ok2) {
            header("Location: dashboard.php?msg=✅ Usuario actualizado correctamente.");
            exit;
        } else {
            $error = "❌ Ocurrió un error al actualizar. Revisa logs.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="visual\css\style.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        label { display:block; margin-top:8px; }
        input, select { padding:6px; width:300px; }
        .msg-success { color: green; }
        .msg-error { color: red; }
    </style>
</head>
<body>
    <header class="header">
        <h1>TaskTart</h1>
        <nav class="navBar">
            <a href="dashboard.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <a href="">Tareas</a>
            <a href="">Perfil</a>
            <a href="logout.php" class="logout-btn">Cerrar sesión</a>
            <button class="dark-mode-toggle" onclick="document.body.classList.toggle('dark-mode')">
                🌙 / ☀️
            </button>
        </nav>
    </header>
    
<?php if (!empty($message)) : ?>
<script>
  Swal.fire({
    title: "Aviso",
    text: "<?php echo htmlspecialchars($message); ?>",
    icon: "<?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>"
  });
</script>
<?php endif; ?>

<?php if (!empty($error)): ?>
<script>
  Swal.fire({
    title: "Error",
    text: "<?php echo htmlspecialchars($error); ?>",
    icon: "error"
  });
</script>
<?php endif; ?>
    <div class="formRegistro">

        <form method="POST">
            <label class="dato">
                <input type="text" name="nombre" id="nombre" required 
                    value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" 
                    placeholder="Nombre">
            </label>
            
            <label class="dato">
                <input type="email" name="correo" id="correo" required 
                    value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" 
                    placeholder="Correo Electrónico">
            </label>
            
            <label class="dato">
                <input type="password" name="password" id="contraseña" 
                    placeholder="Contraseña (dejar vacío para no cambiar)">
            </label>
            
            <label class="dato">
                <select name="id_rol">
                    <option value="">-- Seleccione un rol --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?php echo $r['id_rol']; ?>" 
                            <?php if (isset($currentRoleId) && $currentRoleId == $r['id_rol']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($r['nombre_rol']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    
    </div>
</body>
</html>
