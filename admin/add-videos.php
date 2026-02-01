<?php
require_once "logincheck.php";

$success = "";
$error = "";
$upload_path = "../assets/gifs";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $video_name = $_POST['video_name'];
    $target_area = $_POST['target_area'];
    $mood = $_POST['mood'];
    $intensity = $_POST['intensity'];
    $fitness_level = $_POST['fitness_level'];
    $repetition = $_POST['repetition'];
    $sets = $_POST['sets'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];

    $file_name = null;
    if(is_uploaded_file($_FILES['video_file']['tmp_name'])) {
        $file_name = $_FILES['video_file']['name'];
        move_uploaded_file($_FILES['video_file']['tmp_name'], $upload_path . '/' . $file_name);
    }
    
    $sql = "insert into workout_videos set title='$video_name', file_path='$file_name', target_area='$target_area', mood='$mood', intensity='$intensity', fitness_level='$fitness_level', repetition='$repetition', sets='$sets', duration='$duration', description='$description'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    header("Location: add-videos.php?success=Video added successfully.");
    die;
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
    <link rel="stylesheet" href="../styles/admin/add-videos.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>

        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="user-details.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="videos-list.php"><i class="fa-solid fa-video"></i> Workout Videos</a>
        <a href="plans-list.php"><i class="fa-solid fa-dumbbell"></i> Workout Plans</a>
        <a href="community-posts.php"><i class="fa-solid fa-comment-dots"></i> Community Posts</a>
        <a href="report.php"><i class="fa-solid fa-file-lines"></i> Sign-up Report</a>
    </div>

    <div class="header">
        <div class="left-side">Hello, Admin</div>
        <div class="right-side"><a href="logout.php">Logout</a></div>
    </div>

    <div class="title">Add Workout Video</div>
    <div class="container">
        <?php if(isset($_GET['success'])) { ?>
            <div class="success" style="color:green;margin-bottom:10px;">
                <?php echo $_GET['success']; ?>
            </div>
        <?php } ?>

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
                        <option value="waist">Waist</option>
                        <option value="hips">Hips</option>
                        <option value="abs">Abs</option>
                        <option value="legs">Legs</option>
                        <option value="arms">Arms</option>
                        <option value="back">Back</option>
                        <option value="full-body">Full Body</option>
                    </select>
                </div>

                <div>
                    <label>Mood Category</label>
                    <select name="mood" required>
                        <option value="happy">Happy</option>
                        <option value="sad">Sad</option>
                        <option value="angry">Angry</option>
                        <option value="tired">Tired</option>
                        <option value="energized">Energized</option>
                    </select>
                </div>

                <div>
                    <label>Intensity</label>
                    <select name="intensity" required>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label>Fitness Level</label>
                    <select name="fitness_level" required>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div>
                    <label>Repetitions</label>
                    <input type="number" min="1" name="repetition" required />
                </div>

                <div>
                    <label>Sets</label>
                    <input type="number" min="1" name="sets" required />
                </div>

                <div>
                    <label>Duration (Seconds)</label>
                    <input type="number" min="1" name="duration" required />
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
