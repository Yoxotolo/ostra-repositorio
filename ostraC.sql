-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/12/2025 às 22:18
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

--
-- Despejando dados para a tabela `generos`
--

INSERT INTO `generos` (`id_genero`, `nm_genero`) VALUES
(1, 'relaxante');

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
  `ic_tipo_venda` enum('comum','limitada','unica') DEFAULT 'comum',
  `dt_lancamento` date DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fk_cd_projeto` bigint(20) UNSIGNED DEFAULT NULL,
  `fk_id_usuario` bigint(20) UNSIGNED NOT NULL,
  `visualizacoes` int(10) UNSIGNED DEFAULT 0,
  `curtidas` int(10) UNSIGNED DEFAULT 0,
  `ic_status` enum('ativo','inativo','deletado') DEFAULT 'ativo',
  `ic_visibilidade` enum('publico','privado','agendado') DEFAULT 'publico',
  `qt_limite_vendas` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `musicas`
--

INSERT INTO `musicas` (`id_musica`, `ds_isrc`, `nm_musica`, `nm_artista`, `ds_descricao`, `ds_arquivo`, `ds_foto_capa`, `vl_musica`, `ic_tipo_venda`, `dt_lancamento`, `dt_criacao`, `dt_atualizacao`, `fk_cd_projeto`, `fk_id_usuario`, `visualizacoes`, `curtidas`, `ic_status`, `ic_visibilidade`, `qt_limite_vendas`) VALUES
(1, NULL, 'sadasdsadas', 'hfghfdgdfg', '1234', 'uploads/musicas/audio_1764967793_039a8ac7b30e.mp3', 'uploads/music_covers/cover__5c435420540e.jpeg', 10.00, 'comum', '2025-12-05', '2025-12-05 20:49:53', '2025-12-05 20:49:53', 1, 5, 0, 0, 'ativo', 'publico', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `musica_genero`
--

CREATE TABLE `musica_genero` (
  `fk_id_musica` bigint(20) UNSIGNED NOT NULL,
  `fk_id_genero` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `musica_genero`
--

INSERT INTO `musica_genero` (`fk_id_musica`, `fk_id_genero`) VALUES
(1, 1);

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

--
-- Despejando dados para a tabela `projetos`
--

INSERT INTO `projetos` (`cd_projeto`, `nm_projeto`, `ds_projeto`, `vl_projeto`, `fk_id_usuario`, `ic_tipo_venda`, `ds_foto_capa`, `dt_criacao`) VALUES
(1, 'testabe', NULL, 0.00, 5, '', 'uploads/music_covers/project_cover_1764967793_bb090468804c.jpg', '2025-12-05 20:49:53');

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
(5, 'tester', 'tester', 'tester@tester.com', '$2y$10$rGKmJxXJ3wXsgs0U8inetuBXPpbe7KDOHmcl5MN/05EtZ9jIIs7Za', 'produtor', NULL, NULL, NULL, '2025-12-05 18:49:06', '2025-12-05 18:49:06');

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
  MODIFY `id_genero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `musicas`
--
ALTER TABLE `musicas`
  MODIFY `id_musica` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `cd_projeto` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
