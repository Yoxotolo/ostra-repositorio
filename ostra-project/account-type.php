<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha o tipo de conta - OSTRA</title>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style/entrace.css">

</head>
<body class="home-page">
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="assets/logo-icon.svg" alt="OSTRA Icon" class="logo-icon">
                <img src="assets/logo-text.svg" alt="OSTRA" class="logo-text">
            </div>
            
            <div class="header-right">
                <button class="btn-secondary" onclick="window.location.href='signin.php'">Sign In</button>
                <button class="btn-primary" onclick="window.location.href='account-type.php'">Create Account</button>
            </div>
        </div>
    </header>

    <!-- Background Image -->
    <div class="hero-background">
        <img src="assets/background-home.svg" alt="OSTRA Background">
    </div>

    <!-- Modal de Seleção de Tipo de Conta -->
    <div class="modal-overlay active">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Escolha o tipo de conta</h2>
                <button class="close-btn" onclick="window.location.href='index.php'">✕</button>
            </div>
            
            <div class="account-types">
                <!-- Card Viber -->
                <div class="account-card" onclick="window.location.href='signup-viber.php'">
                    <div class="account-card-icon">
                        <img src="assets/Viber-icon.svg" alt="">
                    </div>
                    <div class="account-card-content">
                        <button class="account-card-button">Cadastrar como viber</button>
                        <p class="account-card-description">
                            Cadastre-se como viber e curta, siga, compre e encomende obras dos seus Produtores favoritos tendo acesso para qualquer estilo de música para os mais diversos ambientes e sentimentos
                        </p>
                    </div>
                </div>

                <!-- Card Produtor -->
                <div class="account-card" onclick="window.location.href='signup-producer.php'">
                    <div class="account-card-icon">
                        <img src="assets/Producer-icon-B.svg" alt="">
                    </div>
                    <div class="account-card-content">
                        <button class="account-card-button">Cadastrar como Produtor</button>
                        <p class="account-card-description">
                            Cadastre-se como Produtor e inicie sua jornada como produtor, vendendo, licenciando e aceitando encomendas para produzir Trilhas Sonoras específicas
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

