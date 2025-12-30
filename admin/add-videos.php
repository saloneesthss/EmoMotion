<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $video_name = $_POST['video_name'];
    $target_area = $_POST['target_area'];
    $mood = $_POST['mood'];
    $intensity = $_POST['intensity'];
    $fitness_level = $_POST['fitness_level'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];

    $file_name = $_FILES["video_file"]["name"];
    $tmp = $_FILES["video_file"]["tmp_name"];
    move_uploaded_file($tmp, "uploads/videos/" . $file_name);

    echo "<script>alert('Workout Video Added Successfully!');</script>";
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
    <link rel="stylesheet" href="../styles/admin/add-videos.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>

        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="user-details.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add-videos.php"><i class="fa-solid fa-video"></i> Workout Videos</a>
        <a href="add-plans.php"><i class="fa-solid fa-dumbbell"></i> Workout Plans</a>
        <a href="community-posts.php"><i class="fa-solid fa-comment-dots"></i> Community Posts</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
    </div>

    <div class="header">
        <div class="left-side">Hello, Admin</div>
        <div class="right-side">Logout</div>
    </div>

    <div class="title">Add Workout Video</div>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <div class="grid">
                <div>
                    <label>Video Title</label>
                    <input type="text" name="video_name" required />
                </div>

                <div>
                    <label>Upload Workout Video / GIF</label>
                    <input type="file" name="video_file" accept="video/*,image/gif" required />
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
                    <label>Duration (Minutes)</label>
                    <input type="number" name="duration" required />
                </div>

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
</body>
</html>
