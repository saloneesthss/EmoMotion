<?php
require_once "../connection.php";
require_once "../components/user-navbar.php";

$sql = "SELECT p.*, v.title as video_title, v.file_path as video_file, v.duration as time
FROM workout_plans p
JOIN workout_videos v
  ON JSON_CONTAINS(p.video_list, JSON_QUOTE(v.file_path))";
$stmt = $con->prepare($sql);
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/customize-plans.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    
    <!-- LEFT SIDEBAR -->
    <div class="sidebar">
        <h3>Total: 2 days</h3>
        <div class="menu-item active">Any</div>
        <div class="menu-item">Any</div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">
        <h2 class="title">Customize Your Own Plan</h2>

        <div class="input-block">
            <label>Plan name</label>
            <input type="text" placeholder="Enter the name of your plan">
        </div>

        <!-- More Settings Toggle -->
        <div class="more-settings-header" onclick="toggleSettings()">
            <span>More settings</span>
            <span id="arrow">▼</span>
        </div>

        <!-- More Settings Content -->
        <div class="more-settings-content" id="moreSettings">
            <div class="settings-section">
                <h4>Target Area</h4>
                <label><input type="radio" name="target" checked> Waist</label>
                <label><input type="radio" name="target"> Hips</label>
                <label><input type="radio" name="target"> Abs</label>
                <label><input type="radio" name="target"> Legs</label>
                <label><input type="radio" name="target"> Arms</label>
                <label><input type="radio" name="target"> Back</label>
            </div>

            <div class="settings-section">
                <h4>Mood</h4>
                <label><input type="radio" name="mood" checked> Happy</label>
                <label><input type="radio" name="mood"> Sad</label>
                <label><input type="radio" name="mood"> Angry</label>
                <label><input type="radio" name="mood"> Tired</label>
                <label><input type="radio" name="mood"> Energized</label>
            </div>

            <div class="settings-section">
                <h4>Intensity</h4>
                <label><input type="radio" name="intensity" checked> Low</label>
                <label><input type="radio" name="intensity"> Medium</label>
                <label><input type="radio" name="intensity"> High</label>
            </div>

            <div class="settings-section">
                <label><input type="radio" name="level" checked> Beginner</label>
                <label><input type="radio" name="level"> Intermediate</label>
                <label><input type="radio" name="level"> Advanced</label>
            </div>

            <textarea class="description" placeholder="Enter your plan details here..."></textarea>
        </div>

        <!-- <hr> -->

        <!-- Routine Detail -->
        <div class="input-block">
            <label>Plan detail</label>
        </div>

        <div class="routine-box">
            <div class="routine-header">
                <span>Any ▾</span>
                <span>Week 1 - 3 ✎</span>
                <span class="estimate">Est. 32 min • 5 exercises</span>
            </div>

            <div class="exercise-card">
                <img src="https://images.jefit.com/images/exercises/weighted-crunch.jpg">
                <div class="exercise-info">
                    <h3>Weighted Crunch</h3>

                    <div class="set-row">
                        <div>1</div>
                        <input value="10 lbs">
                        <input value="30 reps">
                        <input value="45 sec rest">
                    </div>

                    <div class="set-row">
                        <div>2</div>
                        <input value="10 lbs">
                        <input value="30 reps">
                        <input value="45 sec rest">
                    </div>

                    <div class="set-row">
                        <div>3</div>
                        <input value="10 lbs">
                        <input value="30 reps">
                        <input value="45 sec rest">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT EXERCISE LIBRARY -->
    <div class="exercise-library">
        <div class="library-header">
            <h3>Exercise library</h3>
            <input type="text" placeholder="Search exercise name">
        </div>

        <?php foreach ($video_list as $gif): ?>
        <div class="exercise-item">
            <img src="../assets/gifs/<?php echo htmlspecialchars($gif['file']); ?>" 
                class="video-thumb"
                alt="Video Thumbnail">
            <div>
                <h4><?= htmlspecialchars($gif['title']) ?></h4>
                <p><?= htmlspecialchars($gif['target_area']) ?></p>
            </div>
            <button>+</button>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<script src="../scripts/customize-plans.js"></script>
</body>
</html>
