<?php
// Arquivo para upload de músicas com drag-and-drop

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {
    header("Location: signin.php");
    exit();
}

include 'db.php';

$usuario_id = $_SESSION["usuario_id"];

// Verifica se o usuário é produtor
$sql_check = "SELECT ic_tipo_usuario FROM usuarios WHERE id_usuario = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$user_data = $result_check->fetch_assoc();
$stmt_check->close();

if ($user_data['ic_tipo_usuario'] !== 'produtor') {
    header("Location: profile.php");
    exit();
}

// Mensagens de feedback
$upload_message = '';
if (isset($_GET['upload']) && $_GET['upload'] == 'success') {
    $upload_message = "<div class='success-message'>Arquivo de áudio recebido com sucesso! Preencha os detalhes da música.</div>";
} elseif (isset($_GET['upload']) && $_GET['upload'] == 'error') {
    $error_msg = htmlspecialchars(urldecode($_GET['msg'] ?? 'Erro desconhecido ao fazer upload.'));
    $upload_message = "<div class='error-message'>Erro ao fazer upload: {$error_msg}</div>";
}

// Recupera o arquivo de áudio da sessão se existir
$audio_file = isset($_SESSION['temp_audio_file']) ? $_SESSION['temp_audio_file'] : null;
$audio_filename = isset($_SESSION['temp_audio_filename']) ? $_SESSION['temp_audio_filename'] : null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Músicas - OSTRA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="feed-page">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
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

        <div class="upload-container">
            <?php echo $upload_message; ?>

            <h1 class="upload-title">Upload de Músicas</h1>
            <p class="upload-subtitle">Arraste seu arquivo de áudio ou clique para selecionar</p>

            <div class="upload-section" id="uploadSection">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 8L12 3L7 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 3V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h2>Arraste seu arquivo aqui</h2>
                <p>ou clique para selecionar um arquivo de áudio</p>
                <p class="supported-formats">Formatos suportados: MP3, WAV, OGG, M4A, FLAC</p>
                <input type="file" id="fileInput" class="file-input" accept="audio/*">
            </div>

            <div id="fileInfo" class="file-info" style="display: none;">
                <h3>Arquivo Selecionado</h3>
                <div id="fileList"></div>
            </div>

            <div id="audioPreview" class="audio-preview" style="display: none;">
                <audio id="audioPlayer" controls></audio>
            </div>

            <div class="button-group" id="buttonGroup" style="display: none;">
                <button class="btn-secondary" onclick="clearFile( )">Limpar</button>
                <button class="btn-primary" onclick="proceedToMetadata()">Continuar</button>
            </div>
        </div>
    </main>

    <script>
        const uploadSection = document.getElementById('uploadSection');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileList = document.getElementById('fileList');
        const audioPreview = document.getElementById('audioPreview');
        const audioPlayer = document.getElementById('audioPlayer');
        const buttonGroup = document.getElementById('buttonGroup');

        let selectedFile = null;

        // Click para selecionar arquivo
        uploadSection.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadSection.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadSection.classList.add('drag-over');
        });

        uploadSection.addEventListener('dragleave', () => {
            uploadSection.classList.remove('drag-over');
        });

        uploadSection.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadSection.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        // Mudança de arquivo
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            // Validar tipo de arquivo
            const validTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/flac', 'audio/x-m4a'];
            
            if (!validTypes.includes(file.type)) {
                alert('Formato de arquivo não suportado. Use MP3, WAV, OGG, M4A ou FLAC.');
                return;
            }

            // Validar tamanho (máximo 100MB)
            const maxSize = 100 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Arquivo muito grande. Máximo permitido: 100MB');
                return;
            }

            selectedFile = file;

            // Atualizar informações do arquivo
            fileList.innerHTML = `
                <div class="file-item">
                    <span class="file-item-name">${file.name}</span>
                    <span class="file-item-size">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                </div>
            `;

            fileInfo.style.display = 'block';

            // Preview de áudio
            const url = URL.createObjectURL(file);
            audioPlayer.src = url;
            audioPreview.style.display = 'block';

            // Mostrar botões
            buttonGroup.style.display = 'flex';

            // Esconder upload section
            uploadSection.style.display = 'none';
        }

        function clearFile() {
            selectedFile = null;
            fileInput.value = '';
            fileInfo.style.display = 'none';
            audioPreview.style.display = 'none';
            buttonGroup.style.display = 'none';
            uploadSection.style.display = 'block';
        }

        function proceedToMetadata() {
            if (!selectedFile) {
                alert('Selecione um arquivo de áudio primeiro.');
                return;
            }

            // Criar FormData para enviar o arquivo
            const formData = new FormData();
            formData.append('action', 'upload_audio');
            formData.append('audio_file', selectedFile);

            // Mostrar loading
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'Enviando...';

            // Enviar arquivo para servidor
            fetch('upload_musicas_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Adicionar tratamento para respostas não-JSON (como HTML de erro)
                if (!response.ok) {
                    // Se a resposta não for OK (ex: 500 Internal Server Error),
                    // tenta ler como texto para mostrar o erro do servidor
                    return response.text().then(text => {
                        throw new Error('Erro do Servidor: ' + text.substring(0, 100) + '...');
                    });
                }
                // Tenta ler como JSON
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Caminho relativo simples, já que os arquivos estão na mesma pasta
                   window.location.href = 'upload_musicas_metadatas.php'; 
                } else {
                    alert('Erro ao enviar arquivo: ' + (data.message || 'Erro desconhecido'));
                    btn.disabled = false;
                    btn.textContent = 'Continuar';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar arquivo: ' + error.message);
                btn.disabled = false;
                btn.textContent = 'Continuar';
            });
        }
    </script>
</body>
</html>
