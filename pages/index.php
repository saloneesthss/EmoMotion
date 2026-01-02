<?php
session_start();
require_once "../connection.php";

if (!empty($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}

$sql = "SELECT * FROM workout_videos ORDER BY id DESC";
$stmt = $con->prepare($sql);
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$plan_stmt = $con->prepare("SELECT * FROM workout_plans ORDER BY id DESC");
$plan_stmt->execute();
$plans = $plan_stmt->fetchAll(PDO::FETCH_ASSOC);
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
</head>
<body>
    <div class="container">
        <div class='main' id='main'>
            <div class='main-text'>
                <p>Move with your Mood.</p>
                <div class='txt1'>
                    Your digital coach for physical and emotional wellness.
                </div>
                <div class='txt2'>
                    <span>Start your fitness journey now</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
                <div class='buttons'>
                    <button class='signup' onclick="window.location.href='../signup.php'">Sign Up</button>
                    <button class='login' onclick="window.location.href='../login.php'">Log In</button>
                </div>
            </div>
                
            <div class='main-image'>
                <div></div>
                <img src="../assets/images/main.png" />
            </div>
        </div>

        <div class="workout-videos">
            <div class="title-bar">
                <span>Workout Videos</span>
                <button id="view-all-videos">View All Videos</button>
            </div>
            
            <div class="exercise-grid">
                <?php foreach ($videos as $video): ?>
                <div class="exercise-card">

                    <div class="exercise-image-container">
                        <img class="exercise-image" src="../assets/gifs/<?php echo $video['file_path']; ?>" alt="Workout Video">
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
                        Duration: <?php echo $video['duration']; ?> seconds
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="workout-plans">
            <div class="title-bar">
                <span>Workout Plans</span>
                <button id="view-all-plans">View All Plans</button>
            </div>
            <div class="exercise-grid">
                <?php foreach ($plans as $plan): ?>
                    <div class="exercise-card">

                        <div class="exercise-image-container">
                            <img class="exercise-image" src="../assets/plans-thumbnail/<?php echo $plan['file_path']; ?>" alt="Workout Plan">
                        </div>

                        <div class="exercise-name">
                            <?php echo htmlspecialchars($plan['plan_name']); ?>
                        </div>

                        <div class="exercise-price">
                            Target Area: <?php echo htmlspecialchars($plan['target_area']); ?>
                        </div>

                        <div class="exercise-target">
                            Mood: <?php echo htmlspecialchars($plan['mood']); ?>
                        </div>

                        <div class="exercise-equipment">
                            Fitness Level: <?php echo htmlspecialchars($plan['fitness_level']); ?>
                        </div>

                        <div class="exercise-equipment">
                            Intensity: <?php echo htmlspecialchars($plan['intensity']); ?>
                        </div>

                        <div class="exercise-equipment">
                            Duration: <?php echo $plan['duration']; ?> seconds
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script type="module" src="../scripts/index.js"></script>
</body>
</html>