<?php
require_once "logincheck.php";
$stmt = $con->prepare("select * from workout_videos");
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
    <link rel="stylesheet" href="../styles/admin/user-details.css">
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
    
    <div class="content">
        <div class="add-header">
            <div>
                <div class="page-title">Workout Videos</div>
                <div class="subtitle">List of all workout videos posted in the system</div>
            </div>
            <button class="add-btn" onclick="location.href='add-videos.php'">Add</button>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Video/Gif</th>
                <th>Target Area</th>
                <th>Mood</th>
                <th>Intensity</th>
                <th>Fitness Level</th>
                <th>Duration</th>
                <th>Description</th>
                <th></th>
            </tr>

            <?php foreach ($videos as $video) { ?>
            <tr>
                <!-- <td><input type="checkbox"></td> -->
                <td><?php echo $video['id'] ?></td>
                <td><?php echo $video['title'] ?></td>
                <td>
                    <div class="row">
                        <?php if (!empty($video['file_path']) && file_exists('../assets/gifs/' . $video['file_path'])) { ?>
                            <img width="100" src="../assets/gifs/<?php echo $video['file_path']; ?>" class="video-icon">
                        <?php } ?>
                    </div>
                </td>
                <td><?php echo $video['target_area'] ?></td>
                <td><?php echo $video['mood'] ?></td>
                <td><?php echo $video['intensity'] ?></td>
                <td><?php echo $video['fitness_level'] ?></td>
                <td><?php echo $video['duration'] ?>s</td>
                <td><?php echo $video['description'] ?></td>
                <td>
                    <span class="edit"><a href="edit-videos.php?id=<?php echo $video['id']; ?>">🖊</a></span>
                    <span class="trash"><a href="">🗑</a></span>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>