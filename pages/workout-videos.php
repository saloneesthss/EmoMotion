<?php
session_start();
require_once "../connection.php";

if (isset($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}

$filter = "";
$whereClause = "";

if (isset($_GET['filter']) && !empty($_GET['filter'])) {
    $filter = $_GET['filter'];
    $whereClause = " WHERE target_area = :filter OR mood = :filter OR fitness_level = :filter OR intensity = :filter ";
}

$sql = "SELECT * FROM workout_videos $whereClause ORDER BY id DESC";
$stmt = $con->prepare($sql);

if (!empty($filter)) {
    $stmt->bindParam(':filter', $filter);
}

$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/plan-details.css">
    <link rel="stylesheet" href="../styles/workout-videos.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class='container'>
        <div class="collection-filter">
            <button class="collection-button">Browse By Collection ▾</button>
            <ul class="collection-menu">
                <li class="has-submenu" data-filter="">Browse By Target Area ▸
                    <ul class="sub-menu">
                        <li>Abs</li>
                        <li>Waist</li>
                        <li>Hips</li>
                        <li>Legs</li>
                        <li>Arms</li>
                        <li>Back</li>
                        <li>Full Body</li>
                    </ul>
                </li>
                <li class="has-submenu" data-filter="">Browse By Current Mood ▸
                    <ul class="sub-menu">
                        <li>Happy</li>
                        <li>Sad</li>
                        <li>Angry</li>
                        <li>Tired</li>
                        <li>Energized</li>
                    </ul>
                </li>
                <li class="has-submenu" data-filter="">Browse By Intensity ▸
                    <ul class="sub-menu">
                        <li>Low</li>
                        <li>Medium</li>
                        <li>High</li>
                    </ul>
                </li>
                <li class="has-submenu" data-filter="">Browse By Fitness Level ▸
                    <ul class="sub-menu">
                        <li>Beginner</li>
                        <li>Intermediate</li>
                        <li>Advanced</li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="exercise-grid">
            <?php foreach ($videos as $video): ?>
                <div class="exercise-card"
                    data-id="<?= $video['id']; ?>"
                    data-name="<?= htmlspecialchars($video['title']); ?>"
                    data-duration="<?= $video['duration']; ?>"
                    data-rep="<?= $video['repetition']; ?>"
                    data-sets="<?= $video['sets']; ?>"
                >
                    <div class="exercise-image-container">
                        <img class="exercise-image" src="../assets/gifs/<?php echo $video['file_path']; ?>" alt="Workout Video">
                    </div>

                    <a><div class="small-card">
                        <?php 
                        $isLoggedIn = isset($_SESSION['user_id']) ? '1' : '0'; 
                        $isFavorited = false;
                        if($isLoggedIn === '1') {
                            $check = $con->prepare("SELECT 1 from favorites WHERE user_id = :uid AND video_id = :vid");
                            $check->execute([
                                ':uid' => $_SESSION['user_id'],
                                ':vid' => $video['id']
                            ]);
                            $isFavorited = $check->fetch() ? true : false;
                        }
                        ?>
                        <i class="fa fa-heart 
                            <?= $isFavorited ? 'favorited' : '' ?>"
                            id="fav-<?= $video['id'];?>" 
                            data-loggedin="<?= $isLoggedIn; ?>"
                            onclick="handleFavoriteClick(<?= $video['id'];?>, 0, this)"></i>
                    </div></a>

                    <div class="exercise-name">
                        <?php echo htmlspecialchars($video['title']); ?>
                    </div>

                    <div class="exercise-price">
                        Target Area: <?php echo htmlspecialchars($video['target_area']); ?>
                    </div>

                    <div class="exercise-target">
                        Mood: <?php echo htmlspecialchars($video['mood']); ?>
                    </div>

                    <div class="exercise-equipment">
                        Fitness Level: <?php echo htmlspecialchars($video['fitness_level']); ?>
                    </div>

                    <div class="exercise-equipment">
                        Intensity: <?php echo htmlspecialchars($video['intensity']); ?>
                    </div>

                    <div class="exercise-equipment">
                        Duration: <?php echo $video['duration']; ?> seconds
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="workoutPlayer" class="player-overlay">
        <div class="player-container">

            <div class="player-top">
                <button class="player-back" onclick="closeWorkoutPlayer()">←</button>
                <h2 class="player-title" id="playerTitle"></h2>
                <span></span>
            </div>

            <div class="gif-card">
                <img id="playerGif" src="">
            </div>

            <div class="rep-set-box">
                <span id="repCount"></span>
                <span id="setCount"></span>
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
        const dialog = document.getElementById("workoutPlayer");
        const gifImg = document.getElementById("playerGif");
        const timerNum = document.getElementById("timerNumber");
        const repText = document.getElementById("repCount");
        const setText = document.getElementById("setCount");
        const titleText = document.getElementById("playerTitle");

        const circle = document.querySelector(".progress-ring__circle");
        const radius = circle.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;
        circle.style.strokeDasharray = `${circumference} ${circumference}`;

        let countdown = null;
        let paused = false;

        document.querySelectorAll(".exercise-card").forEach(card => {
            card.addEventListener("click", function(e) {
                if (e.target.classList.contains("fa-heart")) return;

                const filePath = this.querySelector(".exercise-image").src;
                const duration = parseInt(this.dataset.duration);
                const rep = this.dataset.rep;
                const sets = this.dataset.sets;

                exercises = [
                    { file: filePath.split("/").pop(), time: duration, repetition: rep, sets: sets }
                ];

                document.querySelector(".player-title").textContent = this.dataset.name;
                document.getElementById("repCount").textContent = `Reps: ${rep} `;
                document.getElementById("setCount").textContent = `Sets: ${sets}`;
                document.getElementById("playerGif").src = filePath;

                totalTime = duration;
                timeLeft = duration;
                updateCircle();
                openWorkoutDialog({
                    title: this.dataset.name,
                    file: filePath,
                    time: duration
                });
            });
        });

        /* OPEN PLAYER */
        function openWorkoutDialog(ex) {
            dialog.style.display = "flex";
            titleText.textContent = ex.title;
            gifImg.src = ex.file;

            startExercise(ex.time);
        }

        /* CLOSE PLAYER */
        function closeWorkoutPlayer() {
            dialog.style.display = "none";
            clearInterval(countdown);
        }

        /* START TIMER */
        function startExercise(totalTime) {
            clearInterval(countdown);
            paused = false;

            let timeLeft = totalTime;
            timerNum.textContent = timeLeft;

            updateCircle(timeLeft, totalTime);

            countdown = setInterval(() => {
                if (!paused) {
                    timeLeft--;
                    timerNum.textContent = timeLeft;
                    updateCircle(timeLeft, totalTime);

                    if (timeLeft < 0) {
                        clearInterval(countdown);
                        timerNum.textContent = "Done!";
                        gifImg.src = "../assets/images/workout-completed.avif";
                        return;
                    }
                }
            }, 1000);
        }

        function updateCircle(timeLeft, totalTime) {
            const percent = timeLeft / totalTime;
            const offset = circumference - percent * circumference;
            circle.style.strokeDashoffset = offset;
        }

        document.getElementById("pauseBtn").onclick = function () {
            paused = !paused;
            this.textContent = paused ? "Resume" : "Pause";
        };
    </script>
</body>
</html>