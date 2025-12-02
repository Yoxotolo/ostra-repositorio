<?php
// Este arquivo contém a VIEW completa para o perfil de PRODUTOR.
// Ele assume que todas as variáveis de dados (user, profile_name, user_music, etc.)
// já foram carregadas pelo arquivo principal (profile.php).

// Variáveis de dados necessárias:
// $profile_name, $profile_username, $profile_type, $profile_bio, $profile_photo, $profile_cover
// $user_music, $user_projects, $music_count, $follower_count, $following_count
// $upload_message

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - OSTRA</title>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style/profile.css">

</head>
<body class="feed-page">
<?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <?php include 'header.php'; ?>

        <!-- Profile Content -->
        <div class="profile-content">
            <?php echo $upload_message; ?>
            <!-- Cover Photo -->
            <div class="profile-cover">
                <img src="<?php echo $profile_cover; ?>" alt="Cover Photo" class="cover-image" id="coverImage">
                <form id="coverUploadForm" action="upload_handler.php" method="POST" enctype="multipart/form-data" style="display: none;">
    <input type="hidden" name="action" value="upload_image">
    <input type="hidden" name="image_type" value="cover">
    <input type="file" id="coverInput" name="banner" accept="image/*" onchange="document.getElementById('coverUploadForm' ).submit();">
</form>
<button class="edit-cover-btn" id="editCoverBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Editar Capa
                </button>
                
            </div>

            <!-- Profile Info Section -->
            <div class="profile-info-section">
                <div class="profile-avatar-container">
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar">
                            <img src="<?php echo $profile_photo; ?>" alt="Profile Picture" class="avatar-image" id="avatarImage">
                        </div>
                        <form id="avatarUploadForm" action="upload_handler.php" method="POST" enctype="multipart/form-data" style="display: none;">
    <input type="hidden" name="action" value="upload_image">
    <input type="hidden" name="image_type" value="profile">
    <input type="file" id="avatarInput" name="foto" accept="image/*" onchange="document.getElementById('avatarUploadForm' ).submit();">
