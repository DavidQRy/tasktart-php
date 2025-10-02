CREATE DATABASE tasktart;
USE DATABASE tasktart;
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion TEXT
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuario_rol (
    id_usuario INT,
    id_rol INT,
    activo BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE CASCADE
);

CREATE TABLE proyectos (
    id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    id_creador INT,
    FOREIGN KEY (id_creador) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

CREATE TABLE tareas (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado VARCHAR(50) DEFAULT 'Pendiente',
    prioridad VARCHAR(20),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_limite DATE,
    id_proyecto INT,
    id_asignado INT,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (id_asignado) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

CREATE TABLE historial_tareas (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_tarea INT,
    id_usuario INT,
    cambio TEXT,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tarea) REFERENCES tareas(id_tarea) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

INSERT INTO roles (nombre_rol, descripcion)
VALUES
('Administrador', 'Control total del sistema: gestión de usuarios, proyectos y tareas.'),
('Project Manager', 'Crea y administra proyectos, asigna tareas y supervisa el progreso.'),
('Team Leader', 'Lidera un equipo dentro de un proyecto, distribuye tareas y supervisa colaboradores.'),
('Colaborador', 'Ejecuta tareas asignadas, actualiza estados y colabora en los proyectos.'),
('Cliente', 'Acceso limitado, solo visualiza el avance del proyecto.'),
('Invitado', 'Acceso mínimo, generalmente de solo lectura.');

INSERT INTO usuario_rol (id_usuario, id_rol, activo)
VALUES (1, 1, TRUE);

-- MÓDULO TRANSACCIONAL (NUEVAS TABLAS)

CREATE TABLE planes (
    id_plan SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    duracion_dias INT NOT NULL -- cuánto dura el plan
);

CREATE TABLE pagos (
    id_pago SERIAL PRIMARY KEY,
    id_usuario INT REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    id_plan INT REFERENCES planes(id_plan) ON DELETE CASCADE,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'Pendiente', -- Pendiente, Completado, Fallido
    referencia VARCHAR(100) -- simulación de un ID de transacción
);

INSERT INTO planes (nombre, descripcion, precio, duracion_dias) VALUES
('Gratis', 'Plan básico con acceso limitado. Hasta 1 proyecto y 5 tareas activas.', 0.00, 30),
('Starter', 'Ideal para usuarios individuales. Hasta 5 proyectos y 50 tareas activas.', 4.99, 30),
('Pro', 'Pensado para equipos pequeños. Hasta 20 proyectos, usuarios ilimitados y 500 tareas activas.', 14.99, 30),
('Business', 'Para empresas. Proyectos ilimitados, integración avanzada y soporte prioritario.', 29.99, 30),
('Anual Pro', 'Mismo que el plan Pro, pero con descuento por pago anual.', 149.99, 365);