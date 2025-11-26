<?php
include 'db.php';

// Inicia a sessão para futuras funcionalidades de login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validação de campos
    if (empty($_POST['nome']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm-password']) || !isset($_POST['terms'])) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos e os Termos de Uso aceitos.";
    } else {
        $nome = $_POST['nome'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $senha = $_POST['password'];
        $confirmSenha = $_POST['confirm-password'];

        // Validação básica
        if ($senha !== $confirmSenha) {
            $erro = "As senhas não coincidem!";
        } else {
            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Verificar se o email ou username já existe (usando prepared statement)
            $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE ds_email = ? OR nm_username = ?");
            $stmt_check->bind_param("ss", $email, $username);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $erro = "Email ou username já cadastrado!";
            } else {
                // Inserir no banco como produtor (usando prepared statement)
                $stmt_insert = $conn->prepare("INSERT INTO usuarios (nm_nome, nm_username, ds_email, ds_senha, ic_tipo_usuario) VALUES (?, ?, ?, ?, 'produtor')");
                $stmt_insert->bind_param("ssss", $nome, $username, $email, $senhaHash);

                if ($stmt_insert->execute()) {
                    // Redirecionar para a página de login após o sucesso
                    header("Location: signin.php?cadastro=sucesso");
                    exit();
                } else {
                    $erro = "Erro ao cadastrar: " . $conn->error;
                }
                $stmt_insert->close();
            }
            $stmt_check->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Para Produtor - OSTRA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="signup-page">
    <div class="signup-container">
        <div class="signup-header">
            <h1 class="signup-title">Cadastro Para Produtor</h1>
        </div>

        <?php if(isset($erro)): ?>
            <p style='color:red; text-align:center; margin-top: 10px; font-weight: bold;'><?php echo $erro; ?></p>
        <?php endif; ?>
        <?php if(isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
            <p style='color:green; text-align:center; margin-top: 10px; font-weight: bold;'>Cadastro realizado com sucesso! Faça login abaixo.</p>
        <?php endif; ?>
        

        <form class="signup-form" method="POST" action="">
            <div class="form-group">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" id="nome" name="nome" class="form-input" placeholder="Seu Nome" required>
            </div>

<div class="form-group">
	                <label for="username" class="form-label">Nome de Usuário</label>
	                <input type="text" id="username" name="username" class="form-input" placeholder="@username" required>
	            </div>

            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="seu@email.com" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="confirm-password" class="form-label">Confirme a Senha</label>
                <input type="password" id="confirm-password" name="confirm-password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">Aceito os <a href="#" target="_blank">Termos de Uso</a>.</label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit">Cadastrar</button>
                <span class="form-footer-text">Já possui uma <a href="signin.php">conta?</a></span>
            </div>
        </form>
    </div>
</body>
</html>
