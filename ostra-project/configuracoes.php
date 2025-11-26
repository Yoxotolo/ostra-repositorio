<?php


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

$usuario_id = $_SESSION["usuario_id"];
$update_error = '';
$update_success = '';

// --- Lógica de Edição de Perfil ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $new_name = trim($_POST['editName']);
    $new_username = trim($_POST['editUsername']);
    $new_email = trim($_POST['editEmail']);
    $new_password = $_POST['editPassword'];
    $new_bio = trim($_POST['editBio']);

    // Recarrega os dados atuais do usuário para comparação
    $stmt_current = $conn->prepare("SELECT nm_nome, nm_username, ds_email, ds_biografia FROM usuarios WHERE id_usuario = ?");
    $stmt_current->bind_param("i", $usuario_id);
    $stmt_current->execute();
    $current_user_data = $stmt_current->get_result()->fetch_assoc();
    $stmt_current->close();

    $update_fields = [];
    $bind_types = '';
    $bind_params = [];

    // 1. Validação e preparação dos campos
    if (!empty($new_name) && $new_name !== $current_user_data['nm_nome']) {
        $update_fields[] = "nm_nome = ?";
        $bind_types .= 's';
        $bind_params[] = &$new_name;
    }

    if (!empty($new_username) && $new_username !== $current_user_data['nm_username']) {
        $stmt_check_username = $conn->prepare("SELECT id_usuario FROM usuarios WHERE nm_username = ? AND id_usuario != ?");
        $stmt_check_username->bind_param("si", $new_username, $usuario_id);
        $stmt_check_username->execute();
        if ($stmt_check_username->get_result()->num_rows > 0) {
            $update_error = "Nome de usuário já está em uso.";
        }
        $stmt_check_username->close();
        if (empty($update_error)) {
            $update_fields[] = "nm_username = ?";
            $bind_types .= 's';
            $bind_params[] = &$new_username;
        }
    }

    if (!empty($new_email) && $new_email !== $current_user_data['ds_email']) {
        $stmt_check_email = $conn->prepare("SELECT id_usuario FROM usuarios WHERE ds_email = ? AND id_usuario != ?");
        $stmt_check_email->bind_param("si", $new_email, $usuario_id);
        $stmt_check_email->execute();
        if ($stmt_check_email->get_result()->num_rows > 0) {
            $update_error = "E-mail já está em uso.";
        }
        $stmt_check_email->close();
        if (empty($update_error)) {
            $update_fields[] = "ds_email = ?";
            $bind_types .= 's';
            $bind_params[] = &$new_email;
        }
    }

    if (!empty($new_password)) {
        $confirm_password = $_POST['confirmEditPassword'];
        if ($new_password !== $confirm_password) {
            $update_error = "As novas senhas não coincidem.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_fields[] = "ds_senha = ?";
            $bind_types .= 's';
            $bind_params[] = &$hashed_password;
        }
    }

    if ($new_bio !== $current_user_data['ds_biografia']) {
        $update_fields[] = "ds_biografia = ?";
        $bind_types .= 's';
        $bind_params[] = &$new_bio;
    }

    // 2. Execução da atualização
    if (empty($update_error) && !empty($update_fields)) {
        $sql_update = "UPDATE usuarios SET " . implode(', ', $update_fields) . " WHERE id_usuario = ?";
        $bind_types .= 'i';
        $bind_params[] = &$usuario_id;

        $stmt_update = $conn->prepare($sql_update);
        call_user_func_array([$stmt_update, 'bind_param'], array_merge([$bind_types], $bind_params));

        if ($stmt_update->execute()) {
            if (in_array("nm_nome = ?", $update_fields)) {
                $_SESSION['usuario_nome'] = $new_name;
            }
            header("Location: configuracoes.php?update=success");
            exit();
        } else {
            $update_error = "Erro ao atualizar o perfil: " . $stmt_update->error;
        }
        $stmt_update->close();
    } elseif (empty($update_error) && empty($update_fields)) {
        $update_error = "Nenhuma alteração foi feita.";
    }
}

