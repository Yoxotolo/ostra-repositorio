    <?php
    // upload_musicas_metadatas.php (modo B - CARDS)
    // - Exibe vários arquivos temporários salvos em sessão (ou via GET temp_paths)
    // - Para cada arquivo mostra um card com preview de áudio e botão Editar
    // - Ao clicar em Editar, abre um painel abaixo do card com o formulário
    // - Salva cada música individualmente via fetch() para upload_musicas_handler.php?action=save_music
    // - Depois de salvar, o card fica cinza com indicação de sucesso (permanece editável)
    // - Quando todas as músicas estiverem salvas, mostra botão Concluir e inicia contagem (3s) para redirecionar
    //
    // DEPENDÊNCIAS / EXPECTATIVAS:
    // - A etapa anterior (upload) deve ter deixado em sessão algo como:
    //   $_SESSION['temp_audio_files'] = [
    //       ['path' => 'uploads/temp/audio_user_123_1600000000.mp3', 'name' => 'MinhaFaixa.mp3'],
    //       ...
    //   ];
    // - Se os dados vierem via GET param 'temp_paths' (csv urlencoded), o arquivo tenta mapear para caminhos relativos.
    // - O handler espera receber POST { action=save_music, temp_file_path, music_name, artist_name, release_date, description, purchase_type } + optional file 'music_image'.
    // - Corrija permissões de pasta uploads/ quando necessário.
    //
    // Nota de segurança: Evite expor caminhos absolutos no front-end em produção. Aqui usamos caminhos relativos que o servidor pode mover/processar.
    // ------------------------------------------------------------------

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: signin.php');
        exit();
    }

    include 'db.php';

    $usuario_id = $_SESSION['usuario_id'];

    // Verifica se usuário é produtor
    $stmt = $conn->prepare("SELECT ic_tipo_usuario, nm_nome, ds_foto_perfil FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || $user['ic_tipo_usuario'] !== 'produtor') {
        header('Location: profile.php');
        exit();
    }

    // Recupera lista de arquivos temporários da sessão
    // Estrutura esperada: $_SESSION['temp_audio_files'] = [ ['path'=>'uploads/temp/..mp3','name'=>'origem.mp3'], ... ]
    $temp_files = [];
    if (!empty($_SESSION['temp_audio_files']) && is_array($_SESSION['temp_audio_files'])) {
        $temp_files = $_SESSION['temp_audio_files'];
    } elseif (!empty($_GET['temp_paths'])) {
        // suporte alternativo: ?temp_paths=urlencode(path1),urlencode(path2)
        $raw = $_GET['temp_paths'];
        $parts = explode(',', $raw);
        foreach ($parts as $p) {
            $p = urldecode($p);
            if ($p) {
                $temp_files[] = ['path' => $p, 'name' => basename($p)];
            }
        }
    }

    // Se não há arquivos temporários, redireciona para upload
    if (empty($temp_files)) {
        header('Location: upload_musicas.php?upload=error&msg=' . urlencode('Nenhum arquivo temporário encontrado.'));
        exit();
    }

    // Função auxiliar para escapar
    function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar Metadados — OSTRA</title>
    <!-- Sem CSS externo (você vai cuidar depois). Estilos mínimos inline. -->

    <style>
    body{font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial; background:#fafafa; margin:0; padding:20px;}
    .container{max-width:1100px;margin:0 auto;}
    .cards{display:flex;flex-wrap:wrap;gap:14px;}
    .card{background:#fff;border:1px solid #e6e6e6;border-radius:8px;padding:12px;width:100%;box-sizing:border-box;}
    .card-row{display:flex;align-items:flex-start;gap:12px;}
    .card-left{width:220px;flex-shrink:0;}
    .card-main{flex:1;}
    .small{font-size:13px;color:#666;}
    .audio-inline{width:100%;}
    .btn{padding:8px 12px;border-radius:6px;border:0;cursor:pointer;}
    .btn-primary{background:#2563eb;color:#fff;}
    .btn-secondary{background:#eef2ff;color:#111;}
    .btn-ghost{background:transparent;border:1px solid #ddd;color:#333;}
    .card-saved{background:#f3f7f2;}
    .form-panel{margin-top:10px;padding:10px;border-top:1px solid #f0f0f0;}
    .field{margin-bottom:8px;}
    .input,textarea,select{width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;}
    .preview-img{width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #eee;}
    .status{font-weight:600;}
    .meta-row{display:flex;gap:8px;align-items:center;}
    .conclude-wrap{margin-top:18px;padding:12px;background:#fff;border-radius:8px;border:1px solid #e6e6e6;}
    .hidden{display:none;}
    .green{color:#0b6b3b;}
    .red{color:#a91b1b;}
    .counter{font-weight:700;color:#2563eb;}
    </style>

    </head>
    <body>

        <?php include 'sidebar.php'; ?>

        <div class="container">

            <?php include 'header.php'; ?>

            <h1>Preencher Metadados — Músicas Enviadas</h1>
            <p class="small">Edite os metadados de cada faixa. Você pode ouvir a faixa, adicionar capa e salvar individualmente.</p>

            <div id="cards" class="cards">
                <!-- Os cards serão montados por PHP/JS -->
                <?php foreach ($temp_files as $index => $f): 
                    // Normalize
                    $path = $f['path'];
                    $name = isset($f['name']) ? $f['name'] : basename($path);
                    // Exibir paths relativos (evitar expor absolutos)
                    $rel_path = h($path);
                    $safe_name = h($name);
                ?>
                <div class="card" data-index="<?php echo $index; ?>" id="card-<?php echo $index; ?>">
                    <div class="card-row">
                        <div class="card-left">
                            <div><strong><?php echo $safe_name; ?></strong></div>
                            <div class="small" style="margin-top:6px;"><?php echo h($rel_path); ?></div>

                            <div style="margin-top:8px;">
                                <audio class="audio-inline" controls preload="metadata">
                                    <source src="<?php echo $rel_path; ?>">
                                    Seu navegador não suporta reprodução de áudio.
                                </audio>
                            </div>

                            <div style="margin-top:8px;" class="meta-row">
                                <div class="status" id="status-<?php echo $index; ?>">Pendente</div>
                                <div style="margin-left:auto;">
                                    <button class="btn btn-ghost" onclick="togglePanel(<?php echo $index; ?>)">Editar</button>
                                </div>
                            </div>
                        </div>

                        <div class="card-main">
                            <!-- Painel de edição (inicialmente oculto) -->
                            <div id="panel-<?php echo $index; ?>" class="form-panel hidden">

                                <form id="form-<?php echo $index; ?>" enctype="multipart/form-data">

                                    <input type="hidden" name="action" value="save_music">
                                    <input type="hidden" name="temp_file_path" value="<?php echo $rel_path; ?>">
                                    <div class="field">
                                        <label>Nome da Música *</label>
                                        <input name="titulo" class="input" required placeholder="Nome da faixa">
                                    </div>
                                    <div class="field">
                                        <label>Artista/Produtor</label>
                                        <input name="artist_name" class="input" placeholder="Artista - será preenchido com seu nome se vazio">
                                    </div>
                                    <div class="field">
                                        <label>Data de Lançamento *</label>
                                        <input type="date" name="release_date" class="input" required>
                                    </div>
                                    <div class="field">
                                        <label>Descrição</label>
                                        <textarea name="description" class="input" rows="3" maxlength="500" placeholder="Descrição (opcional)"></textarea>
                                    </div>

                                    <div class="field" style="display:flex;gap:12px;align-items:flex-start;">
                                        <div style="flex:1;">
                                            <label>Imagem da Música (capa)</label>
                                            <input type="file" accept="image/*" name="music_image" onchange="previewImage(event, <?php echo $index; ?>)">
                                            <div class="small">Max 10MB. JPG/PNG/WebP.</div>
                                        </div>
                                        <div style="width:130px;">
                                            <img id="img-preview-<?php echo $index; ?>" class="preview-img" src="assets/default-album.svg" alt="Preview">
                                        </div>
                                    </div>=

                                    <div class="field">
                                        <label>Tipo de Compra *</label>
                                        <select name="purchase_type" class="input" required>
                                            <option value="">-- selecione --</option>
                                            <option value="unica">Compra Única</option>
                                            <option value="multipla">Compra Múltipla</option>
                                        </select>
                                    </div>

                                    <div style="display:flex;gap:8px;margin-top:8px;align-items:center;">
                                        <button type="button" class="btn btn-primary" onclick="saveMusic(<?php echo $index; ?>)">Salvar Esta Música</button>
                                        <button type="button" class="btn btn-secondary" onclick="clearForm(<?php echo $index; ?>)">Limpar</button>
                                        <div id="form-msg-<?php echo $index; ?>" class="small" style="margin-left:12px;color:#666;"></div>
                                    </div>

                                </form>

                            </div> <!-- fim panel -->
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div> <!-- cards -->

            <div id="conclude" class="conclude-wrap hidden" aria-live="polite">
                <div id="conclude-msg">Todas as músicas foram salvas com sucesso.</div>
                <div style="margin-top:8px;">
                    <button id="finalizeBtn" class="btn btn-primary" onclick="finishNow()">Concluir Agora</button>
                    <span style="margin-left:12px;" class="small">Redirecionando em <span id="countdown">3</span>s...</span>
                </div>
            </div>

        </div>

    <script>
    /* Front-end logic:
    - togglePanel(i) abre/fecha o painel de edição do card i
    - saveMusic(i) envia FormData para upload_musicas_handler.php?action=save_music
    - on success: marca card como salvo (cinza), atualiza status, e deixa possível re-editar
    - quando todos salvos: mostra conclude + inicia contagem de 3s para redirecionar
    */

    // Coleta a quantidade de cards
    const totalCards = document.querySelectorAll('.card').length;
    let savedCount = 0;
    let savingFlags = {}; // evita duplicar requests por card

    function togglePanel(index) {
        const panel = document.getElementById('panel-' + index);
        if (!panel) return;
        panel.classList.toggle('hidden');
    }

    // preview de imagem para o card
    function previewImage(event, index) {
        const input = event.target;
        const preview = document.getElementById('img-preview-' + index);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.startsWith('image/')) {
                alert('Formato de imagem inválido.');
                input.value = '';
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('Imagem muito grande (max 10MB).');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = 'assets/default-album.svg';
        }
    }

    async function saveMusic(index) {
        if (savingFlags[index]) return; // já em processo
        const form = document.getElementById('form-' + index);
        const statusEl = document.getElementById('status-' + index);
        const cardEl = document.getElementById('card-' + index);
        const msgEl = document.getElementById('form-msg-' + index);

        // Basic validation client-side (HTML required covers main cases)
        // Build FormData (inclui file input)
        const fd = new FormData(form);

        // desabilitar botões do form
        savingFlags[index] = true;
        msgEl.textContent = 'Salvando...';
        statusEl.textContent = 'Salvando...';

        try {
            const resp = await fetch('upload_musicas_handler.php', {
                method: 'POST',
                body: fd
            });

            if (!resp.ok) {
                const txt = await resp.text();
                throw new Error('Erro do servidor: ' + (txt.substring ? txt.substring(0,200) : txt));
            }

            const data = await resp.json();
            if (data.success) {
                // Marca como salvo (visual)
                statusEl.textContent = '✔ Salva com sucesso';
                statusEl.classList.add('green');
                cardEl.classList.add('card-saved');
                msgEl.textContent = 'Salva. Você pode editar novamente se quiser.';
                savedCount++;

                // Opcional: se o servidor retornar algo útil (ex: music_id), podemos armazenar
                // Exemplo: data.music_id, data.music_path, etc.
                // Se o servidor limpou a sessão temporária, não precisamos mexer aqui.

                checkAllSaved();
            } else {
                throw new Error(data.message || 'Resposta inválida do servidor');
            }
        } catch (err) {
            console.error('Erro ao salvar faixa', err);
            msgEl.textContent = 'Erro: ' + err.message;
            statusEl.textContent = 'Erro';
            statusEl.classList.add('red');
        } finally {
            savingFlags[index] = false;
        }
    }

    function clearForm(index) {
        const form = document.getElementById('form-' + index);
        form.reset();
        const preview = document.getElementById('img-preview-' + index);
        if (preview) preview.src = 'assets/default-album.svg';
        const msgEl = document.getElementById('form-msg-' + index);
        if (msgEl) msgEl.textContent = '';
    }

    // Quando todos forem salvos, mostramos concluir + redirecionamento
    let countdownTimer = null;
    function checkAllSaved() {
        // conta quantos cards estão com status "Salva com sucesso"
        const statuses = document.querySelectorAll('[id^="status-"]');
        let count = 0;
        statuses.forEach(el => {
            if (el.textContent && el.textContent.toLowerCase().includes('salva')) count++;
        });

        if (count >= totalCards) {
            // mostrar conclude
            const conclude = document.getElementById('conclude');
            conclude.classList.remove('hidden');

            // iniciar contagem regressiva de 3s (mostrar na tela)
            let sec = 3;
            const countdownEl = document.getElementById('countdown');
            countdownEl.textContent = sec;
            if (countdownTimer) clearInterval(countdownTimer);
            countdownTimer = setInterval(() => {
                sec--;
                countdownEl.textContent = sec;
                if (sec <= 0) {
                    clearInterval(countdownTimer);
                    // redirecionar para profile.php
                    window.location.href = 'profile.php?upload=success&type=music';
                }
            }, 1000);
        }
    }

    // Permite ao usuário concluir manualmente antes da contagem terminar
    function finishNow() {
        if (countdownTimer) clearInterval(countdownTimer);
        window.location.href = 'profile.php?upload=success&type=music';
    }

    // Definir data mínima dos inputs para hoje
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('input[type="date"]').forEach(i => { i.min = today; });
    });

    </script>
    </body>
    </html>