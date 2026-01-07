function handleFavoriteClick(videoId, iconElement) {
    let loggedIn = iconElement.getAttribute("data-loggedin");

    if (loggedIn === "0") {
        window.location.href = "../login.php";
        return;
    }

    toggleFavorite(videoId);
}

function toggleFavorite(exerciseId) {
    let favorites = JSON.parse(localStorage.getItem("favoriteExercises")) || {};
    let card = document.querySelector(`.exercise-card[data-id='${exerciseId}']`);

    let icon = document.getElementById("fav-" + exerciseId);

    if (!card || !icon) return;
    if (favorites[exerciseId]) {
        delete favorites[exerciseId];
        icon.classList.remove("favorited");
    } else {
        favorites[exerciseId] = {
            id: exerciseId,
            name: card.dataset.name,
            video: card.dataset.video,
            img: card.dataset.img
        };
        icon.classList.add("favorited");
    }
    localStorage.setItem("favoriteExercises", JSON.stringify(favorites));
}

const button = document.querySelector('.collection-button');
const menu = document.querySelector('.collection-menu');

button.addEventListener('click', () => {
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', (e) => {
    if (!button.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});