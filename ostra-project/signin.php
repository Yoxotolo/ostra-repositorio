<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['email']; // pode ser email ou username
    $senha = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE ds_email = ? OR nm_username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($senha, $user['ds_senha'])) {
            $_SESSION['usuario_id'] = $user['id_usuario'];
            $_SESSION['usuario_nome'] = $user['nm_nome'];
            $_SESSION['usuario_tipo'] = $user['ic_tipo_usuario'];
            header("Location: profile.php");
            exit;
        } else {
            $erro = "E-mail/Username ou Senha incorreta.";
        }
    } else {
        $erro = "E-mail/Username ou Senha incorreta.";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - OSTRA</title>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style/entrace.css">

</head>
<body class="signup-page">
    <div class="signup-container">
        <div class="signup-header">
            <div class="signup-icon">
                <!-- Ícone do site -->
            </div>
            <h1 class="signup-title">Entrar na OSTRA</h1>
            <?php if(isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
                <p style='color:green; text-align:center; margin-top: 10px; font-weight: bold;'>Cadastro realizado com sucesso! Faça login abaixo.</p>
            <?php endif; ?>
        </div>

        <form class="signup-form" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="seuemail@exemplo.com" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <?php if (!empty($erro)): ?>
                <p style="color:red; text-align:center;"><?php echo $erro; ?></p>
            <?php endif; ?>

            <div class="form-footer">
                <button type="submit" class="btn-submit">Entrar</button>
                <span class="form-footer-text">
                    Não tem uma conta? <a href="account-type.php">Cadastre-se</a>
                </span>
            </div>
        </form>
    </div>
</body>
</html>
