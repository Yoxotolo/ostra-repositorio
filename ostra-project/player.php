<?php
// Este arquivo contém o código HTML e PHP para a sidebar de navegação.
// Ele assume que a variável de sessão $_SESSION['ic_tipo_usuario'] está definida.

// Variáveis esperadas:
// $current_page (string) - Nome do arquivo atual (ex: 'profile.php', 'feed.php')
// $is_produtor (boolean) - true se o usuário for 'produtor'

// Garante que a sessão está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_produtor = ($_SESSION['ic_tipo_usuario'] ?? '') === 'produtor';
$current_page = basename($_SERVER['PHP_SELF']);

?>
<aside>
<div id="audio-player" style="display:none;">
    <audio id="player" controls></audio>
</div>
</aside>

<script>
document.addEventListener("click", function(e) {
    // Pega o álbum clicado (qualquer parte)
    let album = e.target.closest(".musica");
    if (!album) return;

    let src = album.getAttribute("data-src");
    if (!src) return;

    let audio = document.getElementById("player");
    let box = document.getElementById("audio-player");

    audio.src = src;        // coloca o .wav
    box.style.display = "block"; 
    audio.play().catch(err => console.log(err)); // log se der erro
});
</script>