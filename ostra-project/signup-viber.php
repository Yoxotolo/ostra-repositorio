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
        // Agora o formulário de viber tem o campo 'nome'
        $nome = $conn->real_escape_string($_POST['nome']); 
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $senha = $_POST['password'];
        $confirmSenha = $_POST['confirm-password'];

        // Validação básica
        if ($senha !== $confirmSenha) {
            $erro = "As senhas não coincidem!";
        } else {
            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Verificar se o email ou username já existe
            // Usando prepared statement para segurança (melhoria de segurança)
            $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE ds_email = ? OR nm_username = ?");
            $stmt_check->bind_param("ss", $email, $username);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $erro = "Email ou username já cadastrado!";
            } else {
                // Inserir no banco como usuário comum ('usuario')
                $stmt_insert = $conn->prepare("INSERT INTO usuarios (nm_nome, nm_username, ds_email, ds_senha, ic_tipo_usuario) VALUES (?, ?, ?, ?, 'usuario')");
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
    <title>Cadastro Para Viber - OSTRA</title>

    <link rel="stylesheet" href="style/entrace.css">
    <link rel="stylesheet" href="styles.css">

</head>
<body class="signup-page">
    <div class="signup-container">
        <!-- Header do Formulário -->
        <div class="signup-header">
            <div class="signup-icon">
                <img src="assets/AFFGGE/logo-white.svg" alt="">
            </div>
            <h1 class="signup-title">Cadastro Para Viber</h1>
            <?php if(isset($erro)): ?>
                <p style='color:red; text-align:center; margin-top: 10px; font-weight: bold;'><?php echo $erro; ?></p>
            <?php endif; ?>
        </div>

        <!-- Formulário -->
<form class="signup-form" id="signupForm" method="POST" action="">
	            <div class="form-group">
	                <label for="nome" class="form-label">Nome Completo</label>
	                <input 
	                    type="text" 
	                    id="nome" 
	                    name="nome" 
	                    class="form-input" 
	                    placeholder="Seu Nome Completo"
	                    required
	                >
	            </div>
	            
	            <div class="form-group">
	                <label for="username" class="form-label">Nome de Usuário</label>
	                <input 
	                    type="text" 
	                    id="username" 
	                    name="username" 
	                    class="form-input" 
	                    placeholder="@username"
	                    required
	                >
	            </div>
	
	            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="CanaryPrimary@LewdThing.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="CaCO3"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm-password" class="form-label">Confirme a Senha</label>
                <input 
                    type="password" 
                    id="confirm-password" 
                    name="confirm-password" 
                    class="form-input" 
                    placeholder="CaCO3"
                    required
                >
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                    I have read and agree to the <a href="#" target="_blank">Terms of Use</a>.
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit">Cadastrar</button>
                <span class="form-footer-text">
                     Ja possui uma <a href="signin.php">conta?</a>
                </span>

            </div>

            <div class="signup-note">
                Você depois nas configurações de Perfil, pode alterar a sua conta para do tipo Produtor caso queira começar a produzir e vender conteúdo na plataforma
            </div>
        </form>
    </div>

    <script>
        // Validação básica do formulário (mantida para UX, mas a validação principal é no PHP)
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Impede o envio inicial do formulário
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (password !== confirmPassword) {
                alert('As senhas não coincidem!');
                return;
            }
            
            const terms = document.getElementById('terms').checked;
            if (!terms) {
                alert('Você precisa aceitar os Termos de Uso para continuar.');
                return;
            }
            
            // Se a validação JS passar, submete o formulário via POST para o PHP
            this.submit();
        });
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (password !== confirmPassword) {
                alert('As senhas não coincidem!');
                return;
            }
            
            const terms = document.getElementById('terms').checked;
            if (!terms) {
                alert('Você precisa aceitar os Termos de Uso para continuar.');
                return;
            }
            
            // alert('Cadastro realizado com sucesso!'); // Removido, pois o PHP fará o redirecionamento ou exibirá o erro
            // A lógica de envio está sendo tratada pelo PHP no topo do arquivo.
        
    </script>
</body>
</html>

