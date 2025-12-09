<?php
// Inicia a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: signin.php");
    exit();
}

include 'db.php';

$usuario_id = $_SESSION["usuario_id"];

// Lógica para carregar os dados do usuário para o avatar
$sql = "SELECT ds_foto_perfil FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$profile_photo = $user['ds_foto_perfil'] ?? 'assets/default-avatar.svg';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - OSTRA</title>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style/feed.css">

</head>
<body class="feed-page">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>


    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <?php include 'header.php'; ?>


    <!--#region-->

        <div>

            <div class="no-one">
                <div class="one"><img style="  object-fit: cover; width: 100%;" src="assets/Feed-Banner-B.png" alt=""></div>
            </div>


        <!-- #region Swipe region -->

            <div class="swipe">

                    
                    <div class="albuns">
                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>

                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>

                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>

                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>

                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>
                        
                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>

                        <div class="album">
                            <div class="cover"><!-- Cubo --></div>
                            <h1><b>Album Name</b></h1>
                            <p>Artist Name</p>
                        </div>



                    <div>

                    </div>

                </div>
            </div>

        <!-- #endregion -->
            
            <div class="divisor">
                <img src="assets/divisor.svg" style="width: 100%; height: 12vh;" alt="">
            </div>

            <div class="cat-wall">

            </div>

            <div class="divisor">
                <img src="assets/divisor.svg" style="width: 100%; height: 12vh;" alt="">
            </div>

            <div class="gender-div">
                <h2>Generos e Ambientações Recomendas</h2>

                <div class="genero-carrousel">
                    <div class="generos">
                        <div class="uper"></div>
                        <div class="downer"><p><b>Vocaloid</b></p></div>
                    </div>

                    <div class="generos">
                        <div class="uper"></div>
                        <div class="downer"><p><b>Eletronica</b></p></div>
                    </div>

                    <div class="generos">
                        <div class="uper"></div>
                        <div class="downer"><p><b>Everson Zoio</b></p></div>
                    </div>
                </div>
            </div>

            <div class="divisor">
                <img src="assets/divisor.svg" style="width: 100%; height: 12vh;" alt="">
            </div>

            <div class="magic-shell">
                <div class="clowster">
                    <div class="big-banner">
                        <img class="banner-img" src="assets/tester-clowster.png" alt="">
                    </div>
                    <div class="info-banner">
                        <div style="display: flex; flex-direction: column; margin: 0% 0% 5% 0%">
                            <h2>Album/Music Name</h2>
                            <p>By: Artist Name</p>
                        </div>
                        
                        <div class="descricao">
                            <p><b>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</b></p>
                        </div>

                        <div class="about">
                                <p><b>Tipo:</b> Compra Unica</p>
                                <p><b>Preço:</b> R$ 69</p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="thing">
                <h2>Ostra tem mais de -1 Musicas <br> para o seu agrado</h2>
            </div>

            <div class="divisor">
                <img src="assets/divisor.svg" style="width: 100%; height: 12vh;" alt="">
            </div>

            <div class="cards">
                <div class="drop">
                    <select name="" id="">
                        <option value="">Melhores do dia</option>
                        <option value="">Melhores do semana</option>
                        <option value="" selected>Melhores do mes</option>
                        <option value="">Melhores do ano</option>
                    </select>

                    <select name="" id="">
                        <option value="" disabled selected hidden>Generos</option>
                        <option value="">Vocaloid</option>
                        <option value="">Horror</option>
                        <option value="">Adventure</option>
                    </select>
                </div>
                <div class="cards-content">

                    <div class="cards-collum">
                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>
                    </div>

                    <div class="cards-collum">
                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>

                        <div class="card">
                            <div class="card-L">
                                <img src="assets/default-cover-icon.png" alt="">
                            </div>
                            <div class="card-R">
                                <div class="R-info">
                                    <div class="R-name">
                                        <h2>Album Name</h2>
                                        <p>By: Artist Name</p>  
                                    </div>
                                    <div class="R-about">
                                        <h2>R$ 00,</h2><h2 class="dim">99</h2>
                                    </div>
                                </div>
                                

                                <div class="descricao-card">
                                    <p><b>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid, non id itaque impedit fugiat eligendi</b></p>
                                </div>
                            </div>   
                        </div>
                    </div>

                </div>
            </div>

        </div>

    <!--#endregion-->

    </main>
</body>
</html>
