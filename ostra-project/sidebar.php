<?php
// Este arquivo contém o código HTML e PHP para a sidebar de navegação.
// Ele assume que a variável de sessão $_SESSION['ic_tipo_usuario'] está definida.

// Variáveis esperadas:
// $current_page (string) - Nome do arquivo atual (ex: 'profile.php', 'feed.php')
// $is_produtor (boolean) - true se o usuário for 'produtor'

// Garante que a sessão está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_produtor = ($_SESSION['ic_tipo_usuario'] ?? '') === 'produtor';
$current_page = basename($_SERVER['PHP_SELF']);

?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="assets/AFFGGE/logo-white.svg" alt="OSTRA Icon" class="sidebar-logo-icon">
            <img src="assets/AFFGGE/lettering-white.svg" alt="OSTRA" class="sidebar-logo-text">
        </div>
    </div>

    <link rel="stylesheet" href="style/sidebar.css">

    <nav class="sidebar-nav">
        <a href="feed.php" class="nav-item <?php echo ($current_page === 'feed.php') ? 'active' : ''; ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>INICIO</span>
        </a>

        <a href="profile.php" class="nav-item <?php echo ($current_page === 'profile.php' || $current_page === 'profile_view_produtor.php' || $current_page === 'profile_view_usuario.php' ) ? 'active' : ''; ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>PERFIL</span>
        </a>
        
        <?php if ($is_produtor ): ?>
            <a href="upload_musicas.php" class="nav-item <?php echo ($current_page === 'upload_musicas.php') ? 'active' : ''; ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>UPLOAD MÚSICAS</span>
            </a>
        <?php endif; ?>

        <a href="#" class="nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>EXPLORAR</span>
        </a>

        <a href="#" class="nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>SEGUIDOS</span>
        </a>
        
        <a href="configuracoes.php" class="nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C11.47 2 11.02 2.38 10.94 2.9L10.5 5.1C9.9 5.3 9.3 5.6 8.76 5.98L6.6 5.1C6.07 4.9 5.5 5.1 5.1 5.5L3.1 8.9C2.7 9.3 2.7 9.9 3.1 10.3L4.7 11.5C4.7 11.7 4.7 11.8 4.7 12C4.7 12.2 4.7 12.3 4.7 12.5L3.1 13.7C2.7 14.1 2.7 14.7 3.1 15.1L5.1 18.5C5.5 18.9 6.07 19.1 6.6 18.9L8.76 18.02C9.3 18.4 9.9 18.7 10.5 18.9L10.94 21.1C11.02 21.62 11.47 22 12 22C12.53 22 12.98 21.62 13.06 21.1L13.5 18.9C14.1 18.7 14.7 18.4 15.24 18.02L17.4 18.9C17.93 19.1 18.5 18.9 18.9 18.5L20.9 15.1C21.3 14.7 21.3 14.1 20.9 13.7L19.3 12.5C19.3 12.3 19.3 12.2 19.3 12C19.3 11.8 19.3 11.7 19.3 11.5L20.9 10.3C21.3 9.9 21.3 9.3 20.9 8.9L18.9 5.5C18.5 5.1 17.93 4.9 17.4 5.1L15.24 5.98C14.7 5.6 14.1 5.3 13.5 5.1L13.06 2.9C12.98 2.38 12.53 2 12 2ZM12 15C10.34 15 9 13.66 9 12C9 10.34 10.34 9 12 9C13.66 9 15 10.34 15 12C15 13.66 13.66 15 12 15Z" fill="currentColor"/>
            </svg>
            <span>CONFIGURAÇÕES</span>
        </a>
    </nav>

    <div class="sidebar-divider"></div>
        <a href="logout.php" class="nav-item" style="margin-top: 20px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>SAIR</span>
        </a>
</aside>
