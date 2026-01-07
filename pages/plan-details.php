<?php
session_start();
require_once "../connection.php";

if (isset($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}

$plan_id = (int) $_GET['id']; 
$sql = "SELECT p.*, v.title as video_title, v.file_path as video_file, v.duration as time
FROM workout_plans p
JOIN workout_videos v
  ON JSON_CONTAINS(p.video_list, JSON_QUOTE(v.file_path)) WHERE p.id=$plan_id";
$stmt = $con->prepare($sql);
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$plans) {
    echo "Plan not found!";
    exit;
}

$plan = $plans[0];
$video_list = [];
foreach($plans as $row){
    if(!empty($row['video_file'])){
        $video_list[] = [
            'title' => $row['video_title'],
            'file' => $row['video_file'],
            'time' => $row['time'],
        ];
    }
// $video_list = json_decode($plans['video_list'], true);
}
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
    <div class="left-sidebar">
        <?php if (!empty($plan['file_path']) && file_exists('../assets/plans-thumbnail/' . $plan['file_path'])) { ?>
            <img class="plan-thumb" src="../assets/plans-thumbnail/<?php echo $plan['file_path']; ?>" alt="">
        <?php } ?>
        
        <h2 class="plan-title"><?php echo $plan['plan_name']; ?></h2>

        <a><div class="small-card">
            <?php $isLoggedIn = isset($_SESSION['user_id']) ? '1' : '0'; ?>
            <i class="fa fa-heart" 
                id="fav-<?php echo $video['id'];?>" 
                data-loggedin="<?php echo $isLoggedIn; ?>"
                onclick="handleFavoriteClick(<?php echo $video['id']; ?>, this)"></i>
        </div></a>

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
            <span><i class="fa-solid fa-stopwatch"></i> <?= $readable ?>/Day</span>
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
        </p>
    </div>

    <div class="right-content">
        <h1 class="main-title"><?php echo $plan['plan_name']; ?></h1>

        <?php foreach ($video_list as $gif): ?>
        <div class="workout-card">
            <img 
                src="../assets/gifs/<?php echo htmlspecialchars($gif['file']); ?>" 
                class="video-thumb"
                alt="Video Thumbnail">

            <div class="video-info">
                <h3><?= htmlspecialchars($gif['title']) ?></h3>
                <span class="video-tag">
                    <i class="fa-regular fa-compass"></i> 
                    <?= htmlspecialchars($plan['target_area']) ?>
                </span>
                <p class="video-meta"><?= htmlspecialchars($plan['intensity']) ?> Intensity • <?= htmlspecialchars($plan['fitness_level']) ?></p>
            </div>

            <div class="right-icons">  
                <?php
                    $seconds = $gif['time'];
                    $minutes = floor($seconds / 60);
                    $remainingSeconds = $seconds % 60;
                    if ($minutes > 0) {
                        if ($remainingSeconds > 9) {
                            $readable = $minutes . ":" . $remainingSeconds;
                        } else {
                            $readable = $minutes . ":0" . $remainingSeconds;
                        }
                    } else {
                        if ($remainingSeconds > 9) {
                            $readable = "0:" . $remainingSeconds;
                        } else {
                            $readable = "0:0" . $remainingSeconds;
                        }
                    }
                ?>              
                <span class="duration"><?= $readable ?></span>
                <i class="fa-regular fa-circle-question info-icon"></i>
                <i class="fa-regular fa-heart heart-icon"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="../scripts/favorites.js"></script>
</body>
</html>
