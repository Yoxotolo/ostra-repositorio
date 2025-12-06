<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Usuário não autenticado"
    ]);
    exit;
}

require_once 'config_up.php';
header('Content-Type: application/json; charset=utf-8');

$uploadMusicDir = __DIR__ . '/uploads/musicas/';
$uploadCoversDir = __DIR__ . '/uploads/music_covers/';

//criar pasta se nao existir

if (!is_dir($uploadMusicDir)) mkdir($uploadMusicDir, 0755, true);
if (!is_dir($uploadCoversDir)) mkdir($uploadCoversDir, 0755, true);

$id_user = $_SESSION['usuario_id'] ?? null;
if (!$id_user) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Usuário não autenticado.']);
    exit;
}

try {
    $pdo-> beginTransaction();

    // 1) se criar projeto novo
    $fk_cd_projeto = null;
    if(!empty($_POST['nm_projeto_novo'])) {
        $nm_projeto = trim($_POST['nm_projeto_novo']);
        $vl_projeto = 0.00;
        $ic_tipo_venda_projeto = 'comum';

        // upload capa do projeto se existir
        if (!empty($_FILES['projeto_cover']) && $_FILES['projeto_cover']['error'] === UPLOAD_ERR_OK){
            $f = $_FILES['projeto_cover'];
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $safeName = 'project_cover_'.time().'_'.bin2hex(random_bytes(6)).'.'.$ext;
            move_uploaded_file($f['tmp_name'], $uploadCoversDir . $safeName);
            $ds_foto_capa = 'uploads/music_covers/' . $safeName;
        } else $ds_foto_capa = null;

        $stmt = $pdo->prepare("INSERT INTO projetos (nm_projeto, ds_projeto, vl_projeto, fk_id_usuario, ic_tipo_venda, ds_foto_capa) VALUES (?, NULL, ?, ?, ?, ?)");
        $stmt->execute([$nm_projeto, $vl_projeto, $id_user, $ic_tipo_venda_projeto, $ds_foto_capa]);
        $fk_cd_projeto = $pdo->lastInsertId();
    }else if (!empty($_POST['fk_cd_projeto'])) {
        $fk_cd_projeto = $_POST['fk_cd_projeto'] ?: null;
    }

   // 2) Processa cada arquivo de musica
   if (empty($_FILES['audios']) || !isset($_FILES['audios']['name'])) {
    throw new Exception("Nenhum arquivo de audio enviado.");
   }

   
    // files array structure -> we'll iterate preserving order
    $files = $_FILES['audios']; // padrão
    // file_index[] diz a qual metadata corresponde. Se ausente, assumimos ordem natural
    $file_indices = $_POST['file_index'] ?? [];
    // Normalize to numeric array
    $file_indices = array_values($file_indices);

    $inserted = [];

    for ($i = 0; $i < count($files['name']); $i++) {
        // map index
        $mapIdx = $file_indices[$i] ?? $i; // se nao enviado, assign i
        $originalName = $files['name'][$i];
        $tmpName = $files['tmp_name'][$i];
        $error = $files['error'][$i];

        if ($error !== UPLOAD_ERR_OK) {
            // pular ou lançar erro
            throw new Exception("Erro no upload do arquivo {$originalName} (index {$i}). Code: {$error}");
        }

        // leitura dos campos correspondentes ao mapIdx
        $nm_musica = trim($_POST['nm_musica'][$mapIdx] ?? pathinfo($originalName, PATHINFO_FILENAME));
        $nm_artista = trim($_POST['nm_artista'][$mapIdx] ?? $_SESSION['nm_nome']);
        $ds_descricao = trim($_POST['ds_descricao'][$mapIdx] ?? null);
        $fk_id_genero = !empty($_POST['fk_id_genero'][$mapIdx]) ? intval($_POST['fk_id_genero'][$mapIdx]) : null;
        $nm_genero_custom = trim($_POST['nm_genero_custom'][$mapIdx] ?? '');
        $ic_visibilidade = $_POST['ic_visibilidade'][$mapIdx] ?? 'publico';
        $dt_lancamento = $_POST['dt_lancamento'][$mapIdx] ?? null;
        $ic_tipo_venda = $_POST['ic_tipo_venda'][$mapIdx] ?? 'comum';
        $qt_limite_vendas = !empty($_POST['qt_limite_vendas'][$mapIdx]) ? intval($_POST['qt_limite_vendas'][$mapIdx]) : null;
        $vl_musica = number_format((float)($_POST['vl_musica'][$mapIdx] ?? 0.00), 2, '.', '');
        $ds_isrc = !empty($_POST['ds_isrc'][$mapIdx]) ? substr(trim($_POST['ds_isrc'][$mapIdx]), 0, 12) : null;

        // se genero custom preenchido, inserir em generos (ou buscar existente)
        if ($fk_id_genero === null && $nm_genero_custom !== '') {
            //verificar existencia
            $stmt = $pdo->prepare("SELECT id_genero FROM generos WHERE nm_genero = ?");
            $stmt->execute([$nm_genero_custom]);
            $g = $stmt->fetch();
            if ($g) {
                $fk_id_genero = $g['id_genero'];
            }else{
                $stmt = $pdo->prepare("INSERT INTO generos (nm_genero) VALUES (?)");
                $stmt->execute([$nm_genero_custom]);
                $fk_id_genero = $pdo->lastInsertId();
            }
        }

        // Upload do arquivo de audio cover
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeAudioName = 'audio_'.time().'_'.bin2hex(random_bytes(6)).'.'.$ext;
        $destAudio = $uploadMusicDir . $safeAudioName;
        if (!move_uploaded_file($tmpName, $destAudio)) {
            throw new Exception("Falha ao mover o arquivo de áudio {$originalName}.");
        }
        $ds_arquivo = 'uploads/musicas/' . $safeAudioName;

        // upload da capa da musica (se enviada)
        $ds_foto_capa = null;

        // note: HTML form used name ds_foto_capa[IDX] — PHP gera arrays em $_FILES['ds_foto_capa'] possivelmente
        if (!empty($_FILES['ds_foto_capa']) && isset($_FILES['ds_foto_capa']['name'][$mapIdx]) && $_FILES['ds_foto_capa']['error'][$mapIdx] === UPLOAD_ERR_OK) {
            $f = [
                'name' => $_FILES['ds_foto_capa']['name'][$mapIdx],
                'tmp_name' => $_FILES['ds_foto_capa']['tmp_name'][$mapIdx],
                'error' => $_FILES['ds_foto_capa']['error'][$mapIdx],
            ];

            $extc = pathinfo($f['name'], PATHINFO_EXTENSION);
            $safeCover = 'cover_'.'_'.bin2hex(random_bytes(6)).'.'.$extc;
            if (!move_uploaded_file($f['tmp_name'], $uploadCoversDir . $safeCover)) {
                throw new Exception("Falha ao mover capa da música {$f['name']}.");
            }
            $ds_foto_capa = 'uploads/music_covers/' . $safeCover;
        }

        // Inserir música
        $sql = "INSERT INTO musicas
            (ds_isrc, nm_musica, nm_artista, ds_descricao, ds_arquivo, ds_foto_capa, vl_musica, ic_tipo_venda, dt_lancamento, fk_cd_projeto, fk_id_usuario, ic_visibilidade, qt_limite_vendas)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $ds_isrc,
            $nm_musica,
            $nm_artista,
            $ds_descricao,
            $ds_arquivo,
            $ds_foto_capa,
            $vl_musica,
            $ic_tipo_venda,
            $dt_lancamento ?: null,
            $fk_cd_projeto,
            $id_user,
            $ic_visibilidade,
            $qt_limite_vendas
        ]);
        $id_musica = $pdo->lastInsertId();

        //inserir genero na tavela musica_genero se existir fk_id_genero
        if ($fk_id_genero) {
            $stmt = $pdo->prepare("INSERT INTO musica_genero (fk_id_musica, fk_id_genero) VALUES (?, ?)");
            $stmt->execute([$id_musica, $fk_id_genero]);
        }

        $inserted[] = [
            'id_musica' => $id_musica,
            'nm_musica' => $nm_musica,
            'arquivo' => $ds_arquivo
        ];
    } // end loop files execute

    $pdo->commit();
    echo json_encode(['success'=>true, 'message'=>'Músicas enviadas com sucesso','data'=>$inserted]);
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['sucess'=>false,'message'=>'erro: '.$e->getMessage()]);
    exit;
}

header("Location: profile.php?upload=sucesso");
exit;

?>