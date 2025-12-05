<?php
// config.php
// ajuste os dados conforme seu ambiente
$host = '127.0.0.1';
$db   = 'ostra';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, $opts);
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro conexão DB: " . $e->getMessage();
    exit;
}


// Exemplo: $_SESSION['id_usuario'] e $_SESSION['nm_nome'] devem existir quando o usuário estiver logado
// Para testes, defina manualmente:
// $_SESSION['id_usuario'] = 1;
// $_SESSION['nm_nome'] = 'miguel';


//feito para usar no upload