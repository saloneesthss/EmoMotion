<?php
require_once "logincheck.php";

if (!isset($_GET['id'])) {
    header("Location:videos-list.php?error=Please provide a valid ID for the video.");
    die;
}

$success = "";
$error = "";
$upload_path = "../assets/gifs";

$id=(int) $_GET['id'];

$sql="select * from workout_videos where id=$id";
$stmt=$con->prepare($sql);
$stmt->execute();
$videos=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$videos) {
    header("Location:add-videos.php?error=No video found with the given ID.");
    die;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $video_name = $_POST['video_name'];
    $target_area = $_POST['target_area'];
    $mood = $_POST['mood'];
    $intensity = $_POST['intensity'];
    $fitness_level = $_POST['fitness_level'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];

    $imageNameOld = $_POST['image_name_old'];
    $file_name = null;
    if(is_uploaded_file($_FILES['video_file']['tmp_name'])) {
        if (!empty($imageNameOld) && file_exists('../assets/gifs/' . $imageNameOld)) {
            unlink('../assets/gifs/' . $imageNameOld);
        }
        $file_name = $_FILES['video_file']['name'];
        move_uploaded_file($_FILES['video_file']['tmp_name'], $upload_path . '/' . $file_name);
    }
    
    $sql = "update workout_videos set title='$video_name', file_path='$file_name', target_area='$target_area', mood='$mood', intensity='$intensity', fitness_level='$fitness_level', duration='$duration', description='$description' where id=$id";
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
        <a href="report.php"><i class="fa-solid fa-file-lines"></i> User Report</a>
    </div>

    <div class="header">
        <div class="left-side">Hello, Admin</div>
        <div class="right-side"><a href="logout.php">Logout</a></div>
    </div>

    <div class="title">Edit Workout Video</div>
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
                    <input type="text" name="video_name" value="<?php echo $videos['title'] ?>" required />
                </div>

                <div>
                    <label>Update Workout Video / GIF</label>
                    <input type="file" name="video_file" accept="video/*,image/gif" required />
                    <input type="hidden" name="image_name_old" value="<?php echo $videos['file_path']; ?>">
                    <?php if (!empty($videos['file_path']) && file_exists('../assets/gifs/' . $videos['file_path'])) { ?>
                        <img width="100" src="../assets/gifs/<?php echo $videos['file_path']; ?>" alt="">
                    <?php } ?>
                </div>

                <div>
                    <label>Target Area</label>
                    <select name="target_area" required>
                        <option value="waist" <?php echo ($videos['target_area'] == 'Waist') ? 'selected' : '';?>>Waist</option>
                        <option value="hips" <?php echo ($videos['target_area'] == 'Hips') ? 'selected' : '';?>>Hips</option>
                        <option value="abs" <?php echo ($videos['target_area'] == 'Abs') ? 'selected' : '';?>>Abs</option>
                        <option value="legs" <?php echo ($videos['target_area'] == 'Legs') ? 'selected' : '';?>>Legs</option>
                        <option value="arms" <?php echo ($videos['target_area'] == 'Arms') ? 'selected' : '';?>>Arms</option>
                        <option value="back" <?php echo ($videos['target_area'] == 'Back') ? 'selected' : '';?>>Back</option>
                    </select>
                </div>

                <div>
                    <label>Mood Category</label>
                    <select name="mood" required>
                        <option value="happy" <?php echo ($videos['mood'] == 'Happy') ? 'selected' : '';?>>Happy</option>
                        <option value="sad" <?php echo ($videos['mood'] == 'Sad') ? 'selected' : '';?>>Sad</option>
                        <option value="angry" <?php echo ($videos['mood'] == 'Angry') ? 'selected' : '';?>>Angry</option>
                        <option value="tired" <?php echo ($videos['mood'] == 'Tired') ? 'selected' : '';?>>Tired</option>
                        <option value="energized" <?php echo ($videos['mood'] == 'Energized') ? 'selected' : '';?>>Energized</option>
                    </select>
                </div>

                <div>
                    <label>Intensity</label>
                    <select name="intensity" required>
                        <option value="low" <?php echo ($videos['intensity'] == 'Low') ? 'selected' : '';?>>Low</option>
                        <option value="medium" <?php echo ($videos['intensity'] == 'Medium') ? 'selected' : '';?>>Medium</option>
                        <option value="high" <?php echo ($videos['intensity'] == 'High') ? 'selected' : '';?>>High</option>
                    </select>
                </div>

                <div>
                    <label>Fitness Level</label>
                    <select name="fitness_level" required>
                        <option value="beginner" <?php echo ($videos['fitness_level'] == 'Beginner') ? 'selected' : '';?>>Beginner</option>
                        <option value="intermediate" <?php echo ($videos['fitness_level'] == 'Intermediate') ? 'selected' : '';?>>Intermediate</option>
                        <option value="advanced" <?php echo ($videos['fitness_level'] == 'Advanced') ? 'selected' : '';?>>Advanced</option>
                    </select>
                </div>

                <div>
                    <label>Duration (Seconds)</label>
                    <input type="number" name="duration" value="<?php echo $videos['duration'] ?>" required />
                </div>

                <div style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description"><?php echo $videos['description'] ?></textarea>
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
