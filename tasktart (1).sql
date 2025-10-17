-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2025 a las 03:41:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tasktart`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_tareas`
--

CREATE TABLE `historial_tareas` (
  `id_historial` int(11) NOT NULL,
  `id_tarea` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `cambio` text DEFAULT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_plan` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'Pendiente',
  `referencia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_usuario`, `id_plan`, `fecha_pago`, `estado`, `referencia`) VALUES
(1, 1, 1, '2025-09-25 23:37:41', 'Completado', 'TXN_68d5d24552ff7'),
(2, 1, 2, '2025-09-25 23:38:06', 'Completado', 'TXN_68d5d25e87ebb'),
(3, 1, 5, '2025-09-25 23:38:42', 'Completado', 'TXN_68d5d28256d72'),
(4, 1, 3, '2025-09-26 00:24:27', 'Completado', 'TXN_68d5dd3b0cf9f'),
(5, 1, 3, '2025-10-01 02:35:35', 'Completado', 'TXN_68dc9377cf1ae'),
(6, 12, 1, '2025-10-01 02:44:12', 'Completado', 'TXN_68dc957c9ab0f'),
(7, 2, 1, '2025-10-02 23:21:56', 'Completado', 'TXN_68df091419210'),
(8, 1, 1, '2025-10-07 02:02:28', 'Pendiente', 'TXN_68e474b419fe7'),
(9, 13, 2, '2025-10-07 02:12:15', 'Completado', 'TXN_68e476ffccada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id_plan` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `duracion_dias` int(11) NOT NULL,
  `limite_proyectos` int(11) DEFAULT 1,
  `limite_tareas` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id_plan`, `nombre`, `descripcion`, `precio`, `duracion_dias`, `limite_proyectos`, `limite_tareas`) VALUES
(1, 'Gratis', 'Plan básico con acceso limitado. Hasta 1 proyecto y 5 tareas activas.', 0.00, 30, 1, 5),
(2, 'Starter', 'Ideal para usuarios individuales. Hasta 5 proyectos y 50 tareas activas.', 4.99, 30, 5, 50),
(3, 'Pro', 'Pensado para equipos pequeños. Hasta 20 proyectos, usuarios ilimitados y 500 tareas activas.', 14.99, 30, 20, 500),
(4, 'Business', 'Para empresas. Proyectos ilimitados, integración avanzada y soporte prioritario.', 29.99, 30, 999999, 9999999),
(5, 'Anual Pro', 'Mismo que el plan Pro, pero con descuento por pago anual.', 149.99, 365, 9999999, 9999999);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id_proyecto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_creador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id_proyecto`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `id_creador`) VALUES
(6, 'Prueba', '', '2025-10-06', '0000-00-00', 1),
(7, 'PRUEBA 2', 'saddasd', '2025-11-01', '2025-10-06', 12),
(8, 'PRUEBA PLAN', 'asdasd', '0000-00-00', '0000-00-00', 13),
(9, 'PRUEBA PLAN 2', 'asdasd', '0000-00-00', '0000-00-00', 13),
(10, '3', '', '0000-00-00', '0000-00-00', 13),
(11, '4', '', '0000-00-00', '0000-00-00', 13),
(12, '5', '', '0000-00-00', '0000-00-00', 13),
(13, '1', '', '0000-00-00', '0000-00-00', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Control total del sistema: gestión de usuarios, proyectos y tareas.'),
(2, 'Project Manager', 'Crea y administra proyectos, asigna tareas y supervisa el progreso.'),
(3, 'Team Leader', 'Lidera un equipo dentro de un proyecto, distribuye tareas y supervisa colaboradores.'),
(4, 'Colaborador', 'Ejecuta tareas asignadas, actualiza estados y colabora en los proyectos.'),
(5, 'Cliente', 'Acceso limitado, solo visualiza el avance del proyecto.'),
(6, 'Invitado', 'Acceso mínimo, generalmente de solo lectura.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente',
  `prioridad` varchar(20) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` date DEFAULT NULL,
  `id_proyecto` int(11) DEFAULT NULL,
  `id_asignado` int(11) DEFAULT NULL,
  `id_creador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id_tarea`, `titulo`, `descripcion`, `estado`, `prioridad`, `fecha_creacion`, `fecha_limite`, `id_proyecto`, `id_asignado`, `id_creador`) VALUES
(1, 'Desarrollo de API', 'Dearrollo de api rest con node.js', 'En progreso', 'Alta', '2025-09-30 01:56:40', '2025-09-24', NULL, 1, NULL),
(2, 'Mockup', 'Mockup a el backend de autentificacion', 'Completada', 'Media', '2025-09-30 02:00:11', '2025-09-27', NULL, 1, NULL),
(9, 'Desarrollo de API', 'Con Golang', 'En progreso', 'Media', '2025-09-30 02:04:02', '2025-09-25', NULL, 2, NULL),
(12, 'TS ', 'Prueba con TypeScript', 'En progreso', 'Media', '2025-09-30 02:15:52', '2025-10-04', NULL, 2, NULL),
(15, 'Desarrollo de API', 'PHP ', 'Pendiente', 'Alta', '2025-09-30 02:47:18', '2025-10-11', NULL, 1, NULL),
(17, 'asdasd', 'prueba', 'Pendiente', 'Alta', '2025-10-06 23:59:04', '2025-10-06', NULL, 1, NULL),
(26, 'Desarrollo de API', 'adada', 'En progreso', 'Alta', '2025-10-07 00:38:02', '2025-10-06', NULL, 2, NULL),
(27, 'sadasd', 'asdasd', 'En progreso', 'Alta', '2025-10-07 00:39:11', '2025-10-24', NULL, 1, NULL),
(29, 'ASDASDASD', 'ASFASFSAF', 'Pendiente', 'Media', '2025-10-07 00:44:01', '0000-00-00', NULL, 2, NULL),
(31, 'ADFSAF', 'SAFDASFAS', 'Completada', 'Baja', '2025-10-07 00:45:01', '0000-00-00', NULL, 1, NULL),
(34, 'preuba de prueba', 'asd', 'Pendiente', 'Media', '2025-10-07 01:54:57', '0000-00-00', 6, 12, NULL),
(35, 'ASDSADF', 'DSAASD', 'Pendiente', 'Alta', '2025-10-07 03:17:57', '0000-00-00', 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contraseña`, `fecha_registro`) VALUES
(1, 'David', 'davidgui393@gmail.com', '$2y$10$DGWl/455rF9e5sXfHs532OhpLpPJR.pY7Dz1YhxBDBkDbH6NLnLBy', '2025-08-31 19:01:39'),
(2, 'David Quintero', 'davidqui939@gmail.com', '$2y$10$KmcY964yoUbGI9tIOMZ9t.lItpCZOUby221G1BgXJtJy50RpIMJAC', '2025-08-31 19:30:54'),
(12, 'David Quintero', 'david@gmail.com', '$2y$10$aWAJtLZvoCA5i9mEEBVPVu3XqTSoF5yd.5pJ7YgRgaqnFWkabnTPm', '2025-09-30 02:06:09'),
(13, 'alert', 'alert@gmail', '$2y$10$Sz94HAieby94ixt7Ej/M/uHxWhaUG9byz/LVg0rSLmEe3a44rrIkW', '2025-10-07 02:10:31'),
(14, 'tareas', 'tareas@a', '$2y$10$V.S5zknKm0XskajSnBYWheKF/MS/eKprAxzbTdLsWo2vxWOi54w9a', '2025-10-07 02:38:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_proyecto`
--

CREATE TABLE `usuario_proyecto` (
  `id_usuario` int(11) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `rol_en_proyecto` enum('Project Manager','Team Leader','Colaborador','Cliente') DEFAULT 'Colaborador',
  `fecha_union` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_proyecto`
--

INSERT INTO `usuario_proyecto` (`id_usuario`, `id_proyecto`, `rol_en_proyecto`, `fecha_union`) VALUES
(1, 6, 'Project Manager', '2025-10-07 01:54:28'),
(1, 7, 'Colaborador', '2025-10-07 01:59:11'),
(2, 6, 'Colaborador', '2025-10-07 02:59:10'),
(12, 6, 'Colaborador', '2025-10-07 01:56:45'),
(12, 7, 'Project Manager', '2025-10-07 01:57:43'),
(13, 8, 'Project Manager', '2025-10-07 02:11:38'),
(13, 9, 'Project Manager', '2025-10-07 02:13:24'),
(13, 10, 'Project Manager', '2025-10-07 02:13:59'),
(13, 11, 'Project Manager', '2025-10-07 02:14:04'),
(13, 12, 'Project Manager', '2025-10-07 02:14:09'),
(14, 13, 'Project Manager', '2025-10-07 02:38:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_rol`
--

INSERT INTO `usuario_rol` (`id_usuario`, `id_rol`, `activo`) VALUES
(1, 1, 1),
(2, 1, 1),
(12, 3, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `fk_historial_tarea` (`id_tarea`),
  ADD KEY `fk_historial_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `pagos_ibfk_1` (`id_usuario`),
  ADD KEY `pagos_ibfk_2` (`id_plan`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id_plan`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id_proyecto`),
  ADD KEY `fk_proyecto_creador` (`id_creador`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `fk_tareas_creador` (`id_creador`),
  ADD KEY `fk_tareas_proyecto` (`id_proyecto`),
  ADD KEY `fk_tareas_asignado` (`id_asignado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `usuario_proyecto`
--
ALTER TABLE `usuario_proyecto`
  ADD PRIMARY KEY (`id_usuario`,`id_proyecto`),
  ADD KEY `id_proyecto` (`id_proyecto`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`id_usuario`,`id_rol`),
  ADD KEY `fk_usuario_rol_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id_plan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  ADD CONSTRAINT `fk_historial_tarea` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_plan`) REFERENCES `planes` (`id_plan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_proyecto_creador` FOREIGN KEY (`id_creador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `fk_tareas_asignado` FOREIGN KEY (`id_asignado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tareas_creador` FOREIGN KEY (`id_creador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tareas_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_proyecto`
--
ALTER TABLE `usuario_proyecto`
  ADD CONSTRAINT `usuario_proyecto_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_proyecto_ibfk_2` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `fk_usuario_rol_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_rol_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
