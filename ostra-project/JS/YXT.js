// #region

document.getElementById("img-up").addEventListener("click", () => {
    document.getElementById("audioFiles").click();
});

document.getElementById("imgPfp").addEventListener("click", () => {
    document.getElementById("coverPfp").click();
});

document.addEventListener('click', function(e) {
    const opener = e.target.closest('.opener');
    if (!opener) return;

    const card = opener.closest('.card');
    const field = card.querySelector('.right-card-field');

    if (!field) return;

    // Abre/fecha
    field.style.display = (field.style.display === 'none' || field.style.display === '')
        ? 'flex'
        : 'none';
});



    document.getElementById("existingProject").addEventListener("change", function() {
        // Quando "Projeto existente" é selecionado
        document.getElementById("projetoSelect").required = true;
        document.getElementsByName("nm_projeto_novo")[0].required = false;
        document.getElementById("projectInput").style.display = "none";
        document.getElementById("coverInput").style.display = "flex";  // Esconde o campo de criação de novo projeto
    });

    document.getElementById("newProject").addEventListener("change", function() {
        // Quando "Criar novo projeto" é selecionado
        document.getElementById("projetoSelect").required = false;
        document.getElementsByName("nm_projeto_novo")[0].required = true;
        document.getElementById("projectInput").style.display = "block";
        document.getElementById("coverInput").style.display = "none";  // Mostra o campo de criação de novo projeto
    });


// #endregion
