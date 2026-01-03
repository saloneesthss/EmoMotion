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
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/workout-videos.css">
</head>
<body>
    <div class='container'>
        <div class="collection-filter">
            <button class="collection-button">Browse By Collection ▾</button>
            <!-- <ul class="collection-menu">
                <li><a href="?filter=Abs">Abs</a></li>
                <li><a href="?filter=Waist">Waist</a></li>
                <li><a href="?filter=Happy">Happy</a></li>
                <li><a href="?filter=Energized">Energized</a></li>
                <li><a href="?filter=Beginner">Beginner</a></li>
            </ul> -->
            <ul class="collection-menu">
                <li data-filter="abs">Abs</li>
                <li data-filter="arms">Arms</li>
                <li data-filter="legs">Legs</li>
                <li data-filter="back">Back</li>
                <li data-filter="chest">Chest</li>
                <li data-filter="shoulders">Shoulders</li>
                <li data-filter="fullbody">Full Body</li>
                <li data-filter="cardio">Cardio</li>
                <li data-filter="stretch">Stretch</li>
            </ul>
        </div>
        <div class="exercise-grid">
            <?php foreach ($videos as $video): ?>
                <div class="exercise-card">
                    <div class="exercise-image-container">
                        <img class="exercise-image" src="../assets/gifs/<?php echo $video['file_path']; ?>" alt="Workout Video">
                    </div>

                    <div class="small-card">
                        <i class="fa fa-heart"></i>
                    </div>

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
                        Duration: <?php echo $video['duration']; ?> mins
                    </div>

                    <!-- <button class="view-more-button"
                        data-video-id="<?php echo $video['id']; ?>">
                        View More
                    </button> -->

                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>