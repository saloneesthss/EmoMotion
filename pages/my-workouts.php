<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM workout_videos";
$stmt = $con->prepare($sql);
$stmt->execute();
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
    <link rel="stylesheet" href="../styles/user-profile.css">
    <link rel="stylesheet" href="../styles/navbar.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h2>EmoMotion</h2>

            <a href="user-profile.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="videos-container" id="myFavoriteVideos">

            <?php foreach ($videos as $video): ?>
                <div class="exercise-card"
                    data-video-id="<?php echo $video['id']; ?>">

                    <img src="<?php echo $video['file_path']; ?>" class="exercise-image">

                    <div class="exercise-name"><?php echo $video['title']; ?></div>
                    <div class="exercise-bodypart">Target Area: <?php echo $video['target_area']; ?></div>

                    <i class="favorite-icon" fa fa-heart
                        data-type="video"
                        data-id="<?php echo $video['id']; ?>"
                        onclick="toggleFavorite(<?php echo $video['id']; ?>)">
                    </i>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
       document.addEventListener("DOMContentLoaded", function () {
            let favContainer = document.getElementById("myFavoriteVideos");
            let favorites = JSON.parse(localStorage.getItem("favoriteExercises")) || {};

            if (Object.keys(favorites).length === 0) {
                favContainer.innerHTML = "<p>No favorite workouts yet.</p>";
                return;
            }

            let html = "";

            Object.values(favorites).forEach(ex => {
                html += `
                <div class="exercise-card">
                    <img src="${ex.img}">
                    <h3>${ex.name}</h3>
                    <video src="${ex.video}" controls></video>
                </div>
                `;
            });

            favContainer.innerHTML = html;
        });
    </script>
</body>
</html>