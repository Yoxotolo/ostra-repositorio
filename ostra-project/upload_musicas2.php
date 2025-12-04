<?php

if (!headers_sent() && session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {
    header("location: signin.php");
    exit();
}

include 'db.php';

$usuario_id = $_SESSION["usuario_id"];

$sql_check = "SELECT ic_tipo_usuario FROM usuarios WHERE id_usuario = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$stmt_check->bind_result($ic_tipo_usuario);
$stmt_check->fetch();
$stmt_check->close();

if ($ic_tipo_usuario != 'produtor') {
    echo "Acesso negado. Apenas produtores podem enviar musicas.";
    exit();
}

$upload_message ='';
if (isset($_GET['upload']) && $_GET['upload'] === 'success') {
    $upload_message = "<div class='success-message'>Upload(s) concluído(s). Preencha agora os metadados.</div>";

} elseif (isset($_GET['upload']) && $_GET['upload'] === 'error') {
    $msg = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : 'Erro desconhecido';
    $upload_message = "<div class='error-message' > Error: {$msg} </div>";

}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Upload de Músicas — OSTRA</title>

        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="style/sidebar.css">

    </head>

    <body class="feed-page">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'header.php'; ?>

            <div class="upload-container" aria-live="polite">
                <?php echo $upload_message; ?>
                
                <h1>upload de musica</h1>

                <p>arraste e solte 1 ou varios arquivos de audio aqui ou clique para selecionar. (MP3, WAV, OGG, M4A, FLAC)</p>

                <div id="uploadSection" class="upload-section" tabindex="0" role="button" aria-label="area de upload. clique ou arraste arquivos.">

                    <strong>Arraste seus arquivos aqui</strong>
                    <div style="margin-top: 8px; color: #666;">ou clique para selecionar</div>
                    <input type="file" id="fileInput" class="file-input" accept="audio/*" multiple>

                </div>

                <div id="fileList" class="file-list" aria-live="polite"></div>

                <div class="controls">
                    <button id="clearAll" class="clean-button">Limpar</button>
                    <button id="upload" class="upload-button" disabled>Enviar musicas</button>
                </div>

                <div style="margin: top 12px; color:#666; font-size:13;">
                    ao enviar, cada arquivo sera processado individualmente. apos o upload, voce podera preencher os metadados de cada faixa.
                </div>

            </div>
        </main>


<script>

/*
 Front-end JS:
 - Mantém uma lista de arquivos selecionados (File objects)
 - Mostra nome, tamanho e tenta extrair duração (opcional)
 - Permite remover antes do envio
 - Faz upload sequencial usando XMLHttpRequest para permitir progresso
 - Coleta JSON de resposta por arquivo e guarda temp_paths retornados
 - Ao final, redireciona para a página de metadados, passando os caminhos temporários (se retornados)
*/

const uploadSection = document.getElementById('uploadSection');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const upload = document.getElementById('upload');
const clearAll = document.getElementById('clearAll');

let filesMap = new Map(); // key: uid, value: { file, duration, status, temp_path }
let uidCounter = 0;

// Util: formatar bytes
function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k,i)).toFixed(2)) + ' ' + sizes[i];
}

// Gera UID simples
function genUID() {
    uidCounter++;
    return 'f' + Date.now() + '_' + uidCounter;
}

// Renderizar lista de arquivos
function renderList() {
    fileList.innerHTML = '';
    if (filesMap.size === 0) {
        fileList.innerHTML = '<div style="color: #777; margin-top: 12px;">Nenhum arquivo selecionado.</div>';
        upload.disabled = true;
        return;
    }


upload.disabled = false;

filesMap.forEach((meta, uid) => {
    const file = meta.file;
    const status = meta.status || 'pendente';
    const durationText = meta.duration ? ' • ' + meta.duration + 's' : '';
    const item = document.createElement('div');
    item.className = 'file-item';
    item.dataset.uid = uid;

    const info = document.createElement('div');
    info.className = 'file-info';
    info.innerHTML = `
        <div class="file-name">${escapeHtml(file.name)}</div>
        <div class="file-name">${formatBytes(file.size)}${durationText}</div>
    `;

    const progressWrap = document.createElement('div');
    progressWrap.style.display = 'flex';
    progressWrap.style.alignItems = 'center';

    //progresso
    const progress = document.createElement('div');
    progress.className = 'progress';
    const bar = document.createElement('i');
    bar.style.width = (meta.progress || 0) + '%';
    progress.appendChild(bar);

    // status / actions
    const actions = document.createElement('div');
    actions.style.display = 'flex';
    actions.style.alignItems = 'center';
    actions.style.gap = '8px';

    const statusLabel = document.createElement('div');
    statusLabel.style.fontSize = '13px';
    statusLabel.style.color = status === 'ok' ? '#0b6b3b' : (status === 'error' ? '#a91b1b' : '#666');
    statusLabel.textContent = status === 'pendente' ? 'pendente' : (status === 'uploading' ? 'Enviando...' : (status === 'ok' ? 'Pronto' : 'Erro'));

    const removeButton = document.createElement('button');
    removeButton.className = 'remove-button';
    removeButton.textContent = 'Remover';
    removeButton.onclick = () => {
        if (meta.status === 'uploading') {
            alert('Arquivo em envio — aguarde terminar ou aguarde o término para remover.');
            return;
        }
        filesMap.delete(uid);
        renderList();
    };

    actions.appendChild(statusLabel);
    actions.appendChild(removeButton);

    progressWrap.appendChild(progress);
    item.appendChild(info);
    item.appendChild(progressWrap);
    item.appendChild(actions);

    fileList.appendChild(item);

    });
}

