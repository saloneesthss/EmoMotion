<?php
session_start();
require_once "../connection.php";

if (isset($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}

$filters = [];
$whereParts = [];

if (isset($_GET['filter'])) {
    $singleFilter = $_GET['filter'];
    $whereParts[] = "target_area = ? OR mood = ? OR intensity = ? OR fitness_level = ?";
    $bindValues = [$singleFilter, $singleFilter, $singleFilter, $singleFilter];
}

elseif (isset($_GET['filters'])) {
    $filters = json_decode($_GET['filters'], true);

    $bindValues = [];
    if (!empty($filters['focus'])) {
        $in = str_repeat('?,', count($filters['focus']) - 1) . '?';
        $whereParts[] = "target_area IN ($in)";
        $bindValues = array_merge($bindValues, $filters['focus']);
    }

    if (!empty($filters['mood'])) {
        $in = str_repeat('?,', count($filters['mood']) - 1) . '?';
        $whereParts[] = "mood IN ($in)";
        $bindValues = array_merge($bindValues, $filters['mood']);
    }

    if (!empty($filters['intensity'])) {
        $in = str_repeat('?,', count($filters['intensity']) - 1) . '?';
        $whereParts[] = "intensity IN ($in)";
        $bindValues = array_merge($bindValues, $filters['intensity']);
    }

    if (!empty($filters['fitness'])) {
        $in = str_repeat('?,', count($filters['fitness']) - 1) . '?';
        $whereParts[] = "fitness_level IN ($in)";
        $bindValues = array_merge($bindValues, $filters['fitness']);
    }

    if (!empty($filters['reps'])) {
        $repConditions = [];
        foreach ($filters['reps'] as $repRange) {
            if ($repRange === "30+") {
                $repConditions[] = "repetition >= 30";
            } else {
                list($minRep, $maxRep) = explode("-", $repRange);
                $repConditions[] = "(repetition BETWEEN $minRep AND $maxRep)";
            }
        }
        $whereParts[] = "(" . implode(" OR ", $repConditions) . ")";
    }
}

$whereSQL = "";
if (!empty($whereParts)) {
    $whereSQL = "WHERE " . implode(" AND ", $whereParts);
}

$sql = "SELECT * FROM workout_videos $whereSQL ORDER BY id DESC";
$stmt = $con->prepare($sql);
$stmt->execute($bindValues ?? []);
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
                        <li onclick="applyFilter('Abs')">Abs</li>
                        <li onclick="applyFilter('Waist')">Waist</li>
                        <li onclick="applyFilter('Hips')">Hips</li>
                        <li onclick="applyFilter('Legs')">Legs</li>
                        <li onclick="applyFilter('Arms')">Arms</li>
                        <li onclick="applyFilter('Back')">Back</li>
                        <li onclick="applyFilter('Full Body')">Full Body</li>
                    </ul>
                </li>
                <li class="has-submenu" data-filter="">Browse By Current Mood ▸
                    <ul class="sub-menu">
                        <li onclick="applyFilter('Happy')">Happy</li>
                        <li onclick="applyFilter('Sad')">Sad</li>
                        <li onclick="applyFilter('Angry')">Angry</li>
                        <li onclick="applyFilter('Tired')">Tired</li>
                        <li onclick="applyFilter('Energized')">Energized</li>
                    </ul>
                </li>
                <li class="has-submenu" data-filter="">Browse By Intensity ▸
                    <ul class="sub-menu">
                        <li onclick="applyFilter('Low')">Low</li>
                        <li onclick="applyFilter('Medium')">Medium</li>
                        <li onclick="applyFilter('High')">High</li>
                    </ul>
                </li>
                <li class="has-submenu" data-filter="">Browse By Fitness Level ▸
                    <ul class="sub-menu">
                        <li onclick="applyFilter('Beginner')">Beginner</li>
                        <li onclick="applyFilter('Intermediate')">Intermediate</li>
                        <li onclick="applyFilter('Advanced')">Advanced</li>
                    </ul>
                </li>
            </ul>
            
            <button class="filter-btn"><img src="../assets/icons/filter-icon.png">Filter</button>
            <div id="filter-overlay" class="filter-overlay">
                <div class="filter-dialog">
                    <div class="filter-columns">
                        <div class="filter-column" data-filter="Focus Area">
                            <h4>FOCUS AREA</h4>
                            <div class="filter-item">Abs <span class="check-icon">✔</span></div>
                            <div class="filter-item">Arms <span class="check-icon">✔</span></div>
                            <div class="filter-item">Back <span class="check-icon">✔</span></div>
                            <div class="filter-item">Waist <span class="check-icon">✔</span></div>
                            <div class="filter-item">Hips <span class="check-icon">✔</span></div>
                            <div class="filter-item">Legs <span class="check-icon">✔</span></div>
                            <div class="filter-item">Full Body <span class="check-icon">✔</span></div>
                        </div>

                        <div class="filter-column" data-filter="Current Mood">
                            <h4>CURRENT MOOD</h4>
                            <div class="filter-item">Happy <span class="check-icon">✔</span></div>
                            <div class="filter-item">Sad <span class="check-icon">✔</span></div>
                            <div class="filter-item">Angry <span class="check-icon">✔</span></div>
                            <div class="filter-item">Tired <span class="check-icon">✔</span></div>
                            <div class="filter-item">Energized <span class="check-icon">✔</span></div>
                        </div>

                        <div class="filter-column" data-filter="Intensity">
                            <h4>INTENSITY</h4>
                            <div class="filter-item">Low <span class="check-icon">✔</span></div>
                            <div class="filter-item">Medium <span class="check-icon">✔</span></div>
                            <div class="filter-item">High <span class="check-icon">✔</span></div>
                        </div>

                        <div class="filter-column" data-filter="Repetitions">
                            <h4>REPETITIONS</h4>
                            <div class="filter-item" data-rep="1-10">1-10 Reps <span class="check-icon">✔</span></div>
                            <div class="filter-item" data-rep="10-20">10-20 Reps <span class="check-icon">✔</span></div>
                            <div class="filter-item" data-rep="20-30">20-30 Reps <span class="check-icon">✔</span></div>
                            <div class="filter-item" data-rep="30+">30+ Reps <span class="check-icon">✔</span></div>
                        </div>

                        <div class="filter-column" data-filter="Fitness Level" style="border-right:none;">
                            <h4>FITNESS LEVEL</h4>
                            <div class="filter-item">Beginner <span class="check-icon">✔</span></div>
                            <div class="filter-item">Intermediate <span class="check-icon">✔</span></div>
                            <div class="filter-item">Advanced <span class="check-icon">✔</span></div>
                        </div>
                    </div>

                    <div class="filter-footer">
                        <button id="clear-filters">Clear Filters</button>
                        <button id="apply-filters">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="exercise-grid">
            <?php foreach ($videos as $video): ?>
                <div class="exercise-card" onclick="trackVideo(<?= $video['id']?>)"
                    data-id="<?= $video['id']; ?>"
                    data-name="<?= htmlspecialchars($video['title']); ?>"
                    data-duration="<?= $video['duration']; ?>"
                    data-rep="<?= $video['repetition']; ?>"
                    data-sets="<?= $video['sets']; ?>">
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

        // Collection button
        function applyFilter(value) {
            window.location.href = "?filter=" + encodeURIComponent(value);
        }

        const collectionButton = document.querySelector(".collection-button");
        const collectionMenu = document.querySelector(".collection-menu");
        collectionButton.addEventListener("click", function(e) {
            e.stopPropagation(); 
            collectionMenu.classList.toggle("show-menu");
        });
        document.addEventListener("click", function(e) {
            if (!collectionButton.contains(e.target) && !collectionMenu.contains(e.target)) {
                collectionMenu.classList.remove("show-menu");
            }
        });

        // Filter button
        const filterBtn = document.querySelector(".filter-btn");
        const filterOverlay = document.getElementById("filter-overlay");
        const applyBtn = document.getElementById("apply-filters");
        const clearBtn = document.getElementById("clear-filters");
        let overlayVisible = false;
        filterBtn.addEventListener("click", () => {
            overlayVisible = !overlayVisible;
            filterOverlay.style.display = overlayVisible ? "block" : "none";
        });

        filterOverlay.addEventListener("click", (e) => {
            if (e.target === filterOverlay) {
                overlayVisible = false;
                filterOverlay.style.display = "none";
            }
        });

        document.querySelectorAll(".filter-item").forEach(item => {
            item.addEventListener("click", () => {
                item.classList.toggle("selected");
            });
        });

        function trackVideo(videoId) {
            fetch("../pages/track-video.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({video_id: videoId})
            })
            .then(res => res.json())
            .then(data => console.log(data))
            .catch(err => console.error(err));
        }

        applyBtn.addEventListener("click", () => {
            const selected = {focus: [], mood: [], intensity: [], reps: [], fitness: []};
            document.querySelectorAll(".filter-column").forEach(col => {
                const type = col.getAttribute("data-filter");
                let values;
                if (type === "Repetitions") {
                    values = Array.from(col.querySelectorAll(".filter-item.selected"))
                        .map(i => i.getAttribute("data-rep"));
                } else {
                    values = Array.from(col.querySelectorAll(".filter-item.selected"))
                        .map(i => i.childNodes[0].nodeValue.trim());
                }
                if (type === "Focus Area") selected.focus = values;
                if (type === "Current Mood") selected.mood = values;
                if (type === "Intensity") selected.intensity = values;
                if (type === "Repetitions") selected.reps = values;
                if (type === "Fitness Level") selected.fitness = values;
            });
            console.log("Filters applied:", selected);
            overlayVisible = false;
            filterOverlay.style.display = "none";
            const query = encodeURIComponent(JSON.stringify(selected));
            window.location.href = "?filters=" + query;
        });

        clearBtn.addEventListener("click", () => {
            document.querySelectorAll(".filter-item.selected").forEach(i => i.classList.remove("selected"));
        });

        // Exercise dialog box
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