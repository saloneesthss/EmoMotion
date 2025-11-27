import { exercises } from "../data/exercises.js";

let exercisesHTML = '';
exercises.forEach((exercise) => {
    exercisesHTML += `
        <div class="exercise-card">
            <div class="exercise-image-container">
                <img class="exercise-image"
                src="${exercise.gifUrl}">
            </div>

            <div class="exercise-name">
                ${exercise.name}
            </div>

            <div class="exercise-price">
                body part: ${exercise.bodyPart}
            </div>

            <div class="exercise-target">
                target: ${exercise.target}
            </div>

            <div class="exercise-equipment">
                equipment: ${exercise.equipment}
            </div>

            <button class="view-more-button"
            data-exercise-id="${exercise.id}">
                View More
            </button>
        </div>
    `;
})

document.querySelector('.exercise-grid').innerHTML = exercisesHTML;

const btn = document.querySelector('.collection-button');
const menu = document.querySelector('.collection-menu');

btn.addEventListener('click', () => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

document.addEventListener('click', (event) => {
    if (!btn.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = "none";
    }
});

menu.querySelectorAll('li').forEach(item => {
    item.addEventListener('click', () => {
        const filterValue = item.dataset.filter;
        btn.innerText = item.innerText + " ▾";
        menu.style.display = "none";
        filterVideos(filterValue);
    });
});

function filterVideos(filter) {
    console.log("Filtering videos by:", filter);
}
