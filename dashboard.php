<?php
session_start();
require_once __DIR__.'/models/User.php';
require_once __DIR__.'/models/Proyecto.php'; // nuevo modelo para proyectos

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];
$usuarioModel = new User();
$esAdmin = $usuarioModel->esAdmin($user['id_usuario']);

$proyectoModel = new Proyecto();

// Mensaje desde acciones (unirse, eliminar, etc.)
$msg = $_GET['msg'] ?? null;

// Crear proyecto (desde el modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_proyecto') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;

    if (!empty($nombre)) {
    // 🔹 Obtener plan y límite
    $planUsuario = $usuarioModel->obtenerPlanUsuario($user['id_usuario']); 
    $limite = $planUsuario['limite_proyectos'] ?? 1; // Por defecto 1 si no tiene plan

    // 🔹 Contar proyectos existentes
    $totalProyectos = $proyectoModel->contarProyectosUsuario($user['id_usuario']);

    // 🔹 Validar límite
    if ($limite != 0 && $totalProyectos >= $limite) {
        $msg = "Has alcanzado el límite de proyectos de tu plan actual.";
    } else {
        $stmt = $proyectoModel->crearProyecto($user['id_usuario'], $nombre, $descripcion, $fecha_inicio, $fecha_fin);
        if ($stmt) {
            header("Location: dashboard.php?msg=Proyecto creado con éxito");
            exit;
        } else {
            $msg = "Error al crear el proyecto.";
        }
    }
} else {
    $msg = "El nombre del proyecto es obligatorio.";
}

}

// Proyectos propios y de otros usuarios
$proyectosPropios = $proyectoModel->obtenerProyectosCreados($user['id_usuario']);
$proyectosUnidos = $proyectoModel->obtenerProyectosUnidos($user['id_usuario']);
$proyectosDisponibles = $proyectoModel->obtenerProyectosDisponibles($user['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - TaskManager</title>
  <link rel="stylesheet" href="visual/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<header class="header">
  <h1>TaskTart</h1>
  <nav class="navBar">
    <a href="dashboard.php">Dashboard</a>
    <a href="tareas.php">Tareas</a>
    <a href="">Perfil</a>
    <a href="planes.php">Planes</a>
    <a href="logout.php" class="logout-btn">Cerrar sesión</a>
    <button class="dark-mode-toggle" onclick="document.body.classList.toggle('dark-mode')">🌙 / ☀️</button>
  </nav>
</header>

<div class="bg-white p-5">
  <h2>Bienvenido <?php echo htmlspecialchars($user['nombre']); ?> 👋</h2>
  <p>Has iniciado sesión correctamente.</p>
</div>

<div class="container-fluid p-4">
  <?php if ($esAdmin): ?>
  <div class="card shadow-lg border-0 rounded-4 mb-5">
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
              <th>#</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Registro</th>
              <th>Rol(es)</th>
              <th class="text-center">Acciones</th>
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
                <td><span class="badge bg-secondary"><?php echo date("d/m/Y", strtotime($u['fecha_registro'])); ?></span></td>
                <td>
                  <?php if (!empty($u['roles'])):
                    foreach (explode(',', $u['roles']) as $rol): ?>
                      <span class="badge bg-info text-dark me-1"><i class="fas fa-shield-alt"></i> <?= htmlspecialchars(trim($rol)); ?></span>
                  <?php endforeach; else: ?>
                    <span class="badge bg-light text-muted">Sin rol</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <a href="editar_usuario.php?id=<?= $u['id_usuario']; ?>" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>
                  <button onclick="confirmarEliminacion(<?= $u['id_usuario']; ?>)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Sección de proyectos -->
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h3 class="mb-0"><i class="fas fa-folder-open me-2"></i> Proyectos</h3>
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#crearProyectoModal">
        <i class="fas fa-plus"></i> Nuevo Proyecto
      </button>
    </div>
    <div class="card-body">
      <h5><i class="fas fa-user"></i> Mis proyectos</h5>
      <div class="row mb-4">
        <?php if (count($proyectosPropios) > 0): ?>
          <?php foreach ($proyectosPropios as $p): ?>
            <div class="col-md-4 mb-3">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($p['nombre']); ?></h5>
                  <p class="card-text"><?= htmlspecialchars($p['descripcion']); ?></p>
                  <small class="text-muted">Inicio: <?= $p['fecha_inicio']; ?></small><br>
                  <small class="text-muted">Fin: <?= $p['fecha_fin'] ?: 'No definida'; ?></small>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No tienes proyectos creados.</p>
        <?php endif; ?>
      </div>

      <h5><i class="fas fa-users"></i> Proyectos donde participo</h5>
      <div class="row mb-4">
        <?php if (count($proyectosUnidos) > 0): ?>
          <?php foreach ($proyectosUnidos as $p): ?>
            <div class="col-md-4 mb-3">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($p['nombre']); ?></h5>
                  <p class="card-text"><?= htmlspecialchars($p['descripcion']); ?></p>
                  <span class="badge bg-info">Rol: <?= $p['rol_en_proyecto']; ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No participas en otros proyectos aún.</p>
        <?php endif; ?>
      </div>

      <h5><i class="fas fa-handshake"></i> Proyectos disponibles para unirse</h5>
      <div class="row">
        <?php if (count($proyectosDisponibles) > 0): ?>
          <?php foreach ($proyectosDisponibles as $p): ?>
            <div class="col-md-4 mb-3">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($p['nombre']); ?></h5>
                  <p class="card-text"><?= htmlspecialchars($p['descripcion']); ?></p>
                  <form method="POST" action="unirse_proyecto.php">
                    <input type="hidden" name="id_proyecto" value="<?= $p['id_proyecto']; ?>">
                    <button class="btn btn-success btn-sm w-100"><i class="fas fa-user-plus"></i> Unirse</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No hay proyectos disponibles para unirse.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- 🧩 Modal Crear Proyecto -->
<div class="modal fade" id="crearProyectoModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-folder-plus me-2"></i> Crear Proyecto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="accion" value="crear_proyecto">
        <div class="mb-3">
          <label class="form-label">Nombre del proyecto</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Fecha de inicio</label>
          <input type="date" name="fecha_inicio" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Fecha de finalización</label>
          <input type="date" name="fecha_fin" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success"><i class="fas fa-save"></i> Crear</button>
      </div>
    </form>
  </div>
</div>

<script>
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

<?php if ($msg): ?>
Swal.fire({
  title: "Aviso",
  text: "<?php echo htmlspecialchars($msg); ?>",
  icon: "success"
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
