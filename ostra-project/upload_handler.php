<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Manipulador de upload de fotos de perfil e capa

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {
    header("Location: signin.php");
    exit();
}

include 'db.php';

$usuario_id = $_SESSION["usuario_id"];
$redirect_url = 'profile.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'upload_image') {
        
        $file_key = 'image_file';
        
        if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
            $error_msg = 'Erro no upload do arquivo. Código: ' . (isset($_FILES[$file_key]['error']) ? $_FILES[$file_key]['error'] : 'Desconhecido');
            header("Location: {$redirect_url}?upload=error&msg=" . urlencode($error_msg));
            exit();
        }

        $image_type = isset($_POST['image_type']) ? $_POST['image_type'] : '';

        if ($image_type !== 'profile' && $image_type !== 'cover') {
            header("Location: {$redirect_url}?upload=error&msg=" . urlencode('Tipo de imagem inválido.'));
            exit();
        }

        $file_tmp_path = $_FILES[$file_key]['tmp_name'];
        $file_extension = pathinfo(basename($_FILES[$file_key]['name']), PATHINFO_EXTENSION);
        
        $sub_dir = ($image_type === 'profile') ? 'profile_photos' : 'cover_photos';
        
        // Caminho relativo a partir da raiz do projeto
        $upload_dir = 'uploads/' . $sub_dir . '/';
        
        $unique_file_name = $image_type . "_" . $usuario_id . "_" . time() . "." . $file_extension;
        $file_destination = $upload_dir . $unique_file_name;
        
        // Cria o diretório se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Verifica se o diretório tem permissão de escrita
        if (!is_writable($upload_dir)) {
            $error_msg = 'Erro de permissão: O diretório ' . $upload_dir . ' não tem permissão de escrita. Verifique as permissões da pasta no seu servidor.';
            header("Location: {$redirect_url}?upload=error&msg=" . urlencode($error_msg));
            exit();
        }

        if (move_uploaded_file($file_tmp_path, $file_destination)) {
            
            $local_url = $file_destination; // Salva o caminho completo relativo
            
            $db_column = ($image_type === 'profile') ? 'ds_foto_perfil' : 'ds_foto_capa';
            
            $sql_update = "UPDATE usuarios SET {$db_column} = ? WHERE id_usuario = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $local_url, $usuario_id);

            if ($stmt_update->execute()) {
                header("Location: {$redirect_url}?upload=success&type={$image_type}");
                exit();
            } else {
                $error_msg = 'Erro ao salvar no banco de dados: ' . $conn->error;
                header("Location: {$redirect_url}?upload=error&msg=" . urlencode($error_msg));
                exit();
            }
            $stmt_update->close();
        } else {
            $error_msg = 'Erro final ao mover o arquivo. Verifique as configurações do PHP e as permissões da pasta.';
            header("Location: {$redirect_url}?upload=error&msg=" . urlencode($error_msg));
            exit();
        }
    }
}

header("Location: {$redirect_url}");
exit();
?>