// Recarrega os dados do usuário para exibir na página
$sql = "SELECT nm_nome, nm_username, ds_email, ic_tipo_usuario, ds_biografia, ds_foto_perfil, ds_foto_capa FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
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
$profile_email = htmlspecialchars($user['ds_email']);
$profile_bio = htmlspecialchars($user['ds_biografia'] ?? '');



?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - OSTRA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="config-page feed-page"> <!-- Adicionei feed-page para herdar o flexbox, mas config-page deve anular o background -->
<?php include 'sidebar-B.php'; ?>

     

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="search-container">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input type="text" class="search-input" placeholder="Search Bar . . .">
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#2a2a2a"/>
                        <circle cx="16" cy="12" r="5" fill="#00D9D9"/>
                        <path d="M6 26C6 21 10 18 16 18C22 18 26 21 26 26" fill="#00D9D9"/>
                    </svg>
                </div>
            </div>
        </header>

        <!-- Configurações Content -->
        <div class="config-container">

    <!-- #region Mudar de Aba -->
            <div class="config-menu">
                <a href="?tab=perfil" class="config-menu-item <?php echo (!isset($_GET['tab'] ) || $_GET['tab'] == 'perfil') ? 'active' : ''; ?>">Editar Perfil</a>
                <a href="?tab=midia" class="config-menu-item <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'midia') ? 'active' : ''; ?>">Mídia (Fotos)</a>
            </div>
    <!-- #endregion -->

    <!-- #region Conteudo da area de configurar perfil-->

            <div class="config-content-area-A">

        <!-- #region Mensagem se foi atualizado-->

                <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
                    <div class="success-message">Perfil atualizado com sucesso!</div>
                <?php endif; ?>
                <?php if (isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
                    <div class="success-message">Upload de mídia realizado com sucesso!</div>
                <?php endif; ?>
                <?php if (!empty($update_error)): ?>
                    <div class="error-message"><?php echo $update_error; ?></div>
                <?php endif; ?>
        <!-- #endregion Mensagem se foi atualizado-->

                <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'perfil'): ?>
                    <h1 class="config-title">Editar Perfil</h1>
                    <p class="config-subtitle">Gerencie suas informações pessoais, e-mail, nome de usuário e senha.</p>
                    
                    <form action="configuracoes.php" method="POST" class="config-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group-A">
                            <label for="editName">Nome Completo</label>
                            <input class="Inputa" type="text" id="editName" name="editName" value="<?php echo $profile_name; ?>" required>
                        </div>
                        
                        <div class="form-group-A">
                            <label for="editUsername">Nome de Usuário (@)</label>
                            <input class="Inputa" type="text" id="editUsername" name="editUsername" value="<?php echo $profile_username; ?>" required>
                        </div>
                        
                        <div class="form-group-A">
                            <label for="editEmail">E-mail</label>
                            <input class="Inputa" type="email" id="editEmail" name="editEmail" value="<?php echo $profile_email; ?>" required>
                        </div>
                        
                        <div class="form-group-A">
                            <label for="editBio">Biografia</label>
                            <textarea class="Inputa" id="editBio" name="editBio" maxlength="200"><?php echo $profile_bio; ?></textarea>
                            <small>Máximo de 200 caracteres.</small>
                        </div>
                        
                        <h2 class="config-title-small">Alterar Senha (Opcional)</h2>
                        
                        <div class="form-group-A">
                            <label for="editPassword">Nova Senha</label>
                            <input class="Inputa" type="password" id="editPassword" name="editPassword">
                        </div>
                        
                        <div class="form-group-A">
                            <label for="confirmEditPassword">Confirmar Nova Senha</label>
                            <input class="Inputa" type="password" id="confirmEditPassword" name="confirmEditPassword">
                        </div>
                        
                        <button type="submit" class="btn-primary">Salvar Alterações</button>
                    </form>
                <?php elseif ($_GET['tab'] == 'midia'): ?>
                    <h1 class="config-title">Mídia (Fotos de Perfil e Capa)</h1>
                    <p class="config-subtitle">O upload de fotos de perfil e capa agora é feito diretamente na página de <a href="profile.php">Perfil</a>.</p>
                <?php endif; ?>
            </div>

    <!-- #endregion -->
        </div>
    </main>
</body>
</html>
