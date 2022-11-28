-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2022 a las 21:32:47
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lacomanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`id`, `descripcion`) VALUES
(1, 'Salon'),
(2, 'Cocina'),
(3, 'Bar'),
(4, 'Administracion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `id_area_empleado` int(11) DEFAULT NULL,
  `nombre` varchar(20) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `usuario_id`, `id_area_empleado`, `nombre`, `fecha_alta`, `fecha_baja`) VALUES
(4, 4, 1, 'Juan Carlos', '2022-11-28 13:22:08', NULL),
(5, 5, 1, 'Maria Laura', '2022-11-28 13:22:47', NULL),
(6, 6, 2, 'Lucia', '2022-11-28 13:23:41', NULL),
(7, 7, 2, 'Felipe', '2022-11-28 13:24:01', NULL),
(8, 8, 3, 'Alejandro', '2022-11-28 13:24:15', NULL),
(9, 9, 3, 'Cesar', '2022-11-28 13:24:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `mesa_puntuacion` int(11) NOT NULL,
  `restaurante_puntuacion` int(11) NOT NULL,
  `mozo_puntuacion` int(11) NOT NULL,
  `cocinero_puntuacion` int(11) NOT NULL,
  `puntuacion_promedio` int(11) NOT NULL,
  `comentario` varchar(66) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `pedido_id`, `mesa_puntuacion`, `restaurante_puntuacion`, `mozo_puntuacion`, `cocinero_puntuacion`, `puntuacion_promedio`, `comentario`) VALUES
(1, 4, 6, 8, 10, 9, 8, 'La mesa parecia la de los argento, pero la comida buenisima!');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logsusuarios`
--

CREATE TABLE `logsusuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `fecha_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `logsusuarios`
--

INSERT INTO `logsusuarios` (`id`, `usuario_id`, `nombre_usuario`, `fecha_login`) VALUES
(6, 4, 'mozo_001', '2022-11-28 13:37:01'),
(7, 7, 'cocinero_002', '2022-11-28 13:37:51'),
(8, 9, 'barman_002', '2022-11-28 13:41:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `codigo_mesa` varchar(5) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `estado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `codigo_mesa`, `id_empleado`, `estado`) VALUES
(12, 'ME001', NULL, 'Cerrada'),
(13, 'ME002', NULL, 'Cerrada'),
(14, 'ME003', NULL, 'Cerrada'),
(15, 'ME004', NULL, 'Cerrada'),
(16, 'ME005', NULL, 'Cerrada'),
(17, 'ME006', NULL, 'Cerrada'),
(18, 'ME007', 4, 'Con cliente esperando pedido'),
(19, 'ME008', NULL, 'Cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id` int(11) NOT NULL,
  `area_orden` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `estado` varchar(30) NOT NULL,
  `descripcion` varchar(128) NOT NULL,
  `precio` int(11) NOT NULL,
  `tiempo_inicio` datetime NOT NULL,
  `tiempo_fin` datetime DEFAULT NULL,
  `tiempo_estimado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id`, `area_orden`, `id_pedido`, `estado`, `descripcion`, `precio`, `tiempo_inicio`, `tiempo_fin`, `tiempo_estimado`) VALUES
(11, 2, 4, 'En preparacion', 'Milanesa a caballo con papas', 1299, '2022-11-28 15:18:23', '2022-11-28 15:38:23', 20),
(12, 3, 4, 'En preparacion', 'Fernet con coca', 1299, '2022-11-28 15:19:15', '2022-11-28 15:44:15', 25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `mesa_id` int(11) DEFAULT NULL,
  `estado_pedido` varchar(50) NOT NULL,
  `nombre_cliente` varchar(50) NOT NULL,
  `costo_pedido` float NOT NULL,
  `foto_pedido` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `mesa_id`, `estado_pedido`, `nombre_cliente`, `costo_pedido`, `foto_pedido`) VALUES
(4, 18, 'En preparacion', 'Ricardo', 2598, './PedidoImagenes/Pedido_4.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `clave` varchar(256) NOT NULL,
  `esAdmin` tinyint(1) NOT NULL,
  `tipo_usuario` varchar(50) DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `clave`, `esAdmin`, `tipo_usuario`, `estado`, `fecha_alta`, `fecha_baja`) VALUES
(3, 'nico_diaz', '$2y$10$9v7ADVB9eFcJd3Capsbf4uhSjk1DuoG73ycE8sVJijGv99mvTfJMG', 1, 'Admin', 'Activo', '2022-11-28 12:32:02', NULL),
(4, 'mozo_001', '$2y$10$LrRI9gpUL8fpFf.7/wSS0.osSCpWbqKW7em4vzjJSmxiARYVKblaa', 0, 'Mozo', 'Activo', '2022-11-28 12:42:11', NULL),
(5, 'mozo_002', '$2y$10$feBwavPnBdkuUqqE.c0/rO63atExgx.m9TMKV8vRrlfafwT5x3Dby', 0, 'Mozo', 'Activo', '2022-11-28 12:43:29', NULL),
(6, 'cocinero_001', '$2y$10$uNR1EUVn3Dt/D/7aXxY7ieedyoclf4NMHrjNGI4/guKH6V7xPWPm.', 0, 'Cocinero', 'Activo', '2022-11-28 12:44:34', NULL),
(7, 'cocinero_002', '$2y$10$/2DAqRMHOOnVy6P1h0F0/e.mfjrKkIAAL8a6cdBB348eC7UACD7zy', 0, 'Cocinero', 'Activo', '2022-11-28 12:45:15', NULL),
(8, 'barman_001', '$2y$10$T.PvtoRRKSEE8nDBPjQ29u1l1o/PtQzXp.SCd3XO9wamqeSzo5hAe', 0, 'Barman', 'Activo', '2022-11-28 12:46:21', NULL),
(9, 'barman_002', '$2y$10$ke9Q0nySMVHtQnb7tWgMvevly/3nxDcw1pP3544Xki0CfyhZzVjn2', 0, 'Barman', 'Activo', '2022-11-28 12:46:45', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `id_area_empleado` (`id_area_empleado`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `logsusuarios`
--
ALTER TABLE `logsusuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`) USING BTREE;

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_mesa` (`codigo_mesa`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mesa_id` (`mesa_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombre_usuario` (`nombre_usuario`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `logsusuarios`
--
ALTER TABLE `logsusuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `FK_id_area_empleado` FOREIGN KEY (`id_area_empleado`) REFERENCES `area` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD CONSTRAINT `FK_pedido_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `	FK_id_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `FK_mesa_id` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
