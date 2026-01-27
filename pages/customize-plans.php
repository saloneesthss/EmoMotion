<?php
session_start();
require_once "../connection.php";
require_once "../components/user-navbar.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = $_POST['plan_name'];
    $target = $_POST['target'] ?? null;
    $mood = $_POST['mood'];
    $intensity = $_POST['intensity'];
    $level = $_POST['level'];

    $video_list = $_POST['video_list']; // json
    $total_duration = $_POST['total_duration']; // seconds
    $days = $_POST['days'];
    $description = $_POST['description'];

    // HANDLE THUMBNAIL UPLOAD
    $thumbnailPath = null;

    if (!empty($_FILES['image']['name'][0])) {
        $uploadDir = "../assets/userplan-thumbnail/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['image']['name'][0]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'][0], $targetPath)) {
            $thumbnailPath = $fileName; 
        }
    }

    $sql = "INSERT INTO customized_plans 
        (user_id, plan_name, file_path, video_list, target_area, mood, intensity, fitness_level, time_duration, duration, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id, $plan_name, $thumbnailPath, $video_list, $target, $mood, $intensity, $level, $total_duration, $days, $description]);

    header("Location: users-db.php");
    exit();
}

$sql = "SELECT * FROM workout_videos";
$stmt = $con->prepare($sql);
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/customize-plans.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <!-- MAIN CONTENT -->
    <form action="" method="post" enctype="multipart/form-data">
        <div class="content">
            <h2 class="title">Customize Your Own Plan</h2>

            <div class="input-block">
                <label>Plan name</label>
                <input type="text" name="plan_name" placeholder="Enter the name of your plan" required>
            </div>

            <!-- More Settings Toggle -->
            <div class="more-settings-header" onclick="toggleSettings()">
                <span>More settings</span>
                <span id="arrow">▼</span>
            </div>

            <!-- More Settings Content -->
            <div class="more-settings-content" id="moreSettings">
                <div class="attach-row">
                    <input type="file" id="image" name="image[]" multiple accept=".jpg, .jpeg, .png, .webp" style="display: none;" required>
                    <label for="image" class="attach-link">+ Attach Thumbnail</label>
                </div>
                <div class="settings-section">
                    <h4>Target Area</h4>
                    <label><input type="radio" name="target" value="Waist" checked> Waist</label>
                    <label><input type="radio" name="target" value="Hips"> Hips</label>
                    <label><input type="radio" name="target" value="Abs"> Abs</label>
                    <label><input type="radio" name="target" value="Legs"> Legs</label>
                    <label><input type="radio" name="target" value="Arms"> Arms</label>
                    <label><input type="radio" name="target" value="Back"> Back</label>
                    <label><input type="radio" name="target" value="Full Body"> Full Body</label>
                </div>

                <div class="settings-section">
                    <h4>Mood</h4>
                    <label><input type="radio" name="mood" value="Happy" checked> Happy</label>
                    <label><input type="radio" name="mood" value="Sad"> Sad</label>
                    <label><input type="radio" name="mood" value="Angry"> Angry</label>
                    <label><input type="radio" name="mood" value="Tired"> Tired</label>
                    <label><input type="radio" name="mood" value="Energized"> Energized</label>
                </div>

                <div class="settings-section">
                    <h4>Intensity</h4>
                    <label><input type="radio" name="intensity" value="Low" checked> Low</label>
                    <label><input type="radio" name="intensity" value="Medium"> Medium</label>
                    <label><input type="radio" name="intensity" value="High"> High</label>
                </div>

                <div class="settings-section">
                    <h4>Fitness Level</h4>
                    <label><input type="radio" name="level" value="Beginner" checked> Beginner</label>
                    <label><input type="radio" name="level" value="Intermediate"> Intermediate</label>
                    <label><input type="radio" name="level" value="Advanced"> Advanced</label>
                </div>

                <textarea class="description" name="description" placeholder="Enter your plan details here..."></textarea>
            </div>

            <!-- Plan Detail -->
            <div class="input-block">
                <label>Plan detail</label>
            </div>

            <div class="routine-box">
                <div class="routine-header">
                    <span>Videos List</span>
                    <span>
                        <span id="daysBox" name="days">30</span> Days 
                        <span id="editDays" style="cursor:pointer;">✎</span>
                    </span>
                    <input type="hidden" id="daysInput" name="days" value="30">

                    <span class="estimate">0:00 • 0 exercise</span>
                </div>
            </div>

            <input type="hidden" name="video_list" id="video_list">
            <input type="hidden" name="total_duration" id="total_duration">
            <input type="hidden" name="days" id="days">

            <div class="btn-row">
                <button type="reset" class="btn cancel">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </div>
    </form>

    <!-- RIGHT EXERCISE LIBRARY -->
    <div class="exercise-library">
        <div class="library-header">
            <h3>Exercise library</h3>
            <input type="text" placeholder="Search exercise name">
        </div>

        <?php foreach ($videos as $gif): ?>
        <div class="exercise-item"
            data-target="<?= htmlspecialchars($gif['target_area']) ?>">
            <img src="../assets/gifs/<?php echo htmlspecialchars($gif['file_path']); ?>" 
                class="video-thumb"
                alt="Video Thumbnail">
            <div>
                <h4><?= htmlspecialchars($gif['title']) ?></h4>
                <p><?= htmlspecialchars($gif['target_area']) ?></p>
            </div>
            <button class="add-video-btn"
                data-id="<?= $gif['id'] ?>"
                data-title="<?= htmlspecialchars($gif['title']) ?>"
                data-target="<?= htmlspecialchars($gif['target_area']) ?>"
                data-duration="<?= htmlspecialchars($gif['duration']) ?>"
                data-rep="<?= htmlspecialchars($gif['repetition']) ?>"
                data-sets="<?= htmlspecialchars($gif['sets']) ?>"
                data-file="<?= htmlspecialchars($gif['file_path']) ?>"
            >+</button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="../scripts/customize-plans.js"></script>
