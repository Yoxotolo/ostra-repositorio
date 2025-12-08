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


// #endregion
