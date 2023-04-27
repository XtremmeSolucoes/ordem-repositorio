-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19-Abr-2023 às 01:59
-- Versão do servidor: 10.4.24-MariaDB
-- versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ordem`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2023-03-31-185641', 'App\\Database\\Migrations\\CriaTabelaUsuarios', 'default', 'App', 1680290301, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(5) UNSIGNED NOT NULL,
  `nome` varchar(128) NOT NULL,
  `email` varchar(240) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `reset_hash` varchar(80) DEFAULT NULL,
  `reset_expira_em` datetime DEFAULT NULL,
  `imagem` varchar(240) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL,
  `criado_em` datetime DEFAULT NULL,
  `atualizado_em` datetime DEFAULT NULL,
  `deletado_em` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `password_hash`, `reset_hash`, `reset_expira_em`, `imagem`, `ativo`, `criado_em`, `atualizado_em`, `deletado_em`) VALUES
(1, 'Scarlett Willms ', '36@gmail.com', '$2y$10$qHhxonp8sX45nzkf/MS9LupzNWZ72y/8nErG.6.OThjHBFIU7RMwm', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-04-16 12:30:54', NULL),
(2, 'Prof. April Stehr', 'astrosin@leannon.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-04-10 15:32:22', NULL),
(3, 'Prof. Skylar Schulist - alterado', 'wpagac@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-04-13 15:17:11', NULL),
(4, 'Prof. Drake Pfeffer Jr.', 'vtremblay@satterfield.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(5, 'Dr. Cindy Okuneva', 'dolores68@torphy.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(6, 'Celestino Kreiger', 'promaguera@bruen.org', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(7, 'Karlee Carter', 'chauck@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(8, 'Kelsie Kling- Atualizado', 'adela64atualizado@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-04-07 20:14:07', NULL),
(9, 'Veda Braun V', 'norberto.klein@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(10, 'Alicia Trantow', 'emayer@nicolas.net', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(11, 'Max Hyatt Sr.', 'chase.dach@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(12, 'Miss Lia Becker IV', 'anabel.brekke@ryan.biz', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(13, 'Josh Spencer', 'oconner.theresa@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(14, 'Mrs. Vincenza Schmitt III', 'zlowe@yahoo.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(15, 'Ms. Marina Jacobs DDS', 'ejacobi@boehm.info', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(16, 'Erica Bauch', 'qkoss@mann.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(17, 'Virgie Parker', 'vortiz@bogan.org', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(18, 'Pearline Feil', 'wilkinson.lina@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(19, 'Hillard Marks', 'ukris@mante.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(20, 'Reilly Herzog', 'zrohan@miller.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(21, 'Terry Luettgen', 'frida.howe@yahoo.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(22, 'Prof. Franco Graham PhD', 'green.bernard@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(23, 'Ilene Prohaska', 'candida37@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(24, 'Constantin Nikolaus DDS', 'maria.eichmann@white.biz', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(25, 'Joel Bahringer', 'gerhold.keira@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(26, 'Shayna Dickinson', 'vance62@gleason.info', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(27, 'Eugenia Grant', 'dillan22@zemlak.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(28, 'Della Stokes V', 'grady15@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(29, 'Nolan Monahan', 'jerrell.king@gulgowski.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(30, 'Marshall Hauck', 'kadin47@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(31, 'Moriah Will', 'padberg.giovanna@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(32, 'Nathanial Dach', 'nelda10@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(33, 'Sigrid Langosh', 'bsteuber@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(34, 'Mayra Koss', 'franz.carroll@corwin.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(35, 'Howard Jacobs', 'isadore53@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(36, 'Floyd Hauck', 'carlos.herzog@yahoo.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(37, 'Ted Erdman', 'della.grimes@marvin.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(38, 'Samantha Lockman', 'manuela.lowe@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(39, 'Nasir Mueller', 'ndicki@tremblay.info', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(40, 'Johanna Nitzsche', 'lerdman@boyle.org', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(41, 'Leonie Bartell', 'ron42@kling.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(42, 'Gilbert Jerde', 'cathy16@gorczany.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(43, 'Bennie Dare', 'jon30@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(44, 'Casey Runolfsdottir', 'emmerich.gussie@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(45, 'Oscar Rempel', 'turcotte.rowena@yahoo.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(46, 'Prof. Marcella Baumbach PhD', 'xharvey@hotmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(47, 'Prof. Amelie Hudson', 'kassulke.devin@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(48, 'Jamar Nitzsche', 'zwintheiser@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(49, 'Ms. Princess Vandervort', 'makenna18@nitzsche.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(50, 'Mr. Jan Strosin DVM', 'vernon32@gmail.com', '123456', NULL, NULL, NULL, 1, '2023-03-31 20:37:31', '2023-03-31 20:37:31', NULL),
(51, 'Adriano Santos', 'adriano@gmail.com', '$2y$10$jX4NGHMoA.fWXAv8b6POwuJ4xYcDQeu6oXJHMwBjrCoFJFJbFZU8a', NULL, NULL, NULL, 1, '2023-04-16 12:38:15', '2023-04-16 12:38:15', NULL),
(52, 'Erik Brayan2', 'erik2@gmail.com', '$2y$10$VJvm9EjV/PCOeL8esE/4PuAwAZhwH8mBXDz35O8hjDxl20iRpn73i', NULL, NULL, NULL, 0, '2023-04-16 13:20:44', '2023-04-16 19:11:31', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