// Escape basico para HTML
function escapeHtml(str) {
    return str.replace(/[&<>"']/g, function(m) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]; });
}

// Extrai duração usando Audio element (assincrono)
function extractDuration(file) {
    return new Promise((resolve) => {
        try {
            const url = URL.createObjectURL(file);
            const audio = new Audio();
            audio.preload = 'metadata';
            audio.src = url;
            audio.addEventListener('loadedmetadata', () => {
                const duri = Math.round(audio.duration);
                URL.revokeObjectURL(url);
                resolve(duri);
            });
            audio.addEventListener('error', () => {
                URL.revokeObjectURL(url);
                resolve(null);
            });
        } catch (e) {
            resolve(null);
        }
    }); 
}

// Ao arrastar/colar

uploadSection.addEventListener('click', () => fileUInput.click());
uploadSection.addEventListener('dragover', (e) => { e.preventDefault(); uploadSection.classList.add('drag-over'); });
uploadSection.addEventListener('dragleave', (e) => { e.preventDefault(); uploadSection.classList.remove('drag-over'); });
uploadSection.addEventListener('drop', async (e) => {
    e.preventDefault();
    uploadSection.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    await handleFiles(file);
});

// Input change
fileInput.addEventListener('change', async (e) => {
    const files = e.target.files;
    await handleFiles(files);
});

// limpar tudo
clearAll.addEventListener('click', () => {
    if (confirm('remover todos os arquivos selecionados?')) {
        filesMap.clear();
        renderList();
    }
});

// Processa FileList
async function handleFiles(fileListObj) {
    const arr = Array.from(fileListObj);
    for (const file of arr) {
        //validar tipo
        if (!file.type.startWith('audio/')) {
            alert('arquivo muito grande (max 100MB): ' + file.name);
            continue;
        }

        const uid = genUID();
        filesMap.set(uid, { file, status: 'pendente', progress: 0, duration: null });

        // tenta extrair duração sem bloquear
        extractDuration(file).then(duri => {
            const meta = filesMap.get(uid);
            if (meta) {
                meta.duration = duri;
                filesMap.set(uid, meta);
                renderList();
            }
        });
    }
    renderList();
}

// upload sequencial
upload.addEventListener('click', async () => {
    if (filesMap.size === 0) return;
    upload.disabled = true;
    clearAll.disabled = true;

    const uploadedTempPaths = []; // coleta 'temp_path' retornados pelo servidor (se houver)

    // iterar em ordem
    for (const [uid, meta] of filesMap) {
        const file = meta.file;
        // pular se já foi enviado com sucesso
        if (meta.status === 'ok') continue;

        // criar FormData
        const fd = new FormData();
        fd.append('action', 'upload_audio');
        fd.append('audio_file', file, file.name);

        // atualizar status
        meta.status = 'uploading';
        filesMap.set(uid, meta);
        renderList();

        try {
            // usar XMLHttpRequest para acompanhar progresso
            const xhr = new XMLHttpRequest();

            const promise = new Promise((resolve, reject) => {
                xhr.open('POST', 'upload_musicas_handler.php', true);
                xhr.responseType = 'json';

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        const m = filesMap.get(uid);
                        if (m) { m.progress = percent; filesMap.set(uid, m); renderList(); }
                    }
                };

                xhr.onload = () => {
                    const status = xhr.status;
                    const res = xhr.response;
                    if (status >= 200 && status < 300) {
                        // O handler idealmente retorna { success: true, temp_path: "...", ... }
                        if (res && res.success) {
                            const m = filesMap.get(uid);
                            m.status = 'ok';
                            // tenta extrair temp path retornado
                            if (res.temp_path) {
                                m.temp_path = res.temp_path;
                                uploadedTempPaths.push(res.temp_path);
                            } else if (res.temp_audio_path) {
                                m.temp_path = res.temp_audio_path;
                                uploadedTempPaths.push(res.temp_audio_path);
                            }
                            filesMap.set(uid, m);
                            renderList();
                            resolve(res);
                        } else {
                            const errMsg = (res && res.message) ? res.message : ('Resposta não esperada do servidor (status ' + status + ')');
                            const m = filesMap.get(uid);
                            m.status = 'error';
                            filesMap.set(uid, m);
                            renderList();
                            reject(new Error(errMsg));
                        }
                    } else {
                        const text = xhr.responseText || ('HTTP ' + status);
                        const m = filesMap.get(uid);
                        m.status = 'error';
                        filesMap.set(uid, m);
                        renderList();
                        reject(new Error('Erro do servidor: ' + text));
                    }
                };

                xhr.onerror = () => {
                    const m = filesMap.get(uid);
                    m.status = 'error';
                    filesMap.set(uid, m);
                    renderList();
                    reject(new Error('Erro na requisição (network)'));
                };

                xhr.send(fd);
            });

            await promise;
        } catch (err) {
            console.error('Upload falhou para', file.name, err);
            alert('Erro ao enviar ' + file.name + ' — ' + err.message);
            // continuar com os próximos arquivos
        }
    }

    // todos processados
    upload.disabled = false;
    clearAll.disabled = false;

    // Se obtivemos temp_paths do servidor, redirecionar para edição em massa
    if (uploadedTempPaths.length > 0) {
        // montar query string segura (urlencode cada caminho)
        const encoded = uploadedTempPaths.map(p => encodeURIComponent(p)).join(',');
        // redireciona para a página de metadados com os caminhos temporários
        window.location.href = 'upload_musicas_metadatas.php?temp_paths=' + encoded;
    } else {
        // Se o handler não retornou caminhos temporários, podemos redirecionar sem parâmetros
        // (mas o ideal é adaptar o handler para retornar os temp_paths)
        window.location.href = 'upload_musicas_metadatas.php';
    }
});
    

        </script>
    </body>
</html>