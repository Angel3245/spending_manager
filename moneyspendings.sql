-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-01-2023 a las 16:55:54
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET SQL_MODE = "NO_ZERO_DATE";

drop schema if exists moneyspendings;
create schema moneyspendings;

-- Some extra sentences for creation of the DB user
DROP user IF EXISTS 'tswuser'@'localhost';
flush privileges;
CREATE USER 'tswuser'@'localhost' IDENTIFIED BY 'tswpass';
GRANT ALL PRIVILEGES ON moneyspendings.* TO 'tswuser'@'localhost' WITH GRANT OPTION;

USE moneyspendings;

-- Some extra sentences to ensure tables do not exist 
DROP TABLE IF EXISTS spending;
DROP TABLE IF EXISTS users; 
DROP FUNCTION IF EXISTS isDateCorrect;
DROP FUNCTION IF EXISTS isGreater;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `moneyspendings`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spending`
--

CREATE TABLE `spending` (
  `id_spending` int(11) NOT NULL,
  `type_spending` enum('FOOD','FUEL','COMMUNICATIONS','SUPPLIES','FREE TIME') NOT NULL CHECK (`type_spending` in ('FOOD','FUEL','COMMUNICATIONS','SUPPLIES','FREE TIME')),
  `date_spending` date NOT NULL,
  `qty_spending` decimal(5,2) NOT NULL DEFAULT 1.00 CHECK (`qty_spending` > 0.00),
  `description_spending` varchar(150) DEFAULT NULL,
  `doc_name_spending` varchar(100) DEFAULT NULL,
  `file_name_on_server` varchar(50) DEFAULT NULL,
  `owner_spending` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `alias` varchar(25) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `passwd` varchar(64) DEFAULT NULL,
  `role` varchar(100) DEFAULT 'user',
  `salt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`alias`, `email`, `passwd`, `role`, `salt`) VALUES
('admin', 'admin@gmail.com', '98e2cc2db16f5e31f1177dbbb7f8b655dddfb9809b7cdc1d5c414c4bb30f2040', 'admin', '7uBYoQ==');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `spending`
--
ALTER TABLE `spending`
  ADD PRIMARY KEY (`id_spending`),
  ADD KEY `owner_spending` (`owner_spending`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`alias`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `spending`
--
ALTER TABLE `spending`
  MODIFY `id_spending` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=804;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `spending`
--
ALTER TABLE `spending`
  ADD CONSTRAINT `spending_ibfk_1` FOREIGN KEY (`owner_spending`) REFERENCES `users` (`alias`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
