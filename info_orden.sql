-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 19-08-2024 a las 03:18:37
-- Versión del servidor: 8.0.37
-- Versión de PHP: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `quicktr`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `info_orden`
--

CREATE TABLE `info_orden` (
  `id` int NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `documento` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `servicio` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `precio` float NOT NULL,
  `iva` float NOT NULL,
  `precio-final` float NOT NULL,
  `desc` text COLLATE utf8mb4_general_ci NOT NULL,
  `local` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` date DEFAULT NULL,
  `firma` text COLLATE utf8mb4_general_ci,
  `tipo` enum('servicio','venta') COLLATE utf8mb4_general_ci NOT NULL,
  `razon` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dept` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indices de la tabla `info_orden`
--
ALTER TABLE `info_orden`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `info_orden`
--
ALTER TABLE `info_orden`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
