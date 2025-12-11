-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 12-12-2025 a las 00:28:56
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
-- Base de datos: `pubs`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `authors`
--

CREATE TABLE `authors` (
  `au_id` varchar(11) NOT NULL,
  `au_lname` varchar(40) NOT NULL,
  `au_fname` varchar(20) NOT NULL,
  `phone` char(12) NOT NULL DEFAULT 'UNKNOWN',
  `address` varchar(40) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` char(5) DEFAULT NULL,
  `contract` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `authors`
--

INSERT INTO `authors` (`au_id`, `au_lname`, `au_fname`, `phone`, `address`, `city`, `state`, `zip`, `contract`) VALUES
('172-32-1176', 'White', 'Johnson', '408 496-7223', '10932 Bigge Rd.', 'Menlo Park', 'CA', '94025', 1),
('213-46-8915', 'Green', 'Marjorie', '415 986-7020', '309 63rd St. #411', 'Oakland', 'CA', '94618', 1),
('238-95-7766', 'Carson', 'Cheryl', '415 548-7723', '589 Darwin Ln.', 'Berkeley', 'CA', '94705', 1),
('267-41-2394', 'O\'Leary', 'Michael', '408 286-2428', '2255 Gaylord St.', 'San Jose', 'CA', '95120', 1),
('274-80-9391', 'Straight', 'Dean', '415 836-7128', '5420 College Ave.', 'Oakland', 'CA', '94618', 1),
('341-22-1782', 'Smith', 'Mei', '415 836-7128', '1033 8th Ave.', 'Corvallis', 'OR', '97330', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discounts`
--

CREATE TABLE `discounts` (
  `discounttype` varchar(40) NOT NULL,
  `stor_id` char(4) DEFAULT NULL,
  `lowqty` smallint(6) DEFAULT NULL,
  `highqty` smallint(6) DEFAULT NULL,
  `discount` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `discounts`
--

INSERT INTO `discounts` (`discounttype`, `stor_id`, `lowqty`, `highqty`, `discount`) VALUES
('Initial Customer', NULL, NULL, NULL, 10.50),
('Volume Discount', '6380', 100, 1000, 6.70),
('Customer Discount', '8042', NULL, NULL, 5.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employee`
--

CREATE TABLE `employee` (
  `emp_id` char(9) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `minit` char(1) DEFAULT NULL,
  `lname` varchar(30) NOT NULL,
  `job_id` smallint(6) NOT NULL DEFAULT 1,
  `job_lvl` tinyint(4) DEFAULT 10,
  `pub_id` char(4) NOT NULL DEFAULT '9952',
  `hire_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `employee`
--

INSERT INTO `employee` (`emp_id`, `fname`, `minit`, `lname`, `job_id`, `job_lvl`, `pub_id`, `hire_date`) VALUES
('A-C71970F', 'Paolo', 'X', 'Accorti', 14, 89, '0877', '1993-08-27 00:00:00'),
('A-D50000X', 'Victoria', 'V', 'Ashworth', 13, 35, '0877', '1993-09-13 00:00:00'),
('A-E57798C', 'Helen', NULL, 'Bennett', 14, 35, '0877', '1993-09-12 00:00:00'),
('B-C32247F', 'Francisco', 'P', 'Chang', 10, 127, '1389', '1993-01-22 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `job_id` smallint(6) NOT NULL,
  `job_desc` varchar(50) NOT NULL DEFAULT 'New Position',
  `min_lvl` tinyint(4) NOT NULL,
  `max_lvl` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jobs`
--

INSERT INTO `jobs` (`job_id`, `job_desc`, `min_lvl`, `max_lvl`) VALUES
(1, 'New Hire - Job not specified', 10, 10),
(2, 'Chief Executive Officer', 127, 127),
(3, 'Business Operations Manager', 127, 127),
(4, 'Chief Financial Officer', 127, 127),
(5, 'Publisher', 127, 127),
(6, 'Managing Editor', 125, 127),
(7, 'Marketing Manager', 75, 125),
(8, 'Public Relations Manager', 75, 125),
(9, 'Acquisitions Editor', 75, 127),
(10, 'Production Manager', 100, 127),
(11, 'Operations Manager', 75, 127),
(12, 'Editor', 25, 125),
(13, 'Sales Representative', 25, 100),
(14, 'Designer', 25, 100),
(15, 'Programmer', 25, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publishers`
--

CREATE TABLE `publishers` (
  `pub_id` char(4) NOT NULL,
  `pub_name` varchar(40) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `country` varchar(30) DEFAULT 'USA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publishers`
--

INSERT INTO `publishers` (`pub_id`, `pub_name`, `city`, `state`, `country`) VALUES
('0001', 'Mixup', 'Torreón', 'CO', 'MX'),
('0736', 'New Age Books', 'Boston', 'MA', 'USA'),
('0877', 'Binnet & Hardley', 'Washington', 'DC', 'USA'),
('1389', 'Algodata Infosystems', 'Berkeley', 'CA', 'USA'),
('9952', 'Scootney Books', 'New York', 'NY', 'USA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `stor_id` char(4) NOT NULL,
  `ord_num` varchar(20) NOT NULL,
  `ord_date` datetime NOT NULL,
  `qty` smallint(6) NOT NULL,
  `payterms` varchar(12) NOT NULL,
  `title_id` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`stor_id`, `ord_num`, `ord_date`, `qty`, `payterms`, `title_id`) VALUES
('6380', 'ORD-1765421609', '2025-12-11 00:00:00', 2, 'Net 30', 'PS0001'),
('7067', 'ORD-1765420731', '2025-12-11 00:00:00', 30, 'Net 30', 'BU1032'),
('7067', 'ORD-1765421240', '2025-12-11 00:00:00', 1, 'Net 30', 'PS0001'),
('7067', 'ORD-1765421835', '2025-12-11 00:00:00', 1, 'Net 30', 'BU1111'),
('7067', 'P2121', '1993-03-11 00:00:00', 5, 'Net 60', 'BU1032'),
('7067', 'P2121', '1993-03-11 00:00:00', 10, 'Net 60', 'BU1111'),
('7068', 'ORD-1765421287', '2025-12-11 00:00:00', 10, 'Net 30', 'BU1111'),
('7068', 'ORD-1765421594', '2025-12-11 00:00:00', 10, 'Net 30', 'BU1111'),
('7068', 'ORD-1765424391', '2025-12-11 00:00:00', 1, 'Net 30', 'PS0001'),
('7068', 'QA7442', '1992-09-13 00:00:00', 10, 'Net 60', 'BU1032');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stockinstores`
--

CREATE TABLE `stockinstores` (
  `stor_id` char(4) NOT NULL,
  `title_id` varchar(6) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stockinstores`
--

INSERT INTO `stockinstores` (`stor_id`, `title_id`, `qty`) VALUES
('1110', 'BU1111', 0),
('1110', 'PS0001', 10),
('6380', 'BU1111', 0),
('6380', 'PS0001', 2),
('7067', 'BU1032', 20),
('7067', 'BU1111', 29),
('7067', 'PS0001', 1),
('7068', 'BU1032', 45),
('7068', 'BU1111', 0),
('7068', 'BU2075', 10),
('7068', 'PS0001', 2),
('7131', 'BU1111', 13),
('7131', 'PC8888', 15),
('7131', 'PS0001', 20),
('8042', 'BU1111', 25),
('8042', 'BU2075', 100),
('8042', 'PS0001', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stores`
--

CREATE TABLE `stores` (
  `stor_id` char(4) NOT NULL,
  `stor_name` varchar(40) DEFAULT NULL,
  `stor_address` varchar(40) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stores`
--

INSERT INTO `stores` (`stor_id`, `stor_name`, `stor_address`, `city`, `state`, `zip`) VALUES
('1110', 'Gonvil', 'Blvrd. Independencia 1300', 'Torreón', 'CO', '27010'),
('6380', 'Eric the Read Books', '788 Catamaugus Ave.', 'Seattle', 'WA', '98056'),
('7067', 'Barnum\'s', '567 Pasadena Ave.', 'Tustin', 'CA', '92789'),
('7068', 'Bookbeat', '679 Carson St.', 'Carson', 'CA', '97260'),
('7131', 'Doc-U-Mat: Quality Laundry and Books', '2410 Take-A-Dive Dr.', 'Deerfield', 'IL', '60015'),
('8042', 'Fricative Bookshop', '89 Madison St.', 'New York', 'NY', '10016');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titleauthor`
--

CREATE TABLE `titleauthor` (
  `au_id` varchar(11) NOT NULL,
  `title_id` varchar(6) NOT NULL,
  `au_ord` tinyint(4) DEFAULT NULL,
  `royaltyper` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `titleauthor`
--

INSERT INTO `titleauthor` (`au_id`, `title_id`, `au_ord`, `royaltyper`) VALUES
('172-32-1176', 'BU1032', 2, 40),
('213-46-8915', 'BU1111', 1, 100),
('238-95-7766', 'BU2075', 1, 50),
('238-95-7766', 'PS0001', 1, 50),
('274-80-9391', 'PC8888', 1, 100),
('341-22-1782', 'BU1032', 1, 60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titles`
--

CREATE TABLE `titles` (
  `title_id` varchar(6) NOT NULL,
  `title` varchar(80) NOT NULL,
  `type` char(12) NOT NULL DEFAULT 'UNDECIDED',
  `pub_id` char(4) DEFAULT NULL,
  `price` decimal(10,4) DEFAULT NULL,
  `advance` decimal(10,4) DEFAULT NULL,
  `royalty` int(11) DEFAULT NULL,
  `ytd_sales` int(11) DEFAULT NULL,
  `notes` varchar(200) DEFAULT NULL,
  `pubdate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `titles`
--

INSERT INTO `titles` (`title_id`, `title`, `type`, `pub_id`, `price`, `advance`, `royalty`, `ytd_sales`, `notes`, `pubdate`) VALUES
('BU1032', 'The Busy Executive\'s Database Guide', 'business', '1389', 19.9900, 5000.0000, 10, 4095, 'An overview of the different types of databases', '1991-06-12 00:00:00'),
('BU1111', 'Cooking with Computers: Surreptitious Balance Sheets', 'business', '1389', 11.9500, 5000.0000, 10, 3876, 'Hands-on guide to the setup and use of a personal computer.', '1992-07-09 00:00:00'),
('BU2075', 'You Can Combat Computer Stress!', 'business', '0736', 2.9900, 10000.0000, 24, 18722, 'The latest medical and psychological techniques.', '1991-06-30 00:00:00'),
('PC8888', 'Secrets of Silicon Valley', 'popular_comp', '1389', 20.0000, 20000.0000, 24, 6722, 'Data on the personnel of computer', '1991-06-12 00:00:00'),
('PS0001', 'Concepto De Lo Mental', 'psychology', '0736', 299.9900, NULL, NULL, NULL, 'Si nuestros cuerpos existen en el espacio y el tiempo, y están sujetos a las leyes de la física, de alguna manera nuestras mentes tienen que estar ocultas dentro de ellos como extraños e inmateriales.', '2005-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `fecha_registro`, `rol`) VALUES
(1, 'admin', 'mmtz5818@gmail.com', '$2y$10$zeFpyQR9AhLsM2VsE7HKJO6jLwcB33JnvOJlsD5akag2NaLEWM4Ue', '2025-12-10 16:40:18', 0),
(5, 'majo', 'takafallingjin@gmail.com', '$2y$10$KGpREKzTs.7VgCs24xcDxO80zhTEGs1yBQvL7sHH0w5VhPDQo/cuS', '2025-12-10 20:54:31', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`au_id`);

--
-- Indices de la tabla `discounts`
--
ALTER TABLE `discounts`
  ADD KEY `stor_id` (`stor_id`);

--
-- Indices de la tabla `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `pub_id` (`pub_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`);

--
-- Indices de la tabla `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`pub_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`stor_id`,`ord_num`,`title_id`),
  ADD KEY `title_id` (`title_id`);

--
-- Indices de la tabla `stockinstores`
--
ALTER TABLE `stockinstores`
  ADD PRIMARY KEY (`stor_id`,`title_id`),
  ADD KEY `title_id` (`title_id`);

--
-- Indices de la tabla `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`stor_id`);

--
-- Indices de la tabla `titleauthor`
--
ALTER TABLE `titleauthor`
  ADD PRIMARY KEY (`au_id`,`title_id`),
  ADD KEY `title_id` (`title_id`);

--
-- Indices de la tabla `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`title_id`),
  ADD KEY `pub_id` (`pub_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`stor_id`) REFERENCES `stores` (`stor_id`);

--
-- Filtros para la tabla `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`),
  ADD CONSTRAINT `employee_ibfk_2` FOREIGN KEY (`pub_id`) REFERENCES `publishers` (`pub_id`);

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`stor_id`) REFERENCES `stores` (`stor_id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`title_id`) REFERENCES `titles` (`title_id`);

--
-- Filtros para la tabla `stockinstores`
--
ALTER TABLE `stockinstores`
  ADD CONSTRAINT `stockinstores_ibfk_1` FOREIGN KEY (`stor_id`) REFERENCES `stores` (`stor_id`),
  ADD CONSTRAINT `stockinstores_ibfk_2` FOREIGN KEY (`title_id`) REFERENCES `titles` (`title_id`);

--
-- Filtros para la tabla `titleauthor`
--
ALTER TABLE `titleauthor`
  ADD CONSTRAINT `titleauthor_ibfk_1` FOREIGN KEY (`au_id`) REFERENCES `authors` (`au_id`),
  ADD CONSTRAINT `titleauthor_ibfk_2` FOREIGN KEY (`title_id`) REFERENCES `titles` (`title_id`);

--
-- Filtros para la tabla `titles`
--
ALTER TABLE `titles`
  ADD CONSTRAINT `titles_ibfk_1` FOREIGN KEY (`pub_id`) REFERENCES `publishers` (`pub_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
