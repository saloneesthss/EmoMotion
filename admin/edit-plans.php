<?php
require_once "logincheck.php";

if (!isset($_GET['id'])) {
    header("Location:add-plans.php?error=Please provide a valid ID for the plan.");
    die;
}

$success = "";
$error = "";
$upload_path = "../assets/plans-thumbnail";
$id=(int) $_GET['id'];

$sql="select * from workout_plans where id=$id";
$stmt=$con->prepare($sql);
$stmt->execute();
$plans=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$plans) {
    header("Location:add-plans.php?error=No plan found with the given ID.");
    die;
}

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

        foreach ($videoDurations as $dur) {
            $totalDuration += (int)$dur;
        }
    }

    $imageNameOld = $_POST['image_name_old'];
    $file_name = $imageNameOld;
    if (is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {
        if (!empty($imageNameOld) && file_exists('../assets/plans-thumbnail/' . $imageNameOld)) {
            unlink('../assets/plans-thumbnail/' . $imageNameOld);
        }
        $file_name = $_FILES['thumbnail']['name'];
        move_uploaded_file(
            $_FILES['thumbnail']['tmp_name'],
            $upload_path . '/' . $file_name
        );
    }

    $sql = "update workout_plans set plan_name=:plan_name, file_path=:file_path, target_area=:target_area, mood=:mood, intensity=:intensity, fitness_level=:fitness_level, duration=:duration, time_duration=:time_duration, description=:description, video_list=:video_list where id=$id";

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
    header("Location:plans-list.php?success=Plan added successfully.");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion - Admin Panel</title>
    <link rel="icon" type="image/svg+xml" href="../assets/icons/title-logo.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
    <link rel="stylesheet" href="../styles/admin/add-plans.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>

        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="user-details.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="videos-list.php"><i class="fa-solid fa-video"></i> Workout Videos</a>
        <a href="plans-list.php"><i class="fa-solid fa-dumbbell"></i> Workout Plans</a>
        <a href="community-posts.php"><i class="fa-solid fa-comment-dots"></i> Community Posts</a>
        <a href="report.php"><i class="fa-solid fa-file-lines"></i> User Report</a>
    </div>

    <div class="header">
        <div class="left-side">Hello, Admin</div>
        <div class="right-side"><a href="logout.php">Logout</a></div>
    </div>
    
    <div class="title">Edit Workout Plan</div>

    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <div class="grid">
                <div>
                    <label>Plan Name</label>
                    <input type="text" name="plan_name" value="<?php echo $plans['plan_name'] ?>" required />
                </div>

                <div>
                    <label>Update Thumbnail</label>
                    <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp,.avif" required />
                    <input type="hidden" name="image_name_old" value="<?php echo $plans['file_path']; ?>">
                    <?php if (!empty($plans['file_path']) && file_exists('../assets/plans-thumbnail/' . $plans['file_path'])) { ?>
                        <img width="100" src="../assets/plans-thumbnail/<?php echo $plans['file_path']; ?>" alt="">
                    <?php } ?>
                </div>

                <div>
                    <label>Target Area</label>
                    <select name="target_area" required>
                        <option <?= ($plans['target_area'] == 'Waist') ? 'selected' : '' ?>>Waist</option>
                        <option <?= ($plans['target_area'] == 'Hips') ? 'selected' : '' ?>>Hips</option>
                        <option <?= ($plans['target_area'] == 'Abs') ? 'selected' : '' ?>>Abs</option>
                        <option <?= ($plans['target_area'] == 'Legs') ? 'selected' : '' ?>>Legs</option>
                        <option <?= ($plans['target_area'] == 'Arms') ? 'selected' : '' ?>>Arms</option>
                        <option <?= ($plans['target_area'] == 'Back') ? 'selected' : '' ?>>Back</option>
                        <option <?= ($plans['target_area'] == 'Full Body') ? 'selected' : '' ?>>Full Body</option>
                    </select>
                </div>

                <div>
                    <label>Mood Category</label>
                    <select name="mood" required>
                        <option <?= ($plans['mood'] == 'Happy') ? 'selected' : '' ?>>Happy</option>
                        <option <?= ($plans['mood'] == 'Sad') ? 'selected' : '' ?>>Sad</option>
                        <option <?= ($plans['mood'] == 'Angry') ? 'selected' : '' ?>>Angry</option>
                        <option <?= ($plans['mood'] == 'Tired') ? 'selected' : '' ?>>Tired</option>
                        <option <?= ($plans['mood'] == 'Energized') ? 'selected' : '' ?>>Energized</option>
                    </select>
                </div>

                <div>
                    <label>Intensity</label>
                    <select name="intensity" required>
                        <option <?= ($plans['intensity'] == 'Low') ? 'selected' : '' ?>>Low</option>
                        <option <?= ($plans['intensity'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                        <option <?= ($plans['intensity'] == 'High') ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div>
                    <label>Fitness Level</label>
                    <select name="fitness_level" required>
                        <option <?= ($plans['fitness_level'] == 'Beginner') ? 'selected' : '' ?>>Beginner</option>
                        <option <?= ($plans['fitness_level'] == 'Intermediate') ? 'selected' : '' ?>>Intermediate</option>
                        <option <?= ($plans['fitness_level'] == 'Advanced') ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>

                <div>
                    <label>Duration (Days)</label>
                    <input type="number" name="duration" value="<?php echo $plans['duration'] ?>" required />
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

                <div style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description"><?php echo $plans['description'] ?></textarea>
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
