<?php
// Arquivo para preencher metadados da música

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

// Recupera o arquivo temporário
$temp_file_path = isset($_SESSION['temp_audio_path']) ? $_SESSION['temp_audio_path'] : null;

if (!$temp_file_path || !file_exists($temp_file_path)) {
    // Se não houver caminho na sessão ou o arquivo não existir, redireciona
    header("Location: upload_musicas.php?upload=error&msg=" . urlencode("Arquivo de áudio temporário não encontrado ou expirado."));
    exit();
}

// Validar que o arquivo temporário existe
$temp_path = 'uploads/temp/' . basename($temp_file_path);
if (!file_exists($temp_path)) {
    header("Location: upload_musicas.php?upload=error&msg=" . urlencode("Arquivo temporário não encontrado."));
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Música - OSTRA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="feed-page">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="metadata-container">
            <div class="metadata-form">
                <h1 class="form-title">Detalhes da Música</h1>
                <p class="form-subtitle">Preencha as informações sobre sua música</p>

                <form id="metadataForm" enctype="multipart/form-data">
                    <input type="hidden" name="temp_file_path" value="<?php echo htmlspecialchars($temp_file_path); ?>">
                    <input type="hidden" name="temp_file" value="<?php echo htmlspecialchars($temp_file); ?>">

                    <!-- Nome da Música -->
                    <div class="form-group">
                        <label for="musicName">Nome da Música *</label>
                        <input type="text" id="musicName" name="music_name" placeholder="Digite o nome da música" required>
                        <p class="form-hint">Exemplo: "Noites de Verão"</p>
                    </div>

                    <!-- Artista/Produtor -->
                    <div class="form-group">
                        <label for="artistName">Artista/Produtor *</label>
                        <input type="text" id="artistName" name="artist_name" placeholder="Seu nome ou nome artístico" required>
                        <p class="form-hint">Será preenchido automaticamente com seu nome se deixado vazio</p>
                    </div>

                    <!-- Data de Lançamento -->
                    <div class="form-group">
                        <label for="releaseDate">Data de Lançamento *</label>
                        <input type="date" id="releaseDate" name="release_date" required>
                    </div>

                    <!-- Descrição -->
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea id="description" name="description" placeholder="Adicione uma descrição sobre a música (opcional)"></textarea>
                        <p class="form-hint">Máximo 500 caracteres</p>
                    </div>

                    <!-- Imagem da Música -->
                    <div class="image-upload-group">
                        <div class="image-preview-container">
                            <div class="image-preview" id="imagePreview">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #666;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                                    <path d="M21 15L16 10L3 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="image-upload-controls">
                                <label for="musicImage">Imagem da Música (Capa)</label>
                                <input type="file" id="musicImage" name="music_image" class="image-upload-input" accept="image/*">
                                <button type="button" class="btn-upload-image" onclick="document.getElementById('musicImage').click()">
                                    Selecionar Imagem
                                </button>
                                <button type="button" class="btn-remove-image" id="removeImageBtn" onclick="removeImage()">
                                    Remover Imagem
                                </button>
                                <p class="form-hint" style="margin-top: 10px;">Recomendado: 500x500px ou maior</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Compra -->
                    <div class="purchase-type-group">
                        <label>Tipo de Compra *</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="purchaseSingle" name="purchase_type" value="unica" required>
                                <label for="purchaseSingle">Compra Única</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="purchaseMultiple" name="purchase_type" value="multipla" required>
                                <label for="purchaseMultiple">Compra Múltipla</label>
                            </div>
                        </div>
                        <p class="form-hint" style="margin-top: 10px;">
                            Compra Única: Cliente compra uma vez e tem acesso permanente.<br>
                            Compra Múltipla: Cliente pode comprar múltiplas vezes (ex: royalties).
                        </p>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="button-group">
                        <button type="button" class="btn-secondary" onclick="goBack()">Voltar</button>
                        <button type="submit" class="btn-primary" id="submitBtn">Salvar Música</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const musicImageInput = document.getElementById('musicImage');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const metadataForm = document.getElementById('metadataForm');
        const submitBtn = document.getElementById('submitBtn');

        let selectedImage = null;

        // Lidar com seleção de imagem
        musicImageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                
                // Validar tipo
                if (!file.type.startsWith('image/')) {
                    alert('Por favor, selecione um arquivo de imagem.');
                    return;
                }

                // Validar tamanho (máximo 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Imagem muito grande. Máximo permitido: 10MB');
                    return;
                }

                selectedImage = file;

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = (event) => {
                    imagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                    removeImageBtn.classList.add('show');
                };
                reader.readAsDataURL(file);
            }
        });

        function removeImage() {
            selectedImage = null;
            musicImageInput.value = '';
            imagePreview.innerHTML = `
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #666;">
                    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                    <path d="M21 15L16 10L3 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            `;
            removeImageBtn.classList.remove('show');
        }

        function goBack() {
            window.history.back();
        }

        // Lidar com envio do formulário
        metadataForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validar campos obrigatórios
            const musicName = document.getElementById('musicName').value.trim();
            const releaseDate = document.getElementById('releaseDate').value;
            const purchaseType = document.querySelector('input[name="purchase_type"]:checked');

            if (!musicName) {
                alert('Por favor, preencha o nome da música.');
                return;
            }

            if (!releaseDate) {
                alert('Por favor, selecione a data de lançamento.');
                return;
            }

            if (!purchaseType) {
                alert('Por favor, selecione o tipo de compra.');
                return;
            }

            // Criar FormData
            const formData = new FormData(metadataForm);

            // Adicionar imagem se selecionada
            if (selectedImage) {
                formData.append('music_image', selectedImage);
            }

            // Desabilitar botão
            submitBtn.disabled = true;
            submitBtn.textContent = 'Salvando...';

            try {
                const response = await fetch('upload_musicas_handler.php', {
                    method: 'POST',
                    body: formData
                });

                // Adicionar tratamento para respostas não-JSON (como HTML de erro)
                if (!response.ok) {
                    // Se a resposta não for OK (ex: 500 Internal Server Error),
                    // tenta ler como texto para mostrar o erro do servidor
                    const errorText = await response.text();
                    throw new Error('Erro do Servidor: ' + errorText.substring(0, 100) + '...');
                }

                const data = await response.json();

                if (data.success) {
                    // Redirecionar para perfil com mensagem de sucesso
                    window.location.href = 'profile.php?upload=success&type=music';
                } else {
                    alert('Erro ao salvar música: ' + (data.message || 'Erro desconhecido'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Salvar Música';
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao salvar música: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Salvar Música';
            }
        });

        // Definir data mínima como hoje
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('releaseDate').min = today;
    </script>
</body>
</html>