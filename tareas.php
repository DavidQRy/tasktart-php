<?php
session_start();
require_once __DIR__ . '/controllers/TareaController.php';
require_once __DIR__ . '/models/User.php';

// Solo usuarios logueados
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$controller = new TareaController();
$usuarioModel = new User();
$usuarios = $usuarioModel->obtenerUsuariosConRoles();

// === Roles y permisos ===
$usuario = $_SESSION['usuario'];
$rolUsuario = $usuarioModel->obtenerRolUsuario($usuario['id_usuario']);

// Si no tiene rol definido o no existe en los permisos, se asigna "Invitado"
if (empty($rolUsuario) || !isset($permisos[$rolUsuario])) {
    $rolUsuario = "Invitado";
}


$permisos = [
    "Administrador"    => ["crear", "editar", "eliminar"],
    "Project Manager"  => ["crear", "editar", "eliminar"],
    "Team Leader"      => ["crear", "editar"],
    "Colaborador"      => ["editar"],
    "Cliente"          => [],
    "Invitado"         => []
];

// Procesar acciones (con verificación de permisos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $controller->store();
    } elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar' && isset($_POST['id_tarea']) && in_array("editar", $permisos[$rolUsuario])) {
        $controller->update($_POST['id_tarea']);
    }
} elseif (isset($_GET['delete']) && in_array("eliminar", $permisos[$rolUsuario])) {
    $controller->delete($_GET['delete']);
}

require_once __DIR__ . '/models/Proyecto.php';
$proyectoModel = new Proyecto();

// Proyectos en los que el usuario está o que creó
$proyectosUsuario = array_merge(
    $proyectoModel->obtenerProyectosCreados($usuario['id_usuario']),
    $proyectoModel->obtenerProyectosUnidos($usuario['id_usuario'])
);

// Proyecto seleccionado
$idProyectoSeleccionado = $_GET['proyecto'] ?? ($proyectosUsuario[0]['id_proyecto'] ?? null);
$idProyectoSeleccionadoURL = $_GET['proyecto'] ?? null;

// Proyecto seleccionado
$idProyectoSeleccionado = $_GET['proyecto'] ?? ($proyectosUsuario[0]['id_proyecto'] ?? null);
$idProyectoSeleccionadoURL = $_GET['proyecto'] ?? $idProyectoSeleccionado ?? null;

// === Roles y permisos ===
$rolUsuario = $usuarioModel->obtenerRolUsuarioProyecto($usuario['id_usuario'], $idProyectoSeleccionado);

// Usuarios asignables (solo del proyecto actual)
$usuarios = $usuarioModel->obtenerUsuariosPorProyecto($idProyectoSeleccionado);


// Modificar consultas de tareas para filtrar por proyecto
$conteo = $controller->modelo->contarTareas($idProyectoSeleccionado);
$pendientes = $controller->modelo->obtenerPorEstado("Pendiente", $idProyectoSeleccionado);
$progreso   = $controller->modelo->obtenerPorEstado("En progreso", $idProyectoSeleccionado);
$completadas= $controller->modelo->obtenerPorEstado("Completada", $idProyectoSeleccionado);

