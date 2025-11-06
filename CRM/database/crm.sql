-- phpMyAdmin SQL Dump
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `crm_db` (Corregido para coincidir con config.php)
--
CREATE DATABASE IF NOT EXISTS `crm_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `crm_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users` (Añadida para login/registro)
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `clientes` (Corregida y mejorada)
--
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `empresa` varchar(255) DEFAULT NULL, 
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL, -- Añadido para coincidir con addClient.php
  `imagen_perfil` varchar(255) DEFAULT NULL, -- Añadido para coincidir con index.php
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos de ejemplo para la tabla `clientes`
--
INSERT INTO `clientes` (`id`, `nombre`, `empresa`, `email`, `telefono`, `direccion`, `imagen_perfil`, `fecha_registro`) VALUES
(1, 'Ana García', 'Innovate Solutions', 'ana.garcia@innovate.com', '611223344', 'Calle Falsa 123, Madrid', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330', '2024-05-23 10:00:00'),
(2, 'Carlos Rodriguez', 'TechPro Services', 'carlos.r@techpro.io', '622334455', 'Avenida Siempre Viva 742, Barcelona', NULL, '2024-05-23 10:05:00');

--
-- Estructura de tabla para la tabla `ventas` (Sugerencia de mejora)
--
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `notas` text DEFAULT NULL, -- Añadido para coincidir con addVentas.php
  `fecha_venta` date NOT NULL,
  -- Unificado con los estados usados en la aplicación (ventas.php, addVentas.php)
  `estado` enum('pendiente','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `tasks` (Añadida para la gestión de tareas)
--
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` varchar(50) DEFAULT 'medium',
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `informes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'borrador',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `tasks` (`id`, `title`, `client_id`, `description`, `due_date`, `priority`, `completed`, `created_at`) VALUES
(1, 'Llamar para seguimiento', 1, 'Seguimiento con Empresa ABC sobre la propuesta enviada.', '2025-11-06', 'high', 0, '2025-11-05 10:00:00'),
(2, 'Enviar propuesta', 2, 'Preparar y enviar propuesta a Ana García.', '2025-10-25', 'medium', 0, '2025-11-05 11:00:00'),
(3, 'Preparar reunión de equipo', NULL, 'Agenda y materiales para la reunión semanal del equipo.', '2025-10-28', 'low', 0, '2025-11-05 12:00:00'),
(4, 'Actualizar base de datos de clientes', NULL, 'Revisar y actualizar la información de contacto de los clientes antiguos.', '2025-11-08', 'medium', 0, '2025-11-05 13:00:00');
COMMIT;
