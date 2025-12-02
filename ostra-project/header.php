<?php
include 'db.php';

$stmt = $conn->prepare("SELECT ds_foto_perfil FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($profile_photo);
$stmt->fetch();

if ($profile_photo == NULL) {
    $profile_photo = "assets/default-avatar.png";
}

?>

        <header class="top-header">
            <div class="search-container">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input type="text" class="search-input" placeholder="Search Bar . . .">
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php if ($profile_photo !== "assets/default-avatar.png" ): ?>
                        <img src="<?php echo $profile_photo; ?>" alt="Avatar" style="">
                    <?php else: ?>
                        <img src="assets/default-avatar.png" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>
        </header>