$total = $conteo['Pendiente'] + $conteo['En progreso'] + $conteo['Completada'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>TaskStart - Dashboard de Tareas</title>
    <style>
        .header { background-color: #e8854f; color: white; padding: 15px 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .nav-links a { color: white; text-decoration: none; margin: 0 20px; font-size: 16px; }
        .nav-links a:hover { color: #ffccaa; }
        .dark-mode-btn { background-color: white; border: 1px solid #ccc; color: #333; padding: 8px 12px; margin-left: 20px; border-radius: 3px; font-size: 14px; }
        .main-content { padding: 30px; background-color: #f5f5f5; min-height: 100vh; }
        .task-columns { display: flex; gap: 20px; margin-top: 20px; }
        .task-column { flex: 1; background-color: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .column-title { background-color: #8accf0; color: white; padding: 10px; text-align: center; margin: -15px -15px 15px -15px; border-radius: 8px 8px 0 0; }
        .task-card { background-color: #ffffff; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .task-title { font-weight: bold; color: #333333; }
        .task-description { color: #666; margin: 8px 0; }
        .priority-alta { background-color: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .priority-media { background-color: #ffc107; color: black; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .priority-baja { background-color: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .task-date { color: #999; font-size: 14px; margin-top: 10px; }
        .add-btn { background-color: #854fe8; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; margin-top: 10px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-box { background-color: white; padding: 20px; border-radius: 5px; text-align: center; flex: 1; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-number { font-size: 24px; font-weight: bold; color: #e8854f; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>TaskTart</h1>
            <div class="nav-links d-flex align-items-center">
                <a href="dashboard.php">Dashboard</a>
                <a href="tareas.php">Tareas</a>
                <a href="perfil.php">Perfil</a>
                <a href="logout.php">Cerrar sesión</a>
                <button class="dark-mode-btn" onclick="toggleDarkMode()">🌙/☀️</button>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Dashboard de Tareas</h2>
            <form method="GET" class="mb-3">
                <div class="input-group" style="max-width: 400px;">
                    <label class="input-group-text bg-primary text-white">Proyecto</label>
                    <select name="proyecto" class="form-select" onchange="this.form.submit()">
                        <?php foreach($proyectosUsuario as $proy): ?>
                            <option value="<?= $proy['id_proyecto']; ?>" <?= ($proy['id_proyecto'] == $idProyectoSeleccionado) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($proy['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            
            <!-- Estadísticas -->
            <div class="stats">
                <div class="stat-box"><div class="stat-number"><?= $total ?></div><div>Total tareas</div></div>
                <div class="stat-box"><div class="stat-number"><?= $conteo['Pendiente'] ?></div><div>Pendientes</div></div>
                <div class="stat-box"><div class="stat-number"><?= $conteo['En progreso'] ?></div><div>En progreso</div></div>
                <div class="stat-box"><div class="stat-number"><?= $conteo['Completada'] ?></div><div>Completadas</div></div>
            </div>

            <!-- Columnas de tareas -->
            <div class="task-columns">
                <!-- Pendientes -->
                <div class="task-column">
                    <div class="column-title">Pendientes</div>
                    <?php foreach($pendientes as $t): ?>
                        <div class="task-card">
                            <div class="task-title"><?= htmlspecialchars($t['titulo']) ?></div>
                            <div class="task-description"><?= htmlspecialchars($t['descripcion']) ?></div>
                            <small><strong>Asignado a:</strong> <?= $t['asignado'] ?? 'Sin asignar' ?></small><br>
                            <span class="priority-<?= strtolower($t['prioridad']) ?>"><?= $t['prioridad'] ?></span>
                            <div class="task-date">Fecha: <?= $t['fecha_limite'] ?></div>

                            <?php if (in_array("editar", $permisos[$rolUsuario])): ?>
                                <a href="#" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#editarModal<?= $t['id_tarea'] ?>">Editar</a>
                            <?php endif; ?>

                            <?php if (in_array("eliminar", $permisos[$rolUsuario])): ?>
                                <a href="tareas.php?delete=<?= $t['id_tarea'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</a>
                            <?php endif; ?>
                        </div>

                        <!-- Modal editar -->
                        <?php if (in_array("editar", $permisos[$rolUsuario])): ?>
                        <div class="modal fade" id="editarModal<?= $t['id_tarea'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Editar Tarea</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_tarea" value="<?= $t['id_tarea'] ?>">
                                    <div class="mb-3"><label>Título</label><input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($t['titulo']) ?>" required></div>
                                    <div class="mb-3"><label>Descripción</label><textarea name="descripcion" class="form-control"><?= htmlspecialchars($t['descripcion']) ?></textarea></div>
                                    <div class="mb-3">
                                        <label>Estado</label>
                                        <select name="estado" class="form-control">
                                            <option <?= $t['estado']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                            <option <?= $t['estado']=='En progreso'?'selected':'' ?>>En progreso</option>
                                            <option <?= $t['estado']=='Completada'?'selected':'' ?>>Completada</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Prioridad</label>
                                        <select name="prioridad" class="form-control">
                                            <option <?= $t['prioridad']=='Alta'?'selected':'' ?>>Alta</option>
                                            <option <?= $t['prioridad']=='Media'?'selected':'' ?>>Media</option>
                                            <option <?= $t['prioridad']=='Baja'?'selected':'' ?>>Baja</option>
                                        </select>
                                    </div>
                                    <div class="mb-3"><label>Fecha límite</label><input type="date" name="fecha_limite" class="form-control" value="<?= $t['fecha_limite'] ?>"></div>
                                    <div class="mb-3">
                                        <label>Responsable</label>
                                        <select name="id_asignado" class="form-control">
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach($usuarios as $u): ?>
                                                <option value="<?= $u['id_usuario'] ?>" <?= $t['id_asignado']==$u['id_usuario']?'selected':'' ?>><?= htmlspecialchars($u['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-primary">Guardar cambios</button></div>
                            </form>
                          </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if (in_array("crear", $permisos[$rolUsuario])): ?>
                        <button class="add-btn" data-bs-toggle="modal" data-bs-target="#agregarModal">+ Agregar tarea</button>
                    <?php endif; ?>
                </div>

                <!-- En progreso -->
                <div class="task-column">
                    <div class="column-title">En progreso</div>
                    <?php foreach($progreso as $t): ?>
                        <div class="task-card">
                            <div class="task-title"><?= htmlspecialchars($t['titulo']) ?></div>
                            <div class="task-description"><?= htmlspecialchars($t['descripcion']) ?></div>
                            <small><strong>Asignado a:</strong> <?= $t['asignado'] ?? 'Sin asignar' ?></small><br>
                            <span class="priority-<?= strtolower($t['prioridad']) ?>"><?= $t['prioridad'] ?></span>
                            <div class="task-date">Fecha: <?= $t['fecha_limite'] ?></div>

                            <?php if (in_array("editar", $permisos[$rolUsuario])): ?>
                                <a href="#" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#editarModal<?= $t['id_tarea'] ?>">Editar</a>
                            <?php endif; ?>

                            <?php if (in_array("eliminar", $permisos[$rolUsuario])): ?>
                                <a href="tareas.php?delete=<?= $t['id_tarea'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</a>
                            <?php endif; ?>
                        </div>

                        <?php if (in_array("editar", $permisos[$rolUsuario])): ?>
                        <!-- Modal editar -->
                        <div class="modal fade" id="editarModal<?= $t['id_tarea'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Editar Tarea</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_tarea" value="<?= $t['id_tarea'] ?>">
                                    <div class="mb-3"><label>Título</label><input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($t['titulo']) ?>" required></div>
                                    <div class="mb-3"><label>Descripción</label><textarea name="descripcion" class="form-control"><?= htmlspecialchars($t['descripcion']) ?></textarea></div>
                                    <div class="mb-3">
                                        <label>Estado</label>
                                        <select name="estado" class="form-control">
                                            <option <?= $t['estado']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                            <option <?= $t['estado']=='En progreso'?'selected':'' ?>>En progreso</option>
                                            <option <?= $t['estado']=='Completada'?'selected':'' ?>>Completada</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Prioridad</label>
                                        <select name="prioridad" class="form-control">
                                            <option <?= $t['prioridad']=='Alta'?'selected':'' ?>>Alta</option>
                                            <option <?= $t['prioridad']=='Media'?'selected':'' ?>>Media</option>
                                            <option <?= $t['prioridad']=='Baja'?'selected':'' ?>>Baja</option>
                                        </select>
                                    </div>
                                    <div class="mb-3"><label>Fecha límite</label><input type="date" name="fecha_limite" class="form-control" value="<?= $t['fecha_limite'] ?>"></div>
                                    <div class="mb-3">
                                        <label>Responsable</label>
                                        <select name="id_asignado" class="form-control">
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach($usuarios as $u): ?>
                                                <option value="<?= $u['id_usuario'] ?>" <?= $t['id_asignado']==$u['id_usuario']?'selected':'' ?>><?= htmlspecialchars($u['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-primary">Guardar cambios</button></div>
                            </form>
                          </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                                            
                <!-- Completadas -->
                <div class="task-column">
                    <div class="column-title">Completadas</div>
                    <?php foreach($completadas as $t): ?>
                        <div class="task-card">
                            <div class="task-title"><?= htmlspecialchars($t['titulo']) ?></div>
                            <div class="task-description"><?= htmlspecialchars($t['descripcion']) ?></div>
                            <small><strong>Asignado a:</strong> <?= $t['asignado'] ?? 'Sin asignar' ?></small><br>
                            <span class="priority-<?= strtolower($t['prioridad']) ?>"><?= $t['prioridad'] ?></span>
                            <div class="task-date">Fecha: <?= $t['fecha_limite'] ?></div>

                            <?php if (in_array("editar", $permisos[$rolUsuario])): ?>
                                <a href="#" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#editarModal<?= $t['id_tarea'] ?>">Editar</a>
                            <?php endif; ?>

                            <?php if (in_array("eliminar", $permisos[$rolUsuario])): ?>
                                <a href="tareas.php?delete=<?= $t['id_tarea'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</a>
                            <?php endif; ?>
                        </div>
                    
                        <?php if (in_array("editar", $permisos[$rolUsuario])): ?>
                        <!-- Modal editar -->
                        <div class="modal fade" id="editarModal<?= $t['id_tarea'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Editar Tarea</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_tarea" value="<?= $t['id_tarea'] ?>">
                                    <div class="mb-3"><label>Título</label><input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($t['titulo']) ?>" required></div>
                                    <div class="mb-3"><label>Descripción</label><textarea name="descripcion" class="form-control"><?= htmlspecialchars($t['descripcion']) ?></textarea></div>
                                    <div class="mb-3">
                                        <label>Estado</label>
                                        <select name="estado" class="form-control">
                                            <option <?= $t['estado']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                            <option <?= $t['estado']=='En progreso'?'selected':'' ?>>En progreso</option>
                                            <option <?= $t['estado']=='Completada'?'selected':'' ?>>Completada</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Prioridad</label>
                                        <select name="prioridad" class="form-control">
                                            <option <?= $t['prioridad']=='Alta'?'selected':'' ?>>Alta</option>
                                            <option <?= $t['prioridad']=='Media'?'selected':'' ?>>Media</option>
                                            <option <?= $t['prioridad']=='Baja'?'selected':'' ?>>Baja</option>
                                        </select>
                                    </div>
                                    <div class="mb-3"><label>Fecha límite</label><input type="date" name="fecha_limite" class="form-control" value="<?= $t['fecha_limite'] ?>"></div>
                                    <div class="mb-3">
                                        <label>Responsable</label>
                                        <select name="id_asignado" class="form-control">
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach($usuarios as $u): ?>
                                                <option value="<?= $u['id_usuario'] ?>" <?= $t['id_asignado']==$u['id_usuario']?'selected':'' ?>><?= htmlspecialchars($u['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-primary">Guardar cambios</button></div>
                            </form>
                          </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

    <!-- Modal agregar tarea -->
    <?php if (in_array("crear", $permisos[$rolUsuario])): ?>
    <div class="modal fade" id="agregarModal" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Nueva Tarea</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="accion" value="crear">
                <div class="mb-3"><label>Título</label><input type="text" name="titulo" class="form-control" required></div>
                <div class="mb-3"><label>Descripción</label><textarea name="descripcion" class="form-control"></textarea></div>
                <div class="mb-3"><label>Estado</label>
                    <select name="estado" class="form-control">
                        <option>Pendiente</option>
                        <option>En progreso</option>
                        <option>Completada</option>
                    </select>
                </div>
                <div class="mb-3"><label>Prioridad</label>
                    <select name="prioridad" class="form-control">
                        <option>Alta</option>
                        <option>Media</option>
                        <option>Baja</option>
                    </select>
                </div>
                <div class="mb-3"><label>Fecha límite</label><input type="date" name="fecha_limite" class="form-control"></div>
                <div class="mb-3">
                    <label>Responsable</label>
                    <select name="id_asignado" class="form-control">
                        <option value="">-- Seleccionar --</option>
                        <?php foreach($usuarios as $u): ?>
                            <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($idProyectoSeleccionadoURL) ?>">
            </div>
            <div class="modal-footer"><button class="btn btn-success">Guardar</button></div>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
