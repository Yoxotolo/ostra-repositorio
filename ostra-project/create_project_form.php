<?php
// Este arquivo será incluído em upload_musicas.php

// A variável $usuario_id é esperada do arquivo principal (upload_musicas.php)
// A variável $profile_type não é estritamente necessária aqui, mas é mantida por consistência se o arquivo for usado em outros lugares.
$profile_type = 'produtor'; // Forçando 'produtor' já que upload_musicas.php só é acessível a produtores

if ($profile_type === 'produtor'): ?>
<div class="create-project-container">
    <h2 class="card-title">Detalhes do Novo Projeto</h2>
    <p class="card-subtitle">Crie um álbum ou coleção para agrupar suas músicas.</p>

    <!-- FORMULÁRIO APONTA PARA O MANIPULADOR UNIFICADO -->
    <form action="./upload_handler.php" method="POST" enctype="multipart/form-data" class="upload-form">

        <input type="hidden" name="action" value="create_project">

        <div class="form-group">
            <label for="nm_projeto">Nome do Projeto/Álbum</label>
            <input type="text" id="nm_projeto" name="nm_projeto" required>
        </div>

        <div class="form-group">
            <label for="ds_projeto">Descrição do Projeto (Opcional)</label>
            <textarea id="ds_projeto" name="ds_projeto"></textarea>
        </div>
        
        <button type="submit" class="btn-primary">Criar Projeto</button>
    </form>
</div>
<?php endif; ?>
