<?php

 #region Iniciar Sessão 

session_start();

if (!isset($_SESSION['usuario_id'])){
    echo 'Voce precisa estar logado';
    exit;
}

$usuarioId = $_SESSION['usuario_id'];

include 'db.php';

 #endregion 

if (isset($_FILES['foto'])) {

$stmt = $conn->prepare("SELECT ds_foto_perfil FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($old_pfp);
$stmt->fetch();
$stmt->close();

 #region Verificar se houve algum erro ao enviar o arquivo 

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo 'erro ao enviar arquivo';
    exit;
}

 #endregion  



 #region Marca o local que o arquivo ira, e se ele nao existir ele cria as pastas 

$uploadDir = "uploads/profile_photos/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

 #endregion 



 #region Verifica se a extensão do arquivo é valida ou não 

$extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

$permitidas = ['jpg', 'png', 'jpeg', 'gif'];

if (!in_array($extensao, $permitidas)) {
    echo "Formato de arquivo não permitida.";
    exit;
}

 #endregion 



 #region Nomeia o arquivo

$newName = "perfil_" . $usuarioId . "_" . time() . "." . $extensao;

 #endregion 



 #region Move o arquivo para o local correto com o novo nome dele

$caminho = $uploadDir . $newName;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) 
{
    echo "Falha ao mover o arquivo.";
    exit;
}

 #endregion 



 #region 
/*
if ($old_pfp && file_exists($old_pfp) && $old_pfp !== "assets/default-avatar.png") {
    unlink($old_pfp);
}
*/

$sql = "UPDATE usuarios SET ds_foto_perfil = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $caminho, $usuarioId);
$stmt->execute();

 #endregion

} else if (isset($_FILES['banner'])) {

 #region Verificar se houve algum erro ao enviar o arquivo 

if (!isset($_FILES['banner']) || $_FILES['banner']['error'] !== UPLOAD_ERR_OK) {
    echo 'erro ao enviar arquivo (Banner)';
    exit;
}

 #endregion  



 #region Marca o local que o arquivo ira, e se ele nao existir ele cria as pastas 

$uploadDir = "uploads/profile_banner/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

 #endregion 



 #region Verifica se a extensão do arquivo é valida ou não 

$extensao = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));

$permitidas = ['jpg', 'png', 'jpeg', 'gif'];

if (!in_array($extensao, $permitidas)) {
    echo "Formato de arquivo não permitida.";
    exit;
}

 #endregion 



 #region Nomeia o arquivo

$newName = "perfil_" . $usuarioId . "_" . time() . "." . $extensao;

 #endregion 



 #region Move o arquivo para o local correto com o novo nome dele

$caminho = $uploadDir . $newName;

if (!move_uploaded_file($_FILES['banner']['tmp_name'], $caminho)) 
{
    echo "Falha ao mover o arquivo.";
    exit;
}

 #endregion 



 #region 

$sql = "UPDATE usuarios SET ds_foto_capa = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $caminho, $usuarioId);
$stmt->execute();

 #endregion

}

header("Location: profile.php?upload=sucesso");
exit;

?>