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
$bindValues = [];

if (isset($_GET['filter'])) {
    $singleFilter = $_GET['filter'];
    $whereParts[] = "(target_area = :f OR mood = :f OR intensity = :f OR fitness_level = :f)";
    $bindValues[':f'] = $singleFilter;
}
elseif (isset($_GET['filters'])) {
    $filters = json_decode($_GET['filters'], true);

    if (!empty($filters['focus'])) {
        $in = implode(',', array_fill(0, count($filters['focus']), '?'));
        $whereParts[] = "target_area IN ($in)";
        $bindValues = array_merge($bindValues, $filters['focus']);
    }

    if (!empty($filters['mood'])) {
        $in = implode(',', array_fill(0, count($filters['mood']), '?'));
        $whereParts[] = "mood IN ($in)";
        $bindValues = array_merge($bindValues, $filters['mood']);
    }

    if (!empty($filters['intensity'])) {
        $in = implode(',', array_fill(0, count($filters['intensity']), '?'));
        $whereParts[] = "intensity IN ($in)";
        $bindValues = array_merge($bindValues, $filters['intensity']);
    }

    if (!empty($filters['fitness'])) {
        $in = implode(',', array_fill(0, count($filters['fitness']), '?'));
        $whereParts[] = "fitness_level IN ($in)";
        $bindValues = array_merge($bindValues, $filters['fitness']);
    }

    if (!empty($filters['reps'])) {
        $durationConditions = [];
        foreach ($filters['reps'] as $range) {
            if ($range === "30+") {
                $durationConditions[] = "duration >= 30";
            } else {
                list($min, $max) = explode("-", $range);
                $durationConditions[] = "(duration BETWEEN $min AND $max)";
            }
        }
        $whereParts[] = "(" . implode(" OR ", $durationConditions) . ")";
    }
}

$whereSQL = "";
if (!empty($whereParts)) {
    $whereSQL = "WHERE " . implode(" AND ", $whereParts);
}

$sql = "SELECT * FROM workout_plans $whereSQL ORDER BY id DESC";
$stmt = $con->prepare($sql);
$stmt->execute($bindValues);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/navbar.css">
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
                            <h4>DURATION</h4>
                            <div class="filter-item" data-rep="1-10">1-10 Days <span class="check-icon">✔</span></div>
                            <div class="filter-item" data-rep="10-20">10-20 Days <span class="check-icon">✔</span></div>
                            <div class="filter-item" data-rep="20-30">20-30 Days <span class="check-icon">✔</span></div>
                            <div class="filter-item" data-rep="30+">30+ Days <span class="check-icon">✔</span></div>
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
                <a href="plan-details.php?id=<?= $video['id'] ?>" class="plan-link">
                    <div class="exercise-card plan-card">
                        <div class="exercise-image-container">
                            <img class="exercise-image" src="../assets/plans-thumbnail/<?php echo $video['file_path']; ?>" alt="Workout Plan">
                        </div>

                        <div class="exercise-name plan-title">
                            <?php echo htmlspecialchars($video['plan_name']); ?>
                        </div>

                        <div class="plan-details">
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
                                Duration: <?php echo $video['time_duration']; ?> seconds
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="../scripts/favorites.js"></script>
    <script>
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

            overlayVisible = false;
            filterOverlay.style.display = "none";

            const query = encodeURIComponent(JSON.stringify(selected));
            window.location.href = "?filters=" + query;
        });

        clearBtn.addEventListener("click", () => {
            document.querySelectorAll(".filter-item.selected").forEach(i => i.classList.remove("selected"));
        });
    </script>
</body>
</html>