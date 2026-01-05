<?php
session_start();
require_once "../connection.php";

if (isset($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}

$plan_id = (int) $_GET['id']; 
$sql = "select * from workout_plans where id = $plan_id";
$stmt = $con->prepare($sql);
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$plans) {
    echo "Plan not found!";
    exit;
}

$video_list = json_decode($plans['video_list'], true);
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
    <?php foreach($plans as $plan) { ?>
    <div class="left-sidebar">
        <?php if (!empty($plan['file_path']) && file_exists('../assets/plans-thumbnail/' . $plan['file_path'])) { ?>
            <img class="plan-thumb" src="../assets/plans-thumbnail/<?php echo $plan['file_path']; ?>" alt="">
        <?php } ?>
        
        <h2 class="plan-title"><?php echo $plan['plan_name']; ?></h2>

        <div class="small-card">
            <i class="fa fa-heart"></i>
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
            <span><i class="fa-solid fa-stopwatch"></i> <?= $readable ?> /Day</span>
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
            <!-- <a href="#" class="read-more">Read More</a> -->
        </p>
    </div>

    <div class="right-content">
        <h1 class="main-title"><?php echo $plan['plan_name']; ?></h1>

        <!-- <div class="days-nav">
            <button class="day active">Day 1</button>
            <button class="day">Day 2</button>
            <button class="day">Day 3</button>
            <button class="day rest">Rest Day</button>
            <button class="day">Day 5</button>
            <button class="day">Day 6</button>
            <button class="day">Day 7</button>
        </div>

        <h2 class="workout-heading">Day 1’s Workout</h2> -->

        <div class="workout-card">
            <img src="" class="video-thumb">

            <div class="video-info">
                <!-- <h3>Full Body</h3>
                <span class="video-tag"><i class="fa-regular fa-compass"></i> Full Body</span>
                <p class="video-meta">43K views • Jan 26</p> -->

                <?php if(!empty($video_list)): ?>
                    <h3>Workout Videos:</h3>
                    <ul>
                        <?php foreach($video_list as $video_path): ?>
                            <li><?= htmlspecialchars($video_path) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="right-icons">  
                <?php
                    $seconds = $plan['time_duration'];
                    $minutes = floor($seconds / 60);
                    $remainingSeconds = $seconds % 60;
                    if ($minutes > 0) {
                        if ($remainingSeconds > 9) {
                            $readable = $minutes . ":" . $remainingSeconds . " mins";
                        } else {
                            $readable = $minutes . ":0" . $remainingSeconds . " mins";
                        }
                    } else {
                        $readable = $remainingSeconds . " secs";
                    }
                ?>              
                <span class="duration"><?= $readable ?></span>
                <i class="fa-regular fa-circle-question info-icon"></i>
                <i class="fa-regular fa-heart heart-icon"></i>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</body>
</html>
