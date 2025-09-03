<?php
session_start();
require_once __DIR__.'/models/User.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];
$usuarioModel = new User();
$esAdmin = $usuarioModel->esAdmin($user['id_usuario']);

// Mensaje desde eliminar_usuario.php
$msg = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - TaskManager</title>
  <link rel="stylesheet" href="visual/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="dashboard.php">Dashboard</a>
            <a href="">Tareas</a>
            <a href="">Perfil</a>
            <a href="logout.php" class="logout-btn">Cerrar sesión</a>
            <button class="dark-mode-toggle" onclick="document.body.classList.toggle('dark-mode')">
                🌙 / ☀️
            </button>
        </nav>
    </header>
<div class="bg-white p-5">
    <h2>Bienvenido <?php echo htmlspecialchars($user['nombre']); ?> 👋</h2>
    <p>Has iniciado sesión correctamente.</p>
</div>
<div class="container-fluid p-4">
  <?php if ($esAdmin): ?>
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
        <h3 class="mb-0"><i class="fas fa-users-cog me-2"></i> Gestión de Usuarios</h3>
        <a href="crear_usuario.php" class="btn btn-success btn-sm">
          <i class="fas fa-user-plus"></i> Nuevo Usuario
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Correo</th>
                <th scope="col">Registro</th>
                <th scope="col">Rol(es)</th>
                <th scope="col" class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $usuarios = $usuarioModel->obtenerUsuariosConRoles();
              foreach ($usuarios as $u): ?>
                <tr>
                  <td><strong><?php echo $u['id_usuario']; ?></strong></td>
                  <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                  <td><?php echo htmlspecialchars($u['correo']); ?></td>
                  <td>
                    <span class="badge bg-secondary">
                      <?php echo date("d/m/Y", strtotime($u['fecha_registro'])); ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($u['roles'])): ?>
                      <?php foreach (explode(',', $u['roles']) as $rol): ?>
                        <span class="badge bg-info text-dark me-1">
                          <i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars(trim($rol)); ?>
                        </span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span class="badge bg-light text-muted">Sin rol</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <a href="editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" 
                       class="btn btn-warning btn-sm me-1">
                       <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="confirmarEliminacion(<?php echo $u['id_usuario']; ?>)" 
                            class="btn btn-danger btn-sm">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>


  <script>
    // Confirmación SweetAlert al eliminar
    function confirmarEliminacion(id) {
      Swal.fire({
        title: "¿Seguro que deseas eliminar este usuario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "eliminar_usuario.php?id=" + id;
        }
      });
    }

    // Mostrar mensajes desde PHP
    <?php if ($msg): ?>
      Swal.fire({
        title: "Aviso",
        text: "<?php echo htmlspecialchars($msg); ?>",
        icon: "success"
      });
    <?php endif; ?>
  </script>
</body>
</html>
