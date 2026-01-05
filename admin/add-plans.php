<?php
require_once "logincheck.php";

$success = "";
$error = "";
$upload_path = "../assets/plans-thumbnail";

// $videos = [];
$stmtvdo=$con->prepare("select * from workout_videos");
$stmtvdo->execute();
$videos[]=$stmtvdo->fetchAll(PDO::FETCH_ASSOC);

$selectedVideos = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $plan_name = $_POST['plan_name'];
    $target_area = $_POST['target_area'];
    $mood = $_POST['mood'];
    $intensity = $_POST['intensity'];
    $fitness_level = $_POST['fitness_level'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];

    $selectedVideos = $_POST['videos'] ?? [];

    $video_list = json_encode($selectedVideos);

    $totalDuration = 0;

    if (!empty($selectedVideos)) {
        $placeholders = rtrim(str_repeat('?,', count($selectedVideos)), ',');
        $stmtDur = $con->prepare("SELECT duration FROM workout_videos WHERE file_path IN ($placeholders)");
        $stmtDur->execute($selectedVideos);
        $videoDurations = $stmtDur->fetchAll(PDO::FETCH_COLUMN);

        // Add all durations
        foreach ($videoDurations as $dur) {
            $totalDuration += (int)$dur;
        }
    }

    $file_name = null;
    if (is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {
        $file_name = $_FILES['thumbnail']['name'];
        move_uploaded_file(
            $_FILES['thumbnail']['tmp_name'],
            $upload_path . '/' . $file_name
        );
    }

    $sql = "INSERT INTO workout_plans 
            (plan_name, file_path, target_area, mood, intensity, fitness_level, duration, time_duration, description, video_list)
            VALUES 
            (:plan_name, :file_path, :target_area, :mood, :intensity, :fitness_level, :duration, :time_duration, :description, :video_list)";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':plan_name' => $plan_name,
        ':file_path' => $file_name,
        ':target_area' => $target_area,
        ':mood' => $mood,
        ':intensity' => $intensity,
        ':fitness_level' => $fitness_level,
        ':duration' => $duration,
        ':time_duration' => $totalDuration,
        ':description' => $description,
        ':video_list' => $video_list
    ]);
    header("Location: add-plans.php?success=Plan added successfully.");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
    <link rel="stylesheet" href="../styles/admin/add-plans.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>

        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="user-details.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add-videos.php"><i class="fa-solid fa-video"></i> Workout Videos</a>
        <a href="add-plans.php"><i class="fa-solid fa-dumbbell"></i> Workout Plans</a>
        <a href="community-posts.php"><i class="fa-solid fa-comment-dots"></i> Community Posts</a>
        <a href="report.php"><i class="fa-solid fa-file-lines"></i> User Report</a>
    </div>

    <div class="header">
        <div class="left-side">Hello, Admin</div>
        <div class="right-side"><a href="logout.php">Logout</a></div>
    </div>
    
    <div class="title">Add Workout Plan</div>

    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <div class="grid">
                <div>
                    <label>Plan Name</label>
                    <input type="text" name="plan_name" required />
                </div>

                <div>
                    <label>Upload Thumbnail</label>
                    <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp,.avif" required />
                </div>

                <div>
                    <label>Target Area</label>
                    <select name="target_area" required>
                        <option>Waist</option>
                        <option>Hips</option>
                        <option>Abs</option>
                        <option>Legs</option>
                        <option>Arms</option>
                        <option>Back</option>
                    </select>
                </div>

                <div>
                    <label>Mood Category</label>
                    <select name="mood" required>
                        <option>Happy</option>
                        <option>Sad</option>
                        <option>Angry</option>
                        <option>Tired</option>
                        <option>Energized</option>
                    </select>
                </div>

                <div>
                    <label>Intensity</label>
                    <select name="intensity" required>
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                    </select>
                </div>

                <div>
                    <label>Fitness Level</label>
                    <select name="fitness_level" required>
                        <option>Beginner</option>
                        <option>Intermediate</option>
                        <option>Advanced</option>
                    </select>
                </div>

                <div>
                    <label>Duration (Days)</label>
                    <input type="number" name="duration" required />
                </div>

                <?php if (isset($videos[0]) && is_array($videos[0])) {
                    $videos = $videos[0];
                } ?>
                <div class="select-box">
                    <label>Select Videos</label>
                    <div class="select-btn" onclick="toggleOptions()">
                        <span id="selected-count"><?= count($selectedVideos) ?> Selected</span>
                        <span>▾</span>
                    </div>
                    <div class="options">
                        <?php foreach ($videos as $video): ?>
                            <label>
                                <input type="checkbox"
                                    name="videos[]"
                                    value="<?= htmlspecialchars($video['file_path']) ?>"
                                    <?= in_array($video['file_path'], $selectedVideos) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($video['title']) ?><br>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- <?php if (!empty($selectedVideos)): ?>
                    <h3>Selected Video Paths:</h3>
                    <ul>
                        <?php foreach ($selectedVideos as $path): ?>
                            <li><?= htmlspecialchars($path) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?> -->

                <div style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>
            </div>

            <div class="btn-row">
                <button type="reset" class="btn cancel">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>

    <script>
    function toggleOptions() {
        const options = document.querySelector('.options');
        options.style.display = options.style.display === 'block' ? 'none' : 'block';
    }

    const checkboxes = document.querySelectorAll('.options input[type="checkbox"]');
    checkboxes.forEach(chk => {
        chk.addEventListener('change', () => {
            const selected = document.querySelectorAll('.options input[type="checkbox"]:checked').length;
            document.getElementById('selected-count').textContent = selected + ' Selected';
        });
    });

    document.addEventListener('click', (e) => {
        const selectBox = document.querySelector('.select-box');
        if (!selectBox.contains(e.target)) {
            document.querySelector('.options').style.display = 'none';
        }
    });
    </script>
</body>
</html>
