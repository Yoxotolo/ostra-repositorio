<?php
// Manipulador de upload de músicas

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para retornar erro em JSON e sair
function return_error($message, $http_code = 400 ) {
    http_response_code($http_code );
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// Função para retornar sucesso em JSON e sair
function return_success($data = [], $http_code = 200 ) {
    http_response_code($http_code );
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => true], $data));
    exit();
}

if (!isset($_SESSION["usuario_id"])) {
    return_error('Usuário não autenticado', 401);
}

include 'db.php';

// TRATAMENTO DE ERRO DE CONEXÃO (Correção 2)
if ($conn->connect_error) {
    return_error('Falha na conexão com o banco de dados: ' . $conn->connect_error, 500);
}

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
    return_error('Apenas produtores podem fazer upload de músicas', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return_error('Método não permitido', 405);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// ============================================
// AÇÃO 1: Upload do arquivo de áudio
// ============================================
if ($action === 'upload_audio') {
    
    if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
        $error_code = isset($_FILES['audio_file']['error']) ? $_FILES['audio_file']['error'] : 'Desconhecido';
        return_error('Erro no upload do arquivo. Código: ' . $error_code);
    }

    $file_tmp_path = $_FILES['audio_file']['tmp_name'];
    $file_name = basename($_FILES['audio_file']['name']);
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_size = $_FILES['audio_file']['size'];

    // Validar extensão
    $allowed_extensions = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        return_error('Formato de arquivo não suportado');
    }

    // Validar tamanho (máximo 100MB)
    $max_size = 100 * 1024 * 1024;
    if ($file_size > $max_size) {
        return_error('Arquivo muito grande. Máximo permitido: 100MB');
    }

    // Criar diretório temporário se não existir
    $temp_dir = 'uploads/temp/';
    if (!is_dir($temp_dir)) {
        if (!mkdir($temp_dir, 0777, true)) {
            return_error('Erro ao criar diretório temporário. Verifique as permissões.');
        }
    }

    // Gerar nome único para o arquivo temporário
    $temp_file_name = 'audio_' . $usuario_id . '_' . time() . '.' . $file_extension;
    $temp_file_path = $temp_dir . $temp_file_name;

    // Mover arquivo para diretório temporário
    if (move_uploaded_file($file_tmp_path, $temp_file_path)) {
        
        // SOLUÇÃO ROBUSTA: Salvar o caminho completo na sessão
        $_SESSION['temp_audio_path'] = $temp_file_path;
        
        return_success([
            'message' => 'Arquivo de áudio recebido com sucesso'
        ]);
    } else {
        // TRATAMENTO DE ERRO MELHORADO (Correção 3)
        $php_error = error_get_last();
        $error_message = 'Erro ao mover arquivo. Detalhe: ' . ($php_error ? $php_error['message'] : 'Verifique as permissões da pasta uploads/temp/');
        return_error($error_message, 500);
    }
}

// ============================================
// AÇÃO 2: Salvar música com metadados
// ============================================
else if ($action === 'save_music') {
    
    // Recuperar dados do formulário
    $temp_file_path = isset($_POST['temp_file_path']) ? $_POST['temp_file_path'] : ''; // NOVO: Recupera o caminho completo
    $music_name = isset($_POST['music_name']) ? trim($_POST['music_name']) : '';
    $artist_name = isset($_POST['artist_name']) ? trim($_POST['artist_name']) : '';
    $release_date = isset($_POST['release_date']) ? $_POST['release_date'] : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $purchase_type = isset($_POST['purchase_type']) ? $_POST['purchase_type'] : '';

    // Validar dados obrigatórios
    if (empty($music_name) || empty($release_date) || empty($purchase_type)) {
        return_error('Campos obrigatórios não preenchidos');
    }

    if ($purchase_type !== 'unica' && $purchase_type !== 'multipla') {
        return_error('Tipo de compra inválido');
    }

    // Validar arquivo temporário
    if (empty($temp_file_path)) {
        return_error('Caminho do arquivo temporário não especificado');
    }
    
    // NOVO: Valida o caminho completo
    if (!file_exists($temp_file_path)) {
        return_error('Arquivo temporário não encontrado. Verifique as permissões ou se o arquivo foi limpo pelo servidor.');
    }

    // Se não informar artista, usar nome do usuário
    if (empty($artist_name)) {
        $sql_user = "SELECT nm_nome FROM usuarios WHERE id_usuario = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $usuario_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user_info = $result_user->fetch_assoc();
        $stmt_user->close();
        $artist_name = $user_info['nm_nome'];
    }

    // Processar imagem se fornecida
    $image_path = 'assets/default-album.svg';
    if (isset($_FILES['music_image']) && $_FILES['music_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['music_image']['tmp_name'];
        $image_name = basename($_FILES['music_image']['name']);
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_size = $_FILES['music_image']['size'];

        // Validar imagem
        $allowed_image_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($image_ext), $allowed_image_ext)) {
            return_error('Formato de imagem não suportado');
        }

        $max_image_size = 10 * 1024 * 1024;
        if ($image_size > $max_image_size) {
            return_error('Imagem muito grande');
        }

        // Criar diretório de imagens se não existir
        $image_dir = 'uploads/music_covers/';
        if (!is_dir($image_dir)) {
            if (!mkdir($image_dir, 0777, true)) {
                return_error('Erro ao criar diretório de capas');
            }
        }

        // Gerar nome único para a imagem
        $image_file_name = 'cover_' . $usuario_id . '_' . time() . '.' . $image_ext;
        $image_destination = $image_dir . $image_file_name;

        if (move_uploaded_file($image_tmp, $image_destination)) {
            $image_path = $image_destination;
        } else {
            return_error('Erro ao salvar imagem', 500);
        }
    }

    // Mover arquivo de áudio do diretório temporário para o final
    $audio_dir = 'uploads/musicas/';
    if (!is_dir($audio_dir)) {
        if (!mkdir($audio_dir, 0777, true)) {
            return_error('Erro ao criar diretório de músicas');
        }
    }

    $audio_ext = pathinfo($temp_file_path, PATHINFO_EXTENSION);
    $audio_file_name = 'music_' . $usuario_id . '_' . time() . '.' . $audio_ext;
    $audio_destination = $audio_dir . $audio_file_name;

    // Usa o caminho completo do arquivo temporário
    if (!rename($temp_file_path, $audio_destination)) {
        return_error('Erro ao processar arquivo de áudio (rename falhou)', 500);
    }
    
    // NOVO: Limpar a sessão após o uso
    unset($_SESSION['temp_audio_path']);

    // Inserir música no banco de dados
    $sql_insert = "INSERT INTO musicas (nm_musica, nm_artista, ds_arquivo, ds_foto_capa, dt_lancamento, ic_tipo_compra, fk_id_usuario, ds_descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if (!$stmt_insert) {
        return_error('Erro na preparação da query: ' . $conn->error, 500);
    }

    $stmt_insert->bind_param("ssssssss", $music_name, $artist_name, $audio_destination, $image_path, $release_date, $purchase_type, $usuario_id, $description);

    if ($stmt_insert->execute()) {
        $music_id = $conn->insert_id; // Pega o ID da música inserida
        $stmt_insert->close();
        return_success([
            'message' => 'Música salva com sucesso',
            'music_id' => $music_id
        ]);
    } else {
        $error_msg = $conn->error;
        $stmt_insert->close();
        return_error('Erro ao salvar música no banco de dados: ' . $error_msg, 500);
    }
}

// Ação não reconhecida
else {
    return_error('Ação não reconhecida');
}
?>
