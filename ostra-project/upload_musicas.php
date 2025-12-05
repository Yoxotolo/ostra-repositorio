<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config_up.php';

// puxar generos
$stmt = $pdo->query("SELECT id_genero, nm_genero FROM generos ORDER BY nm_genero");
$generos = $stmt->fetchAll();

//puxar projetos do usuario logado
$id_user = $_SESSION['usuario_id'] ?? null;
$projetos = [];
if ($id_user) {
    $stmt = $pdo->prepare("SELECT cd_projeto, nm_projeto FROM projetos WHERE fk_id_usuario = ?");
    $stmt->execute([$id_user]);
    $projetos = $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de musicas -- Ostra</title>

    #region
<style>
/* estilo simples, ajuste conforme seu design */
body{font-family:Inter,Arial,Helvetica,sans-serif;margin:20px;background:#f6f7fb;color:#222}
.container{max-width:1100px;margin:0 auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 6px 18px rgba(15,15,15,0.06)}
.file-list{margin-top:12px;display:flex;flex-direction:column;gap:12px}
.card{border:1px solid #e6e9ef;padding:12px;border-radius:8px;background:#fff;display:flex;gap:12px;align-items:flex-start}
.card .left{width:120px}
.card .right{flex:1}
input,select,textarea{width:100%;padding:8px;border-radius:6px;border:1px solid #dbe2ee}
.small{width:160px;display:inline-block}
.btn{background:#0077ff;color:#fff;padding:9px 14px;border-radius:8px;border:none;cursor:pointer}
.row{display:flex;gap:12px}
.tags{display:flex;gap:8px;flex-wrap:wrap}
.tag{background:#eef5ff;padding:6px 10px;border-radius:999px}
.note{font-size:13px;color:#666;margin-top:6px}
.audio-preview{width:100%}
</style>

</head>
<body>
    
    <div class="container">
        <h2>Upload de musicas (1 ou varias)</h2>
        <p class="note">Escolha as músicas. Para cada arquivo aparecerá um bloco de metadados. Você pode selecionar/usar um projeto existente ou criar um novo projeto abaixo.</p>

        <form id="uploadForm" method="post" action="upload_musicas_handler.php" enctype="multipart/form-data" autocomplete="off">

            <label for="">Selecione as musicas (audio)</label><br>
            <input type="file" id="audioFiles" name="audios[]" accept="audio/*" multiple required> <br>

            <div id="fileList" class="file-list"></div>

            <hr>
            <h3>Albuns / projetos</h3>
            <div class="row">
                <div style="flex: 1;">
                    <label>Escolher Projeto</label>
                    <select name="fk_cd_projeto" id="projetoSelect">
                        <option value="">-- Selecionar projeto existente --</option>
                        <?php foreach($projetos as $p): ?>
                            <option value="<?=htmlspecialchars($p['cd_projeto'])?>"><?=htmlspecialchars($p['nm_projeto'])?></option>
                            <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex: 1;">
                    <label>Criar novo projeto (opcional) - nome</label>
                    <input type="text" name="nm_projeto_novo" placeholder="Nome do novo projeto (opcional)">
                </div>
            </div>

            <div style="margin-top: 12px;">
                <label> Capa do projeto (opcional)</label>
                <input type="file" name="projeto_cover" accept="image/*">
            </div>

            <div style="margin-top: 16px;">
                <button type="submit" class="btn">Enviar tudo</button>
            </div>

                <!-- Campos dinâmicos por arquivo serão adicionados via JS em #fileList -->
        </form>

    </div>

    <script>
// dados vindos do PHP
const generos = <?php echo json_encode($generos); ?>;
const userName = <?= json_encode($_SESSION['nm_nome'] ?? '') ?>;
const userId = <?= json_encode($_SESSION['id_usuario'] ?? null) ?>;

const fileInput = document.getElementById('audioFiles');
const fileList = document.getElementById('fileList');

fileInput.addEventListener('change', (e) => {
  fileList.innerHTML = '';
  const files = Array.from(e.target.files);
  files.forEach((file, idx) => createCard(file, idx));
});

function createCard(file, idx) {
  const card = document.createElement('div');
  card.className = 'card';
  card.dataset.index = idx;

  const left = document.createElement('div'); left.className='left';
  const right = document.createElement('div'); right.className='right';

  // left: info + audio preview
  const title = document.createElement('div');
  title.innerHTML = `<strong>${escapeHtml(file.name)}</strong><div class="note">${(file.size/1024/1024).toFixed(2)} MB</div>`;
  const audio = document.createElement('audio');
  audio.controls = true;
  audio.className = 'audio-preview';
  const objectUrl = URL.createObjectURL(file);
  const src = document.createElement('source');
  src.src = objectUrl;
  audio.appendChild(src);
  left.appendChild(title);
  left.appendChild(audio);

  // right: form fields
  const fields = document.createElement('div');

  // hidden file input clone to submit that specific file with metadata
  const fileField = document.createElement('input');
  fileField.type = 'file';
  fileField.name = `audios_files[${idx}]`;
  fileField.style.display='none';
  // we will not populate this programmatically (browsers block). Instead we will submit the main input and metadata mapping by index.
  // So backend will use $_FILES['audios'] array to map by order.

  // Nome da música
  fields.innerHTML += `<label>Nome da música</label><input type="text" name="nm_musica[${idx}]" value="${escapeHtml(file.name.replace(/\.[^/.]+$/, ""))}" required>`;

  // Artista (usuário) + colaboradores
  fields.innerHTML += `<label>Nome do artista / colaboradores</label>
  <input type="text" name="nm_artista[${idx}]" value="${escapeHtml(userName)}">
  <small class="note">Separe colaboradores por vírgula</small>`;

  // Descrição
  fields.innerHTML += `<label>Descrição</label><textarea name="ds_descricao[${idx}]" rows="3"></textarea>`;

  // Gênero (select com opção para digitar novo)
  let generoHTML = `<label>Gênero</label><div style="display:flex;gap:8px"><select name="fk_id_genero[${idx}]">
    <option value="">-- selecionar --</option>`;
  generos.forEach(g=>{
    generoHTML += `<option value="${g.id_genero}">${escapeHtml(g.nm_genero)}</option>`;
  });
  generoHTML += `</select><input type="text" name="nm_genero_custom[${idx}]" placeholder="ou digite novo"></div>`;
  fields.innerHTML += generoHTML;

  // Tipo de visualização
  fields.innerHTML += `<label>Visibilidade</label>
    <select name="ic_visibilidade[${idx}]">
      <option value="publico">Público</option>
      <option value="privado">Privado</option>
      <option value="agendado">Agendado</option>
    </select>`;
  // se agendado, usuário pode preencher data de lançamento existente dt_lancamento
  fields.innerHTML += `<label>Data de lançamento (para agendado)</label><input type="date" name="dt_lancamento[${idx}]">`;

  // Tipo de compra
  fields.innerHTML += `<label>Tipo de compra</label>
    <select name="ic_tipo_venda[${idx}]" onchange="toggleLimit(this, ${idx})">
      <option value="comum">comum</option>
      <option value="limitada">limitada</option>
      <option value="unica">unica</option>
    </select>`;

  // Limite de vendas (visível apenas se limitada)
  fields.innerHTML += `<div id="limit_group_${idx}" style="display:none;margin-top:6px">
      <label>Quantidade limite (se limitada)</label>
      <input type="number" name="qt_limite_vendas[${idx}]" min="1" placeholder="ex: 50">
    </div>`;

  // Preço
  fields.innerHTML += `<label>Preço (BRL)</label><input type="number" step="0.01" name="vl_musica[${idx}]" value="0.00" required>`;

  // Capa (opcional) - se não enviar, poderá ser gravado null
  fields.innerHTML += `<label>Capa da música (opcional)</label><input type="file" name="ds_foto_capa[${idx}]" accept="image/*">`;

  // ISRC
  fields.innerHTML += `<label>ISRC (opcional)</label><input type="text" maxlength="12" name="ds_isrc[${idx}]">`;

  // hidden index marker so backend saiba a ordem
  fields.innerHTML += `<input type="hidden" name="file_index[]" value="${idx}">`;

  right.appendChild(fields);
  card.appendChild(left);
  card.appendChild(right);
  fileList.appendChild(card);
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"'`]/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[m]});
}

// toggles limit field
function toggleLimit(select, idx) {
  const g = select.value;
  const el = document.getElementById('limit_group_'+idx);
  if (g === 'limitada') el.style.display = 'block';
  else el.style.display = 'none';
}

// Intercept submit to ensure files count matches metadata
document.getElementById('uploadForm').addEventListener('submit', function(e){
  const files = document.getElementById('audioFiles').files;
  const indices = document.querySelectorAll('input[name="file_index[]"]');
  if (files.length === 0) {
    e.preventDefault();
    alert('Selecione pelo menos um arquivo de áudio.');
    return;
  }
  if (indices.length !== files.length) {
    // edge case: metas faltando
    // não pediremos para o usuário, assumimos ordem e prosseguimos (back-end mapeará pela ordem)
    // selecione um item
    console.warn('Número de metadados diferente do número de arquivos. O backend mapeará pela ordem.');
  }
});
</script>

</body>
</html>