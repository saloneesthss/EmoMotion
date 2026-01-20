
function createCard(video) {
    const routineBox = document.querySelector(".routine-box");

    const card = document.createElement("div");
    card.classList.add("exercise-card");

    card.dataset.duration = video.duration;
    card.dataset.file = video.file_path;

    card.innerHTML = `
        <img src="../assets/gifs/${video.file_path}" class="video-thumb">
        <div class="exercise-info">
            <h3>${video.title}</h3>
            <div class="set-row">
                <div>${video.target_area}</div>
                <input value="${video.duration} sec" readonly>
                <input value="10 sec rest" readonly>
                <input value="${video.repetition} rep" readonly>
                <input value="${video.sets} sets" readonly>
            </div>
        </div>
    `;

    routineBox.appendChild(card);
}

document.querySelectorAll(".add-video-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        let video = {
            title: this.dataset.title,
            target_area: this.dataset.target,
            duration: this.dataset.duration,
            repetition: this.dataset.rep,
            sets: this.dataset.sets,
            file_path: this.dataset.file
        };
        createCard(video);
        updateEstimate();
    });
});

function updateEstimate() {
    const cards = document.querySelectorAll(".exercise-card");

    let totalSec = 0;
    let count = cards.length;

    cards.forEach(card => {
        totalSec += parseInt(card.dataset.duration);
    });

    let min = Math.floor(totalSec / 60);
    let sec = totalSec % 60;

    document.querySelector(".estimate").textContent =
        `${min}:${sec.toString().padStart(2, "0")} • ${count} exercise${count > 1 ? "s" : ""}`;
}

document.querySelector("form").addEventListener("submit", () => {
    let cards = document.querySelectorAll(".exercise-card");
    let files = [];
    let totalSec = 0;

    cards.forEach(card => {
        files.push(card.dataset.file);
        totalSec += parseInt(card.dataset.duration);
    });

    document.getElementById("video_list").value = JSON.stringify(files);
    document.getElementById("total_duration").value = totalSec;
});
