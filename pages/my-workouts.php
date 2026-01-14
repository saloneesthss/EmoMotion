<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$user_id = $_SESSION['user_id'];
$userstmt = $con->prepare("SELECT * FROM users WHERE id = $user_id");
$userstmt->execute();
$user = $userstmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT workout_videos.* 
    FROM favorites 
    JOIN workout_videos ON favorites.video_id = workout_videos.id
    WHERE favorites.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$user_id]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/settings.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/workout-videos.css">
    <link rel="stylesheet" href="../styles/user-profile.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h2>EmoMotion</h2>

            <a href="user-profile.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" onclick="return confirm('Are you sure to logout?')" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="videos-container exercise-grid" id="myFavoriteVideos">
        <?php foreach ($videos as $video): ?>

            <?php
                $isLoggedIn = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

                $favorited = false;
                if ($isLoggedIn) {
                    $stmt = $con->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND video_id = :video_id");
                    $stmt->execute([
                        ':user_id' => $isLoggedIn,
                        ':video_id' => $video['id']
                    ]);
                    $favorited = $stmt->fetchColumn() > 0;
                }

                $heartClass = $favorited ? 'fa-heart favorited' : 'fa-heart';
            ?>

            <div class="exercise-card" data-id="<?php echo $video['id']; ?>" data-name="<?php echo $video['title']; ?>">

                <div class="exercise-image-container">
                    <img class="exercise-image" src="../assets/gifs/<?php echo htmlspecialchars($video['file_path']); ?>" alt="Workout Video">
                </div>

                <a>
                    <div class="small-card">
                        <i class="fa <?php echo $heartClass; ?>" 
                        id="fav-<?php echo $video['id']; ?>" 
                        data-loggedin="<?php echo $isLoggedIn ? '1' : '0'; ?>"
                        onclick="handleFavoriteClick(<?php echo $video['id']; ?>, 0, this)"></i>
                    </div>
                </a>

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
    <script src="../scripts/favorites.js"></script>
</body>
</html>