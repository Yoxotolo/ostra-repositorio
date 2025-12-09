-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/12/2025 às 19:47
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
(3, 'Chill'),
(6, 'Horror'),
(2, 'Pop Rock'),
(4, 'Venturian'),
(5, 'Wild');

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
(2, NULL, 'Weird Autumn Cover', 'Mady Pont', 'Musica boa demais sobre um Outono Bizarro onde alguem desapareceu sem deixar um adeus', 'uploads/musicas/audio_1765217469_03ab0648b178.mp3', 'uploads/music_covers/cover__d75b93361eba.jpeg', 150.00, 'comum', '2025-12-08', '2025-12-08 18:11:09', '2025-12-08 18:11:09', 2, 6, 0, 0, 'ativo', 'publico', NULL),
(3, NULL, 'Weird Autumn', 'MandoPony', 'Buranyna', 'uploads/musicas/audio_1765287270_30844e54585b.mp3', 'uploads/music_covers/cover__f6fd40566a66.jpg', 120.00, 'comum', NULL, '2025-12-09 13:34:30', '2025-12-09 13:34:30', 3, 6, 0, 0, 'ativo', 'publico', NULL),
(4, NULL, 'Silent Partner', 'RFM', '', 'uploads/musicas/audio_1765289991_4ee2aa208917.mp3', 'uploads/music_covers/cover__8ac061aa7764.jpg', 20.00, 'comum', NULL, '2025-12-09 14:19:51', '2025-12-09 14:19:51', 4, 6, 0, 0, 'ativo', 'publico', NULL),
(5, NULL, 'A Himitsu', 'RFM', '', 'uploads/musicas/audio_1765289991_538ddeb86fdb.mp3', 'uploads/music_covers/cover__e4b48d8105fe.jpg', 20.00, 'comum', NULL, '2025-12-09 14:19:51', '2025-12-09 14:19:51', 4, 6, 0, 0, 'ativo', 'publico', NULL),
(6, NULL, 'Beach Party', 'RFM', '', 'uploads/musicas/audio_1765289991_125066d277a3.mp3', 'uploads/music_covers/cover__7c01090702e1.jpg', 20.00, 'comum', NULL, '2025-12-09 14:19:51', '2025-12-09 14:19:51', 4, 6, 0, 0, 'ativo', 'publico', NULL),
(7, NULL, 'The Place Inside', 'RFM', '', 'uploads/musicas/audio_1765289991_c3e9eeb88076.mp3', 'uploads/music_covers/cover__b2cf93031a74.jpg', 20.00, 'comum', NULL, '2025-12-09 14:19:51', '2025-12-09 14:19:51', 4, 6, 0, 0, 'ativo', 'publico', NULL),
(8, NULL, 'Kontekst', 'RFM', '', 'uploads/musicas/audio_1765289991_d5fa3b51ad47.mp3', 'uploads/music_covers/cover__b14e686cd54d.jpg', 20.00, 'comum', NULL, '2025-12-09 14:19:51', '2025-12-09 14:19:51', 4, 6, 0, 0, 'ativo', 'publico', NULL),
(9, NULL, 'Nicolai Heidlas Music', 'RFM', '', 'uploads/musicas/audio_1765290166_b8ffcfbef4d7.mp3', 'uploads/music_covers/cover__4a05d6613a1c.jpg', 50.00, 'comum', NULL, '2025-12-09 14:22:46', '2025-12-09 14:22:46', 5, 6, 0, 0, 'ativo', 'publico', NULL),
(10, NULL, 'New On The Block', 'RFM', '', 'uploads/musicas/audio_1765290166_e92ac441394d.mp3', 'uploads/music_covers/cover__44e62fa53061.jpg', 50.00, 'comum', NULL, '2025-12-09 14:22:46', '2025-12-09 14:22:46', 5, 6, 0, 0, 'ativo', 'publico', NULL),
(11, NULL, 'About That Oldie', 'RFM', '', 'uploads/musicas/audio_1765290166_4785596ef652.mp3', 'uploads/music_covers/cover__4dba95e1736d.jpg', 50.00, 'comum', NULL, '2025-12-09 14:22:46', '2025-12-09 14:22:46', 5, 6, 0, 0, 'ativo', 'publico', NULL),
(12, NULL, 'Green Daze', 'RFM', '', 'uploads/musicas/audio_1765290166_a41d487d4cee.mp3', 'uploads/music_covers/cover__d92cb86c1077.jpg', 50.00, 'comum', NULL, '2025-12-09 14:22:46', '2025-12-09 14:22:46', 5, 6, 0, 0, 'ativo', 'publico', NULL),
(13, NULL, 'Out Here', 'RFM', '', 'uploads/musicas/audio_1765290166_e2b5c0a1208f.mp3', 'uploads/music_covers/cover__d0ad0aa1a057.jpg', 50.00, 'comum', NULL, '2025-12-09 14:22:46', '2025-12-09 14:22:46', 5, 6, 0, 0, 'ativo', 'publico', NULL),
(14, NULL, 'Garage', 'RFM', '', 'uploads/musicas/audio_1765290311_389cf18ce23b.mp3', 'uploads/music_covers/cover__70fc46dd55e1.jpg', 72.00, 'comum', NULL, '2025-12-09 14:25:11', '2025-12-09 14:25:11', 6, 6, 0, 0, 'ativo', 'publico', NULL),
(15, NULL, 'The Creek', 'RFM', '', 'uploads/musicas/audio_1765290311_833b73414a32.mp3', 'uploads/music_covers/cover__5d9d507176c3.jpg', 72.00, 'comum', NULL, '2025-12-09 14:25:11', '2025-12-09 14:25:11', 6, 6, 0, 0, 'ativo', 'publico', NULL),
(16, NULL, 'Jazz Comedy', 'RFM', '', 'uploads/musicas/audio_1765290311_d8d241f54e00.mp3', 'uploads/music_covers/cover__5504d168d995.jpg', 72.00, 'comum', NULL, '2025-12-09 14:25:11', '2025-12-09 14:25:11', 6, 6, 0, 0, 'ativo', 'publico', NULL),
(17, NULL, 'Donors', 'RFM', '', 'uploads/musicas/audio_1765290311_a11fca11a01b.mp3', 'uploads/music_covers/cover__c1f9e9f7bf16.jpg', 72.00, 'comum', NULL, '2025-12-09 14:25:11', '2025-12-09 14:25:11', 6, 6, 0, 0, 'ativo', 'publico', NULL),
(18, NULL, 'Sweet as Honey', 'RFM', '', 'uploads/musicas/audio_1765290311_fa66a4730000.mp3', 'uploads/music_covers/cover__7216793a0b1c.jpg', 72.00, 'comum', NULL, '2025-12-09 14:25:11', '2025-12-09 14:25:11', 6, 6, 0, 0, 'ativo', 'publico', NULL),
(19, NULL, 'The Woods', 'RFM', '', 'uploads/musicas/audio_1765290494_c42043d2269b.mp3', 'uploads/music_covers/cover__4516362cb81e.jpg', 2.00, 'comum', NULL, '2025-12-09 14:28:14', '2025-12-09 14:28:14', 7, 6, 0, 0, 'ativo', 'publico', NULL),
(20, NULL, 'SOLO ACOUSTIC GUITAR', 'RFM', '', 'uploads/musicas/audio_1765290494_80764203739b.mp3', 'uploads/music_covers/cover__104e6f63300a.jpg', 2.00, 'comum', NULL, '2025-12-09 14:28:14', '2025-12-09 14:28:14', 7, 6, 0, 0, 'ativo', 'publico', NULL),
(21, NULL, 'Time Piece', 'RFM', '', 'uploads/musicas/audio_1765290494_d9e57d59ab75.mp3', 'uploads/music_covers/cover__5498a4ce3213.jpg', 2.00, 'comum', NULL, '2025-12-09 14:28:14', '2025-12-09 14:28:14', 7, 6, 0, 0, 'ativo', 'publico', NULL),
(22, NULL, 'Life Is', 'RFM', '', 'uploads/musicas/audio_1765290494_3f17966250a3.mp3', 'uploads/music_covers/cover__443bc1a51a9e.jpg', 2.00, 'comum', NULL, '2025-12-09 14:28:14', '2025-12-09 14:28:14', 7, 6, 0, 0, 'ativo', 'publico', NULL),
(23, NULL, 'Cut It', 'RFM', '', 'uploads/musicas/audio_1765290494_0ae6929a5bf4.mp3', 'uploads/music_covers/cover__c180d012f9af.jpg', 2.00, 'comum', NULL, '2025-12-09 14:28:14', '2025-12-09 14:28:14', 7, 6, 0, 0, 'ativo', 'publico', NULL);

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
(2, 2),
(3, 2),
(4, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(13, 4),
(14, 5),
(15, 5),
(16, 5),
(17, 5),
(18, 5),
(19, 6),
(20, 6),
(21, 6),
(22, 6),
(23, 6);

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
(3, 'Devilish', NULL, 0.00, 6, '', 'uploads/music_covers/project_cover_1765287270_6b4d27c33c77.jpg', '2025-12-09 13:34:30'),
(4, 'Corvinos Gods', NULL, 0.00, 6, '', 'uploads/music_covers/project_cover_1765289991_b68c72f65b3f.jpg', '2025-12-09 14:19:51'),
(5, 'Bend To Wind', NULL, 0.00, 6, '', 'uploads/music_covers/project_cover_1765290166_01de8326619b.jpg', '2025-12-09 14:22:46'),
(6, 'Wild Creature', NULL, 0.00, 6, '', 'uploads/music_covers/project_cover_1765290311_0dbbc7ba9f65.jpg', '2025-12-09 14:25:11'),
(7, 'Old Building', NULL, 0.00, 6, '', 'uploads/music_covers/project_cover_1765290494_cfa0693ae0e0.jpg', '2025-12-09 14:28:14');

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
(6, 'YXT', 'YXT', 'YXT@YXT.com', '$2y$10$G0/Bn5z4OK0rS9KkMBEDl.rFh5kZk2JCyYT8x82H4kY2UM0BEfea2', 'produtor', NULL, NULL, NULL, '2025-12-06 11:04:56', '2025-12-06 11:04:56'),
(7, 'RFM', 'RFM', 'RFM@RFM.com', '$2y$10$/aRBQJykObulapgT3urI1ezfYt6dmDkWvw0v3iodKo/cuMRsZ7x7e', 'produtor', NULL, NULL, NULL, '2025-12-09 12:28:38', '2025-12-09 12:28:38');

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
  MODIFY `id_genero` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `musicas`
--
ALTER TABLE `musicas`
  MODIFY `id_musica` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `cd_projeto` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