</form>
<button class="edit-avatar-btn" id="editAvatarBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        
                    </div>
                </div>

                <div class="profile-info">
                    <div class="profile-header">
                        <div>
                        <div class="status">
                            <div>
                                <h1 class="profile-name" id="profileName"><?php echo $profile_name; ?></h1>
                                <p class="profile-username" id="profileUsername">@<?php echo $profile_username; ?></p>
                            </div>

                            <div class="tipo-conta">
                                <span class="profile-badge"><?php echo ucfirst($profile_type  ); ?></span>
                            </div>
                            
                            <div class="profile-stats">
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo $music_count; ?></span>
                                    <span class="stat-label">Músicas</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo $follower_count; ?></span>
                                    <span class="stat-label">Seguidores</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo $following_count; ?></span>
                                    <span class="stat-label">Seguindo</span>
                                </div>
                            </div>
                        </div>
                        </div>
                        <a href="configuracoes.php" class="btn-edit-profile">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.5 2.50001C18.8978 2.10219 19.4374 1.87869 20 1.87869C20.5626 1.87869 21.1022 2.10219 21.5 2.50001C21.8978 2.89784 22.1213 3.4374 22.1213 4.00001C22.1213 4.56262 21.8978 5.10219 21.5 5.50001L12 15L8 16L9 12L18.5 2.50001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Editar Perfil
                        </a>
                    </div>

                    <div class="profile-bio">
                        <div class="bio-display" id="bioDisplay">
                            <p><?php echo $profile_bio; ?></p>
                        </div>
                        <div class="bio-edit" id="bioEdit" style="display: none;">
                            <textarea 
                                class="bio-textarea" 
                                id="bioTextarea" 
                                placeholder="Escreva sua bio aqui..."
                                maxlength="200"
                            ></textarea>
                            <div class="bio-actions">
                                <span class="char-count" id="charCount">0/200</span>
                                <button class="btn-save-bio" onclick="saveBio( )">Salvar Bio</button>
                                <button class="btn-cancel-bio" onclick="cancelBio()">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Tabs -->
            <div class="profile-tabs">
                <button class="tab-btn active" onclick="switchTab('musicas' )">Músicas</button>
                <button class="tab-btn" onclick="switchTab('playlists')">Playlists</button>
                <button class="tab-btn" onclick="switchTab('curtidas')">Curtidas</button>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <div id="tab-musicas" class="tab-pane active">
                    <p class="upload-callout">Para enviar músicas ou criar projetos, use o link <strong>UPLOAD MÚSICAS</strong> na barra lateral.</p>
                    
                    <h2 class="config-title-small" style="margin-top: 40px;">Seus Projetos (Álbuns)</h2>
                    <?php if (empty($user_projects)): ?>
                        <p>Você ainda não tem projetos criados.</p>
                    <?php else: ?>
                        <div class="project-list">
                            <?php foreach ($user_projects as $project): ?>
                                <div class="project-item">
                                    <img src="<?php echo htmlspecialchars($project['ds_foto_capa'] ?? 'assets/default-album.svg'); ?>" alt="Capa do Projeto" class="project-cover">
                                    <div class="project-info">
                                        <h3><?php echo htmlspecialchars($project['nm_projeto']); ?></h3>
                                        <p>ID: #<?php echo $project['cd_projeto']; ?></p>
                                        <!-- Aqui você pode adicionar um link para a página do projeto -->
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <h2 class="config-title-small" style="margin-top: 40px;">Músicas Avulsas</h2>
                    <?php if (empty($user_music)): ?>
                        <p>Você ainda não tem músicas avulsas enviadas.</p>
                    <?php else: ?>
                        <div class="music-list">
                            <?php foreach ($user_music as $music): ?>
                                <div class="music-item">
                                    <div class="music-item-cover">
                                        <img src="<?php echo htmlspecialchars($music['ds_foto_capa'] ?? 'assets/default-album.svg'); ?>" alt="Capa" class="music-cover-image">
                                    </div>
                                    <div class="music-item-info">
                                        <h3><?php echo htmlspecialchars($music['nm_musica']); ?></h3>
                                        <p class="music-artist"><?php echo htmlspecialchars($music['nm_artista']); ?></p>
                                        <p class="music-date">Lançamento: <?php echo date('d/m/Y', strtotime($music['dt_lancamento'])); ?></p>
                                        <p class="music-type">Tipo: <?php echo ($music['ic_tipo_compra'] === 'unica') ? 'Compra Única' : 'Compra Múltipla'; ?></p>
                                        <?php if (!empty($music['ds_descricao'])): ?>
                                            <p class="music-description"><?php echo htmlspecialchars(substr($music['ds_descricao'], 0, 100)); ?>...</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="music-item-player">
                                        <audio controls src="<?php echo htmlspecialchars($music['ds_arquivo']); ?>"></audio>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="tab-playlists" class="tab-pane">
                    <div class="empty-tab-state">
                        <p>Funcionalidade de Playlists em desenvolvimento.</p>
                    </div>
                </div>
                <div id="tab-curtidas" class="tab-pane">
                    <div class="empty-tab-state">
                        <p>Nenhuma curtida ainda.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Adicionar event listeners quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica para o botão de edição de capa
        const editCoverBtn = document.getElementById('editCoverBtn');
        const coverInput = document.getElementById('coverInput');
        if (editCoverBtn && coverInput) {
            editCoverBtn.addEventListener('click', function() {
                coverInput.click();
            });
        }

        // Lógica para o botão de edição de avatar
        const editAvatarBtn = document.getElementById('editAvatarBtn');
        const avatarInput = document.getElementById('avatarInput');
        if (editAvatarBtn && avatarInput) {
            editAvatarBtn.addEventListener('click', function() {
                avatarInput.click();
            });
        }
    });

    // Função para editar a bio
    function editBio() {
        const bioDisplay = document.getElementById('bioDisplay');
        const bioEdit = document.getElementById('bioEdit');
        const editBioBtn = document.getElementById('editBioBtn');
        const bioTextarea = document.getElementById('bioTextarea');

        if (bioDisplay && bioEdit && editBioBtn && bioTextarea) {
            bioTextarea.value = bioDisplay.querySelector('p').textContent.trim();
            bioDisplay.style.display = 'none';
            editBioBtn.style.display = 'none';
            bioEdit.style.display = 'block';
            updateCharCount();
        }
    }

    // Função para salvar a bio (Ainda precisa de um handler PHP)
    function saveBio() {
        const bioTextarea = document.getElementById('bioTextarea');
        const newBio = bioTextarea.value.trim();
        
        // Aqui você faria uma requisição AJAX para um handler PHP para salvar a bio
        // Por enquanto, apenas atualiza a visualização
        document.getElementById('bioDisplay').querySelector('p').textContent = newBio;
        
        document.getElementById('bioDisplay').style.display = 'block';
        document.getElementById('bioEdit').style.display = 'none';
        document.getElementById('editBioBtn').style.display = 'flex';
        // O handler PHP cuidaria de salvar a bio e recarregar a página ou usar AJAX para atualizar a bio
        // Para fins de demonstração, o usuário deve salvar a bio separadamente
    }

    // Cancel Bio Edit
    function cancelBio() {
        document.getElementById('bioDisplay').style.display = 'block';
        document.getElementById('bioEdit').style.display = 'none';
        document.getElementById('editBioBtn').style.display = 'flex';
        document.getElementById('bioTextarea').value = '';
    }

    // Update Character Count
    function updateCharCount() {
        const textarea = document.getElementById('bioTextarea');
        const charCount = document.getElementById('charCount');
        if (textarea && charCount) {
            charCount.textContent = `${textarea.value.length}/200`;
        }
    }

    // Switch Tabs
    function switchTab(tabName) {
        const tabs = document.querySelectorAll('.tab-btn');
        tabs.forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        
        // Lógica para mostrar o conteúdo da aba
        const panes = document.querySelectorAll('.tab-pane');
        panes.forEach(pane => pane.classList.remove('active'));
        
        const targetPane = document.getElementById('tab-' + tabName);
        if (targetPane) {
            targetPane.classList.add('active');
        }
    }
</script>

</body>
</html>
