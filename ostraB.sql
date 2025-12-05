-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/12/2025 às 02:09
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ostra`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `generos`
--

CREATE TABLE `generos` (
  `id_genero` bigint(20) UNSIGNED NOT NULL,
  `nm_genero` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `musicas`
--

CREATE TABLE `musicas` (
  `id_musica` bigint(20) UNSIGNED NOT NULL,
  `ds_isrc` varchar(12) DEFAULT NULL,
  `nm_musica` varchar(200) NOT NULL,
  `nm_artista` varchar(200) NOT NULL,
  `ds_descricao` text DEFAULT NULL,
  `ds_arquivo` varchar(255) NOT NULL,
  `ds_foto_capa` varchar(255) DEFAULT NULL,
  `vl_musica` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ic_tipo_venda` enum('exclusiva','multipla') DEFAULT 'multipla',
  `dt_lancamento` date DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fk_cd_projeto` bigint(20) UNSIGNED DEFAULT NULL,
  `fk_id_usuario` bigint(20) UNSIGNED NOT NULL,
  `visualizacoes` int(10) UNSIGNED DEFAULT 0,
  `curtidas` int(10) UNSIGNED DEFAULT 0,
  `ic_status` enum('ativo','inativo','deletado') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `musica_genero`
--

CREATE TABLE `musica_genero` (
  `fk_id_musica` bigint(20) UNSIGNED NOT NULL,
  `fk_id_genero` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `projetos`
--

CREATE TABLE `projetos` (
  `cd_projeto` bigint(20) UNSIGNED NOT NULL,
  `nm_projeto` varchar(200) NOT NULL,
  `ds_projeto` text DEFAULT NULL,
  `vl_projeto` decimal(10,2) NOT NULL,
  `fk_id_usuario` bigint(20) UNSIGNED NOT NULL,
  `ic_tipo_venda` enum('exclusiva','multipla') DEFAULT 'multipla',
  `ds_foto_capa` varchar(255) DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nm_nome` varchar(100) NOT NULL,
  `nm_username` varchar(50) NOT NULL,
  `ds_email` varchar(100) NOT NULL,
  `ds_senha` varchar(255) NOT NULL,
  `ic_tipo_usuario` enum('usuario','produtor') NOT NULL,
  `ds_biografia` text DEFAULT NULL,
  `ds_foto_perfil` varchar(255) DEFAULT NULL,
  `ds_foto_capa` varchar(255) DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nm_nome`, `nm_username`, `ds_email`, `ds_senha`, `ic_tipo_usuario`, `ds_biografia`, `ds_foto_perfil`, `ds_foto_capa`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'miguel', 'miguelperereca', 'miguel@gmail.com', '$2y$10$2/nYmlOAZ2fQVe7Oe5yAdeiBMne94vT4Aci8phKiUnMEpW6Y.VQMi', 'usuario', 'oie eu sou o miguel e gosto de homens sarados', 'https://res.cloudinary.com/dvlitdt5i/image/upload/v1763079836/ostra-project/profiles/profile_1_1763079791.jpg', 'https://res.cloudinary.com/dvlitdt5i/image/upload/v1763080056/ostra-project/covers/cover_1_1763080008.png', '2025-10-31 00:54:10', '2025-11-14 00:26:53'),
(2, 'miguelpro', 'miguelpropro', 'miguel11@gmail.com', '$2y$10$SNAOp6bKkjeRM4notaqx3uTRtU9vHPm40jNtISlKBneh3e4dRVNSC', 'produtor', NULL, 'uploads/profile_photos/profile_2_1763595000.png', 'uploads/cover_photos/cover_2_1763595113.jfif', '2025-10-31 01:05:10', '2025-11-19 23:31:53'),
(3, 'gabi', '___pressaobaixa', 'gabi@gmail.com', '$2y$10$XDzKH/Rw0Mk7rNSso9jZv.FtxPLtrcyC9Sl9NbKrNXLMf3.IMSJTa', 'produtor', NULL, 'uploads/profile_photos/profile_3_1764200355.png', 'uploads/cover_photos/cover_3_1764200402.jfif', '2025-11-26 23:23:43', '2025-11-26 23:40:02'),
(4, 'pintogrosso', 'pintogrosso69', 'migueldsilva272006@gmail.com', '$2y$10$H8ZfQBhAxXafzzsMSjQGhe7dDXggtwnoxjAQPPIaAnTU2sTj3mBtq', 'produtor', NULL, 'uploads/profile_photos/perfil_4_1764887753.jpg', 'uploads/profile_banner/perfil_4_1764887730.gif', '2025-12-04 22:32:16', '2025-12-04 22:35:53');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `generos`
--
ALTER TABLE `generos`
  ADD PRIMARY KEY (`id_genero`),
  ADD UNIQUE KEY `nm_genero` (`nm_genero`);

--
-- Índices de tabela `musicas`
--
ALTER TABLE `musicas`
  ADD PRIMARY KEY (`id_musica`),
  ADD KEY `idx_usuario` (`fk_id_usuario`),
  ADD KEY `idx_projeto` (`fk_cd_projeto`),
  ADD KEY `idx_status` (`ic_status`),
  ADD KEY `idx_criacao` (`dt_criacao`),
  ADD KEY `idx_usuario_status` (`fk_id_usuario`,`ic_status`);

--
-- Índices de tabela `musica_genero`
--
ALTER TABLE `musica_genero`
  ADD PRIMARY KEY (`fk_id_musica`,`fk_id_genero`),
  ADD KEY `fk_id_genero` (`fk_id_genero`);

--
-- Índices de tabela `projetos`
--
ALTER TABLE `projetos`
  ADD PRIMARY KEY (`cd_projeto`),
  ADD KEY `fk_id_usuario` (`fk_id_usuario`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nm_username` (`nm_username`),
  ADD UNIQUE KEY `ds_email` (`ds_email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `generos`
--
ALTER TABLE `generos`
  MODIFY `id_genero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `musicas`
--
ALTER TABLE `musicas`
  MODIFY `id_musica` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `cd_projeto` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `musicas`
--
ALTER TABLE `musicas`
  ADD CONSTRAINT `musicas_ibfk_1` FOREIGN KEY (`fk_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabelas `musica_genero`
--
ALTER TABLE `musica_genero`
  ADD CONSTRAINT `musica_genero_ibfk_1` FOREIGN KEY (`fk_id_musica`) REFERENCES `musicas` (`id_musica`) ON DELETE CASCADE,
  ADD CONSTRAINT `musica_genero_ibfk_2` FOREIGN KEY (`fk_id_genero`) REFERENCES `generos` (`id_genero`) ON DELETE CASCADE;

--
-- Restrições para tabelas `projetos`
--
ALTER TABLE `projetos`
  ADD CONSTRAINT `projetos_ibfk_1` FOREIGN KEY (`fk_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
