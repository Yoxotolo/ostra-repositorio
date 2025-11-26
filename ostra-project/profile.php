<?php
// Este arquivo agora atua como o CONTROLADOR do perfil.
// Ele carrega os dados e decide qual VIEW (produtor ou usuário comum) deve ser exibida.

// Inicia a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: signin.php");
    exit();
}

include 'db.php';

// Carrega os dados do usuário logado
$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT nm_nome, nm_username, ic_tipo_usuario, ds_biografia, ds_foto_perfil, ds_foto_capa FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Se o usuário não for encontrado (erro de sessão/DB), destrói a sessão e redireciona para login
    session_unset();
    session_destroy();
    header("Location: signin.php?erro=sessao_invalida");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Variáveis para o HTML
$profile_name = htmlspecialchars($user['nm_nome']);
$profile_username = htmlspecialchars($user['nm_username']);
$profile_type = htmlspecialchars($user['ic_tipo_usuario']);
$profile_bio = htmlspecialchars($user['ds_biografia'] ?? 'Adicione uma bio para contar mais sobre você...');
$profile_photo = htmlspecialchars($user['ds_foto_perfil'] ?? 'assets/default-avatar.png'); // Usar um avatar padrão
$profile_cover = htmlspecialchars($user["ds_foto_capa"] ?? 'assets/default-cover.png'); // Usar uma capa padrão

// --- Lógica de Busca de Músicas e Projetos ---
$user_music = [];
$user_projects = [];

// Buscar Projetos
$sql_projects = "SELECT cd_projeto, nm_projeto, ds_foto_capa FROM projetos WHERE fk_id_usuario = ?";
$stmt_projects = $conn->prepare($sql_projects);
$stmt_projects->bind_param("i", $usuario_id);
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();
while ($row = $result_projects->fetch_assoc()) {
    $user_projects[] = $row;
}
$stmt_projects->close();

// Buscar Músicas Avulsas (fk_cd_projeto IS NULL)
// SQL Corrigido (em uma linha para evitar erro de sintaxe)
$sql_music = "SELECT m.id_musica, m.nm_musica, u.nm_nome AS nm_artista, m.ds_arquivo FROM musicas m JOIN usuarios u ON m.fk_id_usuario = u.id_usuario WHERE m.fk_id_usuario = ? AND m.fk_cd_projeto IS NULL";
$stmt_music = $conn->prepare($sql_music);
$stmt_music->bind_param("i", $usuario_id);
$stmt_music->execute();
$result_music = $stmt_music->get_result();
while ($row = $result_music->fetch_assoc()) {
    $user_music[] = $row;
}
$stmt_music->close();

// Contagem para o painel de estatísticas
$music_count = count($user_music) + count($user_projects); // Contagem simplificada de itens principais
$follower_count = 0; // Placeholder
$following_count = 0; // Placeholder

// --- Fim da Lógica de Busca ---

// Lógica para exibir mensagens de sucesso/erro após o upload de fotos
$upload_message = '';
if (isset($_GET['upload']) && $_GET['upload'] == 'success') {
    $type = $_GET['type'] ?? 'profile';
    $type_text = ($type == 'profile') ? 'Foto de Perfil' : 'Foto de Capa';
    $upload_message = "<div class='success-message'>{$type_text} atualizada com sucesso!</div>";
} elseif (isset($_GET['upload']) && $_GET['upload'] == 'error') {
    $error_msg = htmlspecialchars(urldecode($_GET['msg'] ?? 'Erro desconhecido ao fazer upload.'));
    $upload_message = "<div class='error-message'>Erro ao fazer upload: {$error_msg}</div>";
}
// Mensagens de upload de áudio e projeto devem ser tratadas em upload_musicas.php


// --- Lógica de Redirecionamento para a View Correta ---
// Define a variável de sessão para o tipo de usuário (necessário para sidebar.php)
$_SESSION['ic_tipo_usuario'] = $profile_type;

if ($profile_type === 'produtor') {
    include 'profile_view_produtor.php';
} else {
    include 'profile_view_usuario.php';
}

// O restante do código (HTML e JavaScript) foi movido para as views.
?>
