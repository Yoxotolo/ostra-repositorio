<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function return_json($data, $http_code = 200) {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit();
}

function return_error($msg, $code = 400) {
    return_json(['success' => false, 'message' => $msg], $code);
}

function sanitize_filename($name) {
    $name = preg_replace('/[^\w\-\.\s()]/u', '', $name);
    return trim($name) ?: 'file';
}

include 'db.php';
if (!isset($conn)) return_error("Erro ao conectar ao banco.");

if (!isset($_SESSION['usuario_id'])) return_error("Usuário não autenticado.");

$usuario_id = (int)$_SESSION['usuario_id'];

// verificar se é produtor
$stmt = $conn->prepare("SELECT ic_tipo_usuario, nm_nome FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $user['ic_tipo_usuario'] !== 'produtor') {
    return_error("Apenas produtores podem enviar músicas.");
}

$action = $_POST['action'] ?? null;

/* ==========================================================================================
   🔥 AÇÃO: SALVAR METADADOS NO BANCO (musica)
   ========================================================================================== */
if ($action === "save_music") {

    if (!isset($_POST['temp_file_path'])) return_error("Arquivo temporário não encontrado.");

    $temp_rel = $_POST['temp_file_path'];
    $temp_full = __DIR__ . '/' . $temp_rel;

    if (!file_exists($temp_full)) return_error("Arquivo não encontrado no servidor.");

    // Campos do formulário
$titulo        = trim((string)($_POST['titulo'] ?? ''));
$descricao     = trim((string)($_POST['description'] ?? ''));
$purchase_type = trim((string)($_POST['purchase_type'] ?? ''));
$release_date  = trim((string)($_POST['release_date'] ?? ''));
$price         = trim((string)($_POST['music_price'] ?? '0'));
$project_id    = ($_POST['project_id'] ?? null); // aqui não dou trim porque é numérico
$generos       = $_POST['generos'] ?? [];

    if ($titulo === '') return_error("O título é obrigatório.");
    if (!in_array($purchase_type, ['exclusiva','multipla'])) {
        return_error("Tipo de venda inválido.");
    }

    // descrição só aceita 20 chars
    $descricao = mb_substr($descricao, 0, 20);

    // mover arquivo final
    $ext = strtolower(pathinfo($temp_full, PATHINFO_EXTENSION));
    $final_dir_rel = "uploads/musicas/";
    $final_dir = __DIR__ . '/' . $final_dir_rel;

    if (!is_dir($final_dir)) mkdir($final_dir, 0777, true);

    $new_audio_name = "music_{$usuario_id}_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;

    $dest_full = $final_dir . $new_audio_name;
    $dest_rel  = $final_dir_rel . $new_audio_name;

    if (!rename($temp_full, $dest_full))
        return_error("Erro ao mover o arquivo para o destino final.");

    // Capa (não existe no banco — apenas salva no diretório)
    $capa_rel = null;

    if (!empty($_FILES['music_image']) && $_FILES['music_image']['error'] === 0) {
        $ext_i = strtolower(pathinfo($_FILES['music_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext_i, ['jpg','jpeg','png','webp'])) {
            return_error("Formato da capa inválido.");
        }

        $capas_rel = "uploads/capas/";
        $capas_dir = __DIR__ . '/' . $capas_rel;

        if (!is_dir($capas_dir)) mkdir($capas_dir, 0777, true);

        $new_cover = "cover_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext_i;

        $dest_cover_full = $capas_dir . $new_cover;
        $capa_rel = $capas_rel . $new_cover;

        if (!move_uploaded_file($_FILES['music_image']['tmp_name'], $dest_cover_full)) {
            return_error("Erro ao salvar capa.");
        }
    }

    /* ======================================================
       🔥 INSERIR NA TABELA musicas
       ====================================================== */

    $stmt = $conn->prepare("
        INSERT INTO musicas 
        (ds_musica, nm_musica, ds_arquivo, vl_musica, fk_cd_projeto, fk_id_usuario, ic_tipo_venda) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssdiis",
        $descricao,
        $titulo,
        $dest_rel,
        $price,
        $project_id,
        $usuario_id,
        $purchase_type
    );

    $stmt->execute();
    $music_id = $stmt->insert_id;
    $stmt->close();

    /* ======================================================
       🔥 INSERIR GÊNEROS NA TABELA musica_genero
       ====================================================== */

    if (!empty($generos)) {
        $stmt = $conn->prepare("INSERT INTO musica_genero (fk_id_musica, fk_id_genero) VALUES (?, ?)");

        foreach ($generos as $g) {
            $gid = (int)$g;
            $stmt->bind_param("ii", $music_id, $gid);
            $stmt->execute();
        }
        $stmt->close();
    }

    // remover temp da sessão
    if (isset($_SESSION['temp_audio_files'])) {
        $_SESSION['temp_audio_files'] = array_values(array_filter(
            $_SESSION['temp_audio_files'],
            fn($x) => $x['path'] !== $temp_rel
        ));
    }

    return_json([
        'success' => true,
        'message' => 'Música salva!',
        'music_id' => $music_id,
        'arquivo' => $dest_rel,
        'capa' => $capa_rel
    ]);
}

/* ==========================================================================================
   🔥 UPLOAD INICIAL TEMPORÁRIO
   ========================================================================================== */

$max_size = 100 * 1024 * 1024;
$allowed_ext = ['mp3','wav','ogg','m4a','aac','flac'];

$temp_dir_rel = "uploads/temp/";
$temp_dir = __DIR__ . '/' . $temp_dir_rel;

if (!is_dir($temp_dir)) mkdir($temp_dir, 0777, true);

function move_temp_audio($tmp, $orig, $user_id, $temp_dir, $temp_dir_rel, $allowed_ext) {
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) return ['error' => "Formato não suportado."];

    $name = "temp_{$user_id}_" . time() . "_" . bin2hex(random_bytes(4)) . ".$ext";
    $full = $temp_dir . $name;
    $rel = $temp_dir_rel . $name;

    if (!move_uploaded_file($tmp, $full)) return ['error' => "Erro ao mover arquivo."];

    return ['temp_path' => $rel, 'temp_name' => $name];
}

$saved_files = [];

if (!empty($_FILES['audio_files'])) {
    foreach ($_FILES['audio_files']['name'] as $i => $name) {

        if ($_FILES['audio_files']['error'][$i] !== UPLOAD_ERR_OK) continue;

        $orig = sanitize_filename($name);
        $tmp  = $_FILES['audio_files']['tmp_name'][$i];

        $m = move_temp_audio($tmp, $orig, $usuario_id, $temp_dir, $temp_dir_rel, $allowed_ext);

        if (!isset($m['error'])) {
            $saved_files[] = ['path' => $m['temp_path'], 'name' => $orig];
        }
    }
}

if (empty($saved_files)) {
    return_error("Nenhum arquivo válido enviado.");
}

$_SESSION['temp_audio_files'] = $saved_files;

return_json([
    'success' => true,
    'message' => "Upload recebido!",
    'files' => $saved_files
]);