<script>
const searchInput = document.querySelector(".library-header input");
const exerciseItems = document.querySelectorAll(".exercise-item");
const filters = {
    target: document.getElementsByName("target")
};

function getSelectedValue(radioNodeList) {
    for (let radio of radioNodeList) {
        if (radio.checked) return radio.value.toLowerCase();
    }
    return "";
}

function filterExercises() {
    const selectedTarget = getSelectedValue(filters.target);
    // const selectedMood = getSelectedValue(filters.mood);
    // const selectedIntensity = getSelectedValue(filters.intensity);
    // const selectedLevel = getSelectedValue(filters.level);

    exerciseItems.forEach(item => {
        const title = item.querySelector("h4").textContent.toLowerCase();
        const target = item.querySelector("p").textContent.toLowerCase();
        const itemTarget = item.dataset.target?.toLowerCase() || "";
        // const itemMood = item.dataset.mood?.toLowerCase() || "";
        // const itemIntensity = item.dataset.intensity?.toLowerCase() || "";
        // const itemLevel = item.dataset.level?.toLowerCase() || "";

        if (
            itemTarget.includes(selectedTarget) 
            // itemMood.includes(selectedMood) &&
            // itemIntensity.includes(selectedIntensity) &&
            // itemLevel.includes(selectedLevel)
        ) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
}
for (let group in filters) {
    filters[group].forEach(radio => {
        radio.addEventListener("change", filterExercises);
    });
}
filterExercises();

searchInput.addEventListener("keyup", function() {
    const query = this.value.toLowerCase().trim();
    exerciseItems.forEach(item => {
        const title = item.querySelector("h4").textContent.toLowerCase();
        if (title.includes(query)) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
});

document.querySelectorAll(".add-video-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        const video = {
            title: this.dataset.title,
            target: this.dataset.target,
            duration: this.dataset.duration,
            rep: this.dataset.rep,
            sets: this.dataset.sets,
            file: this.dataset.file
        };

        const routineBox = document.querySelector(".routine-box");

        const card = document.createElement("div");
        card.classList.add("exercise-card");

        card.dataset.duration = video.duration;
        card.dataset.title = video.title;

        card.innerHTML = `
            <span class="close-btn">×</span>
            <img src="../assets/gifs/${video.file}" class="video-thumb" alt="Thumbnail">

            <div class="exercise-info">
                <h3>${video.title}</h3>

                <div class="set-row">
                    <div>${video.target}</div>
                    <input value="${video.duration} sec" readonly>
                    <input value="10 sec rest" readonly>
                    <input value="${video.rep} rep" readonly>
                    <input value="${video.sets} sets" readonly>
                </div>
            </div>
        `;
        
        card.querySelector(".close-btn").addEventListener("click", () => {
            card.remove();
            updateEstimate();
        });

        routineBox.appendChild(card);
        updateEstimate();
    });
});

function updateEstimate() {
    const cards = document.querySelectorAll(".exercise-card");

    let totalSec = 0;
    let count = cards.length;

    cards.forEach(card => {
        let duration = card.dataset.duration;
        totalSec += parseInt(duration);
    });

    let minutes = Math.floor(totalSec / 60);
    let seconds = totalSec % 60;
    let timeFormatted = `${minutes}:${seconds.toString().padStart(2, "0")}`;

    if (count <= 1) {
        document.querySelector(".estimate").textContent =
        `${timeFormatted} • ${count} exercise`;
    } else {
        document.querySelector(".estimate").textContent =
        `${timeFormatted} • ${count} exercises`;
    }
}

document.querySelector("form").addEventListener("submit", function (e) {
    let cards = document.querySelectorAll(".exercise-card");
    let videoArray = [];
    let totalSec = 0;

    cards.forEach(card => {
        videoArray.push(card.dataset.file);
        totalSec += parseInt(card.dataset.duration);
    });

    document.getElementById("video_list").value = JSON.stringify(videoArray);
    document.getElementById("total_duration").value = totalSec;
});
</script>
</body>
</html>
