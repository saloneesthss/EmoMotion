<?php
session_start();
require_once "../connection.php";

if (isset($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}

$plan_id = (int) $_GET['id']; 
$sql = "SELECT p.*, v.title as video_title, v.file_path as video_file, v.duration as time, v.repetition, v.sets
FROM customized_plans p
JOIN workout_videos v
  ON JSON_CONTAINS(p.video_list, JSON_QUOTE(v.file_path)) WHERE p.id=$plan_id";
$stmt = $con->prepare($sql);
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$plans) {
    echo "Plan not found!";
    exit;
}

$plan = $plans[0];
$video_list = [];
foreach($plans as $row){
    if(!empty($row['video_file'])){
        $video_list[] = [
            'title' => $row['video_title'],
            'file' => $row['video_file'],
            'time' => $row['time'],
            'repetition' => $row['repetition'],
            'sets' => $row['sets'],
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/plan-details.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="page-container">
    <div class="left-sidebar">
        <?php if (!empty($plan['file_path']) && file_exists('../assets/userplan-thumbnail/' . $plan['file_path'])) { ?>
            <img class="plan-thumb" src="../assets/userplan-thumbnail/<?php echo $plan['file_path']; ?>" alt="">
        <?php } ?>
        
        <div class="left-title">
            <h2 class="plan-title"><?php echo $plan['plan_name']; ?></h2>
            <a href="edit-customize.php?id=<?php echo $plan['id']; ?>">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
        </div>

        <div class="plan-meta">
            <span><i class="fa-regular fa-calendar"></i> <?php echo $plan['duration']; ?> Days</span>
            <?php
                $seconds = $plan['time_duration'];
                $minutes = floor($seconds / 60);
                $remainingSeconds = $seconds % 60;
                if ($minutes > 0) {
                    if ($remainingSeconds > 0) {
                        $readable = $minutes . " Minute" . ($minutes > 1 ? "s " : " ") . $remainingSeconds . " Second" . ($remainingSeconds > 1 ? "s" : "");
                    } else {
                        $readable = $minutes . " Minute" . ($minutes > 1 ? "s" : "");
                    }
                } else {
                    $readable = $remainingSeconds . " Second" . ($remainingSeconds > 1 ? "s" : "");
                }
            ?>
            <span><i class="fa-solid fa-stopwatch"></i> <?= $readable ?>/Day</span>
        </div>

        <h4 class="section-label">January 2026</h4>
        <ul class="plan-features">
            <li><i class="fa-solid fa-bolt"></i> Target: <?php echo $plan['target_area']; ?></li>
            <li><i class="fa-solid fa-lightbulb"></i> Mood: <?php echo $plan['mood']; ?></li>
            <li><i class="fa-solid fa-fire"></i> Intensity: <?php echo $plan['intensity']; ?></li>
            <li><i class="fa-solid fa-cube"></i> Fitness Level: <?php echo $plan['fitness_level']; ?></li>
        </ul>

        <h4 class="section-label">Details</h4>
        <p class="plan-description">
            <?php echo $plan['description']; ?>
        </p>
    </div>

    <div class="right-content">
        <div class="content-header">
            <h1 class="main-title"><?php echo $plan['plan_name']; ?></h1>
            <button class="start-btn" onclick="openWorkoutDialog()"><i class="fa-solid fa-circle-play"></i> Start</button>
        </div>
        
        <?php foreach ($video_list as $gif): ?>
        <div class="workout-card">
            <img 
                src="../assets/gifs/<?php echo htmlspecialchars($gif['file']); ?>" 
                class="video-thumb"
                alt="Video Thumbnail">

            <div class="video-info">
                <h3><?= htmlspecialchars($gif['title']) ?></h3>
                <span class="video-tag">
                    <i class="fa-regular fa-compass"></i> 
                    <?= htmlspecialchars($plan['target_area']) ?>
                </span>
                <p class="video-meta"><?= htmlspecialchars($plan['intensity']) ?> Intensity • <?= htmlspecialchars($plan['fitness_level']) ?></p>
            </div>

            <div class="right-icons">  
                <?php
                    $seconds = $gif['time'];
                    $minutes = floor($seconds / 60);
                    $remainingSeconds = $seconds % 60;
                    if ($minutes > 0) {
                        if ($remainingSeconds > 9) {
                            $readable = $minutes . ":" . $remainingSeconds;
                        } else {
                            $readable = $minutes . ":0" . $remainingSeconds;
                        }
                    } else {
                        if ($remainingSeconds > 9) {
                            $readable = "0:" . $remainingSeconds;
                        } else {
                            $readable = "0:0" . $remainingSeconds;
                        }
                    }
                ?>              
                <span class="duration"><?= $readable ?></span>
                <i class="fa-regular fa-circle-question info-icon"></i>
                <i class="fa-regular fa-heart heart-icon"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="workoutPlayer" class="player-overlay">
    <div class="player-container">

        <div class="player-top">
            <button class="player-back" onclick="closeWorkoutPlayer()">←</button>
            <h2 class="player-title"><?php echo $plan['plan_name']; ?></h2>
            <span></span>
        </div>

        <div class="gif-card">
            <img id="playerGif" src="" style="width:350px; object-fit:contain;">
        </div>

        <div class="rep-set-box">
            <span id="repCount">Rep <?php echo $plan['repetition']; ?></span>
            <span id="setCount">Sets <?php echo $plan['sets']; ?></span>
        </div>

        <div class="time-details">
            <div class="timer-wrapper">
                <svg class="progress-ring" width="220" height="220">
                    <circle class="progress-ring__circle"
                            stroke="#B388FF"
                            stroke-width="10"
                            fill="transparent"
                            r="100"
                            cx="110"
                            cy="110"/>
                </svg>

                <div class="timer-number" id="timerNumber">00</div>
            </div>

            <div class="player-controls">
                <button id="pauseBtn" class="control-btn">Pause</button>
                <button class="control-btn stop-btn" onclick="closeWorkoutPlayer()">Stop</button>
            </div>
        </div>
    </div>
</div>

<script src="../scripts/favorites.js"></script>
<script>
let exercises = <?php echo json_encode($video_list); ?>;
let current = 0;
let countdown = null;
let paused = false;

const dialog = document.getElementById("workoutPlayer");
const gifImg = document.getElementById("playerGif");
const timerNum = document.getElementById("timerNumber");
const repText = document.getElementById("repCount");
const setText = document.getElementById("setCount");

const circle = document.querySelector(".progress-ring__circle");
const radius = circle.r.baseVal.value;
const circumference = 2 * Math.PI * radius;
circle.style.strokeDasharray = `${circumference} ${circumference}`;

/* OPEN PLAYER */
function openWorkoutDialog() {
    dialog.style.display = "flex";
    clearInterval(countdown); 
    startExercise(0);
}

/* CLOSE PLAYER */
function closeWorkoutPlayer() {
    dialog.style.display = "none";
    clearInterval(countdown);
}

/* START SINGLE EXERCISE */
function startExercise(index) {
    clearInterval(countdown);
    current = index;
    paused = false;

    let ex = exercises[index];
    gifImg.src = "../assets/gifs/" + ex.file;

    let totalTime = ex.time;
    let timeLeft = totalTime;
    timerNum.textContent = timeLeft;

    updateCircle(timeLeft, totalTime);

    countdown = setInterval(() => {
        if (!paused) {
            timerNum.textContent = timeLeft;
            updateCircle(timeLeft, totalTime);

            if (timeLeft < 0) {
                clearInterval(countdown);
                if (current === exercises.length - 1) {
                    timerNum.textContent = "Done!";
                    gifImg.src = "../assets/images/workout-completed.avif";
                    return;
                }
                showNextMessage();
                return;
            }
            timeLeft--;
        }
    }, 1000);
}

/* SHOW NEXT EXERCISE */
function showNextMessage() {
    if (current === exercises.length - 1) {
        timerNum.textContent = "Done!";
        gifImg.src = "../assets/images/workout-completed.avif";
        return;
    }

    gifImg.src = "../assets/images/next-exercise-msg.png";
    let waitTime = 10;
    timerNum.textContent = waitTime;

    updateCircle(waitTime, waitTime);

    const waitCountdown = setInterval(() => {
        timerNum.textContent = waitTime;
        updateCircle(waitTime, 10);

        if (waitTime < 0) {
            clearInterval(waitCountdown);
            gifImg.style.display = "block";
            goNext();
        }
        waitTime--;
    }, 1000);
}

function updateCircle(timeLeft, totalTime) {
    const percent = timeLeft / totalTime;
    const offset = circumference - percent * circumference;
    circle.style.strokeDashoffset = offset;
}

/* MOVE TO NEXT GIF */
function goNext() {
    if (current + 1 < exercises.length) {
        startExercise(current + 1);
    } else {
        timerNum.textContent = "Done";
        gifImg.src = "../assets/images/workout-completed.avif";
    }
}

/* PAUSE BUTTON */
document.getElementById("pauseBtn").onclick = function () {
    paused = !paused;
    this.textContent = paused ? "Resume" : "Pause";
};
</script>

</body>
</html>
