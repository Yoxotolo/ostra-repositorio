<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha o tipo de conta - OSTRA</title>
    <link rel="stylesheet" href="styles.css">
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
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Personagem Pixel Art - Viber -->
                            <rect x="45" y="20" width="30" height="10" fill="#8B4513"/>
                            <rect x="40" y="30" width="40" height="25" fill="#FFD4A3"/>
                            <rect x="48" y="38" width="6" height="6" fill="#000"/>
                            <rect x="66" y="38" width="6" height="6" fill="#000"/>
                            <rect x="35" y="55" width="50" height="35" fill="#4169E1"/>
                            <rect x="30" y="60" width="10" height="25" fill="#FFD4A3"/>
                            <rect x="80" y="60" width="10" height="25" fill="#FFD4A3"/>
                            <rect x="40" y="90" width="15" height="20" fill="#2F4F4F"/>
                            <rect x="65" y="90" width="15" height="20" fill="#2F4F4F"/>
                            <!-- Espada -->
                            <rect x="90" y="50" width="4" height="30" fill="#C0C0C0"/>
                            <rect x="88" y="48" width="8" height="4" fill="#FFD700"/>
                        </svg>
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
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Personagem Pixel Art - Produtor -->
                            <rect x="45" y="15" width="30" height="15" fill="#FF1493"/>
                            <rect x="40" y="30" width="40" height="25" fill="#FFD4A3"/>
                            <rect x="48" y="38" width="6" height="6" fill="#000"/>
                            <rect x="66" y="38" width="6" height="6" fill="#000"/>
                            <rect x="35" y="55" width="50" height="35" fill="#8B008B"/>
                            <!-- Tentáculos -->
                            <path d="M 30 70 Q 20 80 25 95" stroke="#FF1493" stroke-width="6" fill="none"/>
                            <path d="M 40 75 Q 30 85 35 100" stroke="#FF1493" stroke-width="6" fill="none"/>
                            <path d="M 80 75 Q 90 85 85 100" stroke="#FF1493" stroke-width="6" fill="none"/>
                            <path d="M 90 70 Q 100 80 95 95" stroke="#FF1493" stroke-width="6" fill="none"/>
                            <!-- Fones de ouvido -->
                            <rect x="25" y="35" width="8" height="12" rx="4" fill="#00CED1"/>
                            <rect x="87" y="35" width="8" height="12" rx="4" fill="#00CED1"/>
                            <path d="M 33 41 Q 60 30 87 41" stroke="#00CED1" stroke-width="3" fill="none"/>
                        </svg>
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

