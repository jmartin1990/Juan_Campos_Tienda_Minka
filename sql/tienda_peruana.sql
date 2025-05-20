-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 20-05-2025 a las 17:19:19
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
-- Base de datos: `tienda_peruana`
--
CREATE DATABASE IF NOT EXISTS `tienda_peruana` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tienda_peruana`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','O') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellidos`, `correo`, `fecha_nacimiento`, `genero`) VALUES
(1, 'Juan', 'Campos', 'jmartin.campos90@gmail.com', '1990-09-15', 'M'),
(3, 'Marta Cristina', 'Sanchez Guzman', 'marta1985@gmail.com', '1988-06-10', 'F'),
(7, '&#039; OR &#039;1&#039;=&#039;1', '&#039; OR &#039;1&#039;=&#039;1', 'carlitos90@gmail.com', '0000-00-00', 'M'),
(8, 'Maria Susana', 'Sanchez Carrion', 'maria90@gmail.com', '2011-01-06', 'F');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `cliente_id`, `fecha`, `total`) VALUES
(1, 1, '2025-05-17 14:37:19', 45.90),
(13, 3, '2025-05-20 04:18:03', 47.00),
(14, 8, '2025-05-20 14:58:18', 120.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_compra`
--

CREATE TABLE `detalles_compra` (
  `compra_id` int(11) NOT NULL,
  `producto_ref` varchar(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_compra`
--

INSERT INTO `detalles_compra` (`compra_id`, `producto_ref`, `cantidad`, `precio_unitario`) VALUES
(1, 'P001', 1, 45.90),
(13, 'P004', 2, 8.30),
(13, 'P005', 2, 15.20),
(14, 'P001', 2, 45.90),
(14, 'P002', 1, 12.50),
(14, 'P004', 2, 8.30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `referencia` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`referencia`, `nombre`, `precio`, `descripcion`, `imagen`) VALUES
('P001', 'Pisco Peruano', 45.90, 'Pisco puro de uva, botella 750ml', NULL),
('P002', 'Chocolate de Cusco', 12.50, 'Chocolate artesanal con granos de cacao peruano', NULL),
('P003', 'Café de Chanchamayo', 28.75, 'Café gourmet de altura', NULL),
('P004', 'Aceitunas de Tacna', 8.30, 'Aceitunas verdes y negras', NULL),
('P005', 'Quinua Orgánica', 15.20, 'Quinua real de los Andes', NULL),
('P006', 'Maca en Polvo', 22.40, 'Superalimento andino', NULL),
('P007', 'Ají Amarillo', 7.80, 'Pasta de ají amarillo peruano', NULL),
('P008', 'Alpaca Sweater', 89.90, 'Chompa de lana de alpaca', NULL),
('P009', 'Spirulina Peruana', 34.50, 'Alga nutritiva del Lago Titicaca', NULL),
('P010', 'Olluco Seco', 6.75, 'Tubérculo andino deshidratado', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contrasena`, `cliente_id`) VALUES
(1, 'juan', '$2y$10$YYshRaN4jwnUYKMVLLdNb.ZId.2DHfHkGRb83db/ukM.vNN9Srz9e', 1),
(2, 'Marta', '$2y$10$FIlVkuvMJ0Lhy5JFvXpxf.498wEiaInoBNkhDnarAtjTx8bh18Bb2', 3),
(3, '&#039; OR &#039;1&#039;=&#039;1', '$2y$10$qAlBmB0B.9L373dwoOrTMOaV/7fMs9tSkRTrE4VQ49viYWc/OJP3K', 7),
(4, 'Maria', '$2y$10$/eWv5bzzPVg/JJEMI6XW..W8btYkJDXenbYp/IlmLzoDc7.KYw2SS', 8);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `detalles_compra`
--
ALTER TABLE `detalles_compra`
  ADD PRIMARY KEY (`compra_id`,`producto_ref`),
  ADD KEY `producto_ref` (`producto_ref`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`referencia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `detalles_compra`
--
ALTER TABLE `detalles_compra`
  ADD CONSTRAINT `detalles_compra_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `detalles_compra_ibfk_2` FOREIGN KEY (`producto_ref`) REFERENCES `productos` (`referencia`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
