<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config_up.php';

// verificar se recebeu o id do projeto
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Projeto não encontrado.");
}

$idProjeto = intval($_GET['id']);

// puxar dados do projeto
$sqlProjeto = $pdo->prepare("
    SELECT p.*, u.nm_nome AS nome_usuario, u.nm_username
    FROM projetos p
    JOIN usuarios u ON u.id_usuario = p.fk_id_usuario
    WHERE p.cd_projeto = :id
");
$sqlProjeto->execute(['id' => $idProjeto]);
$projeto = $sqlProjeto->fetch();

if (!$projeto) {
    die("Projeto não encontrado.");
}

// puxar músicas do projeto
$sqlMusicas = $pdo->prepare("
    SELECT m.*, GROUP_CONCAT(g.nm_genero SEPARATOR ', ') AS generos
    FROM musicas m
    LEFT JOIN musica_genero mg ON mg.fk_id_musica = m.id_musica
    LEFT JOIN generos g ON g.id_genero = mg.fk_id_genero
    WHERE m.fk_cd_projeto = :id
    GROUP BY m.id_musica
    ORDER BY m.dt_criacao DESC
");
$sqlMusicas->execute(['id' => $idProjeto]);
$musicas = $sqlMusicas->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($projeto['nm_projeto']) ?></title>

<link rel="stylesheet" href="styles.css">

<style>
.page-container {
    width: 90%;
    margin: 0 auto;
}

.project-header {
    display: flex;
    gap: 20px;
    padding: 20px;
    align-items: center;
}

.project-cover {
    width: 220px;
    height: 220px;
    border-radius: 12px;
    object-fit: cover;
}

.music-list {
    margin-top: 30px;
}

.music-item {
    display: flex;
    background: #1f1f1f;
    margin-bottom: 15px;
    padding: 15px;
    border-radius: 12px;
    gap: 20px;
    align-items: center;
}

.music-cover {
    width: 90px;
    height: 90px;
    border-radius: 8px;
    object-fit: cover;
}

.music-info {
    display: flex;
    width: 100%;
}

.music-info p {
    margin: 1%;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.music-info h3 {
    margin: 0 0 5px;
}

audio {
    width: 220px;
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

        <?php include 'header.php'; ?>

<div class="page-container">

    <div class="project-header">
        <img src="<?= $projeto['ds_foto_capa'] ?>" class="project-cover">

        <div>
            <h1><?= htmlspecialchars($projeto['nm_projeto']) ?></h1>
            <p><strong>Criado por:</strong> <?= $projeto['nome_usuario'] ?> (<?= $projeto['nm_username'] ?>)</p>
            <p><strong>Tipo de venda:</strong> <?= $projeto['ic_tipo_venda'] ?></p>
            <p><?= nl2br($projeto['ds_projeto']) ?></p>
        </div>
    </div>

    <h2>Músicas do Projeto</h2>

    <div class="music-list">

        <?php if (count($musicas) == 0): ?>
            <p>Este projeto ainda não possui músicas.</p>
        <?php endif; ?>

        <?php foreach ($musicas as $m): ?>
        <div class="music-item">

            <img src="<?= $m['ds_foto_capa'] ?>" class="music-cover">

            <div style="display: flex; flex-direction: column; width: 100%">
                                    <h3><?= htmlspecialchars($m['nm_musica']) ?></h3>
                <div class="music-info">
                    <p style="display: flex; flex-direction:column;"><strong>Artista:</strong> <?= $m['nm_artista'] ?></p>
                    <p><strong>Gêneros:</strong> <?= $m['generos'] ?: '—' ?></p>
                    <p><strong>Descrição:</strong> <?= nl2br($m['ds_descricao']) ?></p>
                    <p><strong>Lançamento:</strong> <?= $m['dt_criacao'] ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($m['vl_musica'], 2, ',', '.') ?></p>
                </div>

                <audio controls style="width: 100%">
                    <source src="<?= $m['ds_arquivo'] ?>" type="audio/mpeg">
                </audio>
            </div>


        </div>
        <?php endforeach; ?>

    </div>

</div>

</main>
</body>
</html>
