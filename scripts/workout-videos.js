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
                ${exercise.bodyPart}
            </div>

            <div class="exercise-target">
                ${exercise.target}
            </div>

            <button class="view-more-button"
            data-exercise-id="${exercise.id}">
                View More
            </button>
        </div>
    `;
})

document.querySelector('.exercise-grid').innerHTML = exercisesHTML;
