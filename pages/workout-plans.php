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

$sql = "SELECT * FROM workout_plans $whereClause ORDER BY id DESC";
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
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/workout-videos.css">
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
        function applyFilter(value) {
            window.location.href = "?filter=" + encodeURIComponent(value);
        }

        document.querySelector(".collection-button").addEventListener("click", function() {
            document.querySelector(".collection-menu").classList.toggle("show-menu");
        });
    </script>
</body>
</html>