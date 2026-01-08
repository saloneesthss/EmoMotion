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
                <div class="exercise-card" data-id="<?php echo $video['id']; ?>" data-name="<?php echo $video['title'];?>">
                    <div class="exercise-image-container">
                        <img class="exercise-image" src="../assets/gifs/<?php echo $video['file_path']; ?>" alt="Workout Video">
                    </div>

                    <a><div class="small-card">
                        <?php $isLoggedIn = isset($_SESSION['user_id']) ? '1' : '0'; ?>
                        <i class="fa fa-heart" 
                            id="fav-<?php echo $video['id'];?>" 
                            data-loggedin="<?php echo $isLoggedIn; ?>"
                            onclick="handleFavoriteClick(<?php echo $video['id'];?>, 0, this)"></i>
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
    
    <script src="../scripts/favorites.js"></script>
</body>
</html>