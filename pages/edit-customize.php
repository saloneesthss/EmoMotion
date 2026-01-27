<?php
session_start();
require_once "../connection.php";
require_once "../components/user-navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$plan_id = (int) $_GET['id'];

$sql = "SELECT * FROM customized_plans WHERE id = ? AND user_id = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$plan_id, $user_id]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    echo "Plan not found or unauthorized.";
    exit;
}

$video_list = json_decode($plan['video_list'], true);

$stmt = $con->prepare("SELECT * FROM workout_videos");
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = $_POST['plan_name'];
    $target = $_POST['target'];
    $mood = $_POST['mood'];
    $intensity = $_POST['intensity'];
    $level = $_POST['level'];

    $updated_video_list = $_POST['video_list'];
    $total_duration = $_POST['total_duration'];
    $days = $_POST['days'];
    $description = $_POST['description'];

    $thumbnailPath = $plan['file_path']; 

    if (!empty($_FILES['image']['name'][0])) {
        $uploadDir = "../assets/userplan-thumbnail/";
        $fileName = basename($_FILES['image']['name'][0]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'][0], $targetPath)) {
            $thumbnailPath = $fileName;
        }
    }

    $sql = "UPDATE customized_plans 
            SET plan_name=?, file_path=?, video_list=?, target_area=?, mood=?, intensity=?, 
                fitness_level=?, time_duration=?, duration=?, description=?
            WHERE id=? AND user_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([
        $plan_name, $thumbnailPath, $updated_video_list, $target, $mood, $intensity, 
        $level, $total_duration, $days, $description, $plan_id, $user_id
    ]);

    header("Location: customized-details.php?id=" . $plan_id);
    exit();
}
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
            <h2 class="title">Edit Customized Plan</h2>

            <div class="input-block">
                <label>Plan name</label>
                <input type="text" name="plan_name" value="<?= htmlspecialchars($plan['plan_name']) ?>" placeholder="Enter the name of your plan" required>
            </div>

            <!-- More Settings Toggle -->
            <div class="more-settings-header" onclick="toggleSettings()">
                <span>More settings</span>
                <span id="arrow">▼</span>
            </div>

            <!-- More Settings Content -->
            <div class="more-settings-content" id="moreSettings">
                <div class="attach-row">
                    <?php if ($plan['file_path']) : ?>
                        <img src="../assets/userplan-thumbnail/<?= $plan['file_path'] ?>" style="width:120px;">
                    <?php endif; ?>
                    
                    <input type="file" id="image" name="image[]" multiple accept=".jpg, .jpeg, .png, .webp" style="display: none;" required>
                    <label for="image" class="attach-link">+ Change Thumbnail</label> 
                </div>
                <div class="settings-section">
                    <h4>Target Area</h4>
                    <?php foreach (["Waist","Hips","Abs","Legs","Arms","Back","Full Body"] as $t): ?>
                        <label>
                            <input type="radio" name="target" value="<?= $t ?>" 
                                <?= $plan['target_area'] == $t ? "checked" : "" ?>>
                            <?= $t ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="settings-section">
                    <h4>Mood</h4>
                    <?php foreach (["Happy","Sad","Angry","Tired","Energized"] as $m): ?>
                        <label>
                            <input type="radio" name="mood" value="<?= $m ?>" 
                                <?= $plan['mood'] == $m ? "checked" : "" ?>>
                            <?= $m ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="settings-section">
                    <h4>Intensity</h4>
                    <?php foreach (["Low","Medium","High"] as $i): ?>
                        <label>
                            <input type="radio" name="intensity" value="<?= $i ?>" 
                                <?= $plan['intensity'] == $i ? "checked" : "" ?>>
                            <?= $i ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="settings-section">
                    <h4>Fitness Level</h4>
                    <?php foreach (["Beginner","Intermediate","Advanced"] as $l): ?>
                        <label>
                            <input type="radio" name="level" value="<?= $l ?>" 
                                <?= $plan['fitness_level'] == $l ? "checked" : "" ?>>
                            <?= $l ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <textarea class="description" name="description" placeholder="Enter your plan details here..."><?= htmlspecialchars($plan['description']) ?></textarea>
            </div>

            <!-- Plan Detail -->
            <div class="input-block">
                <label>Plan detail</label>
            </div>

            <div class="routine-box">
                <div class="routine-header">
                    <span>Videos List</span>
                    <span>
                        <span id="daysBox" name="days"><?= $plan['duration'] ?></span> Days 
                        <span id="editDays" style="cursor:pointer;">✎</span>
                    </span>
                    <input type="hidden" id="daysInput" name="days" value="<?= $plan['duration'] ?>">

                    <span class="estimate">0:00 • 0 exercise</span>
                </div>
            </div>

            <input type="hidden" name="video_list" id="video_list">
            <input type="hidden" name="total_duration" id="total_duration">

            <div class="btn-row">
                <button type="reset" class="btn cancel" onclick="location.href='customized-details.php?id=<?= $plan_id ?>'">Cancel</button>
                <button type="submit" class="btn">Update</button>
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

    exerciseItems.forEach(item => {
        const title = item.querySelector("h4").textContent.toLowerCase();
        const target = item.querySelector("p").textContent.toLowerCase();
        const itemTarget = item.dataset.target?.toLowerCase() || "";
        if (
            itemTarget.includes(selectedTarget) 
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

let existingVideos = <?= json_encode($video_list) ?>;
window.addEventListener("DOMContentLoaded", () => {
    const routineBox = document.querySelector(".routine-box");
    existingVideos.forEach(file => {
        let video = window.allVideos.find(v => v.file_path === file);
        if (!video) return;
        createCard(video);
    });
    updateEstimate();
});

window.allVideos = <?= json_encode($videos) ?>;

function createCard(video) {
    const routineBox = document.querySelector(".routine-box");

    const card = document.createElement("div");
    card.classList.add("exercise-card");

    card.dataset.duration = video.duration;
    card.dataset.file = video.file_path;

    card.innerHTML = `
        <span class="close-btn">×</span>
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
    
    card.querySelector(".close-btn").addEventListener("click", () => {
        card.remove();
        updateEstimate();
    });

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
</script>
</body>
</html>
