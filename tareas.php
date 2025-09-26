<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>TaskStart - Dashboard de Tareas</title>
    <style>
        .header {
            background-color: #e8854f;
            color: white;
            padding: 15px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-size: 16px;
        }

        .nav-links a:hover {
            color: #ffccaa;
        }

        .dark-mode-btn {
            background-color: white;
            border: 1px solid #ccc;
            color: #333;
            padding: 8px 12px;
            margin-left: 20px;
            border-radius: 3px;
            font-size: 14px;
        }

        .main-content {
            padding: 30px;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        .task-columns {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .task-column {
            flex: 1;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .column-title {
            background-color: #8accf0;
            color: white;
            padding: 10px;
            text-align: center;
            margin: -15px -15px 15px -15px;
            border-radius: 8px 8px 0 0;
        }

        .task-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .task-title {
            font-weight: bold;
            color: #333333;
        }

        .task-description {
            color: #666;
            margin: 8px 0;
        }

        .priority-high {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }

        .priority-medium {
            background-color: #ffc107;
            color: black;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }

        .priority-low {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }

        .task-date {
            color: #999;
            font-size: 14px;
            margin-top: 10px;
        }

        .add-btn {
            background-color: #854fe8;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            margin-top: 10px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #e8854f;
        }

        /* Dark mode */
        body.dark-mode {
            background-color: #333;
            color: white;
        }

        body.dark-mode .main-content {
            background-color: #444;
        }

        body.dark-mode .task-column {
            background-color: #555;
        }

        body.dark-mode .task-card {
            background-color: #666;
            border-color: #777;
            color: white;
        }

        body.dark-mode .stat-box {
            background-color: #555;
            color: white;
        }

        @media (max-width: 768px) {
            .task-columns {
                flex-direction: column;
            }
            .stats {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>TaskTart</h1>
            <div class="nav-links d-flex align-items-center">
                <a href="dashboard.php">Dashboard</a>
                <a href="">Tareas</a>
                <a href="">Perfil</a>
                <a href="logout.php">Cerrar sesión</a>
                <button class="dark-mode-btn" onclick="toggleDarkMode()">🌙/☀️</button>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Dashboard de Tareas</h2>
            
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-number">8</div>
                    <div>Total tareas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">3</div>
                    <div>Pendientes</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">2</div>
                    <div>En progreso</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">3</div>
                    <div>Completadas</div>
                </div>
            </div>

            <div class="task-columns">
                <div class="task-column">
                    <div class="column-title">Pendientes</div>
                    
                    <div class="task-card">
                        <div class="task-title">Diseñar interfaz</div>
                        <div class="task-description">Hacer los mockups para la app</div>
                        <span class="priority-high">Alta</span>
                        <div class="task-date">Fecha: 28/09/2025</div>
                    </div>

                    <div class="task-card">
                        <div class="task-title">Revisar documentación</div>
                        <div class="task-description">Actualizar docs del proyecto</div>
                        <span class="priority-medium">Media</span>
                        <div class="task-date">Fecha: 30/09/2025</div>
                    </div>

                    <div class="task-card">
                        <div class="task-title">Planificar sprint</div>
                        <div class="task-description">Organizar tareas siguientes</div>
                        <span class="priority-low">Baja</span>
                        <div class="task-date">Fecha: 02/10/2025</div>
                    </div>

                    <button class="add-btn" onclick="addTask()">+ Agregar tarea</button>
                </div>

                <div class="task-column">
                    <div class="column-title">En progreso</div>
                    
                    <div class="task-card">
                        <div class="task-title">Desarrollar API</div>
                        <div class="task-description">Hacer endpoints de usuarios</div>
                        <span class="priority-high">Alta</span>
                        <div class="task-date">Fecha: 26/09/2025</div>
                    </div>

                    <div class="task-card">
                        <div class="task-title">Testing</div>
                        <div class="task-description">Pruebas de los componentes</div>
                        <span class="priority-medium">Media</span>
                        <div class="task-date">Fecha: 29/09/2025</div>
                    </div>

                    <button class="add-btn" onclick="addTask()">+ Agregar tarea</button>
                </div>

                <div class="task-column">
                    <div class="column-title">Completadas</div>
                    
                    <div class="task-card">
                        <div class="task-title">Configurar BD</div>
                        <div class="task-description">Setup inicial de database</div>
                        <span class="priority-high">Alta</span>
                        <div class="task-date">Fecha: 20/09/2025</div>
                    </div>

                    <div class="task-card">
                        <div class="task-title">Investigar tecnologías</div>
                        <div class="task-description">Ver qué framework usar</div>
                        <span class="priority-medium">Media</span>
                        <div class="task-date">Fecha: 22/09/2025</div>
                    </div>

                    <div class="task-card">
                        <div class="task-title">Setup entorno</div>
                        <div class="task-description">Instalar herramientas</div>
                        <span class="priority-low">Baja</span>
                        <div class="task-date">Fecha: 25/09/2025</div>
                    </div>

                    <button class="add-btn" onclick="addTask()">+ Agregar tarea</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function addTask() {
            alert('Aquí iría el código para agregar una nueva tarea');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>