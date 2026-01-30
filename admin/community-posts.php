<?php
require_once "logincheck.php";

$userid = isset($_GET['id']) ? $_GET['id'] : '';
$where='';
if (!empty($userid)) {
    $where="WHERE u.id=$userid";
}

$sql="SELECT u.name as username, u.image as user_image, p.* FROM community_posts p INNER JOIN users u ON u.id=p.user_id $where";
$stmt = $con->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <div class="page-title">Community Posts</div>
        <div class="subtitle">List of all community posts made by registered users in the system</div>
        <table>
            <tr>
                <th>Name</th>
                <th>Title</th>
                <th>Body</th>
                <th>Image</th>
                <th>Category</th>
                <th>Created at</th>
            </tr>

            <?php foreach ($posts as $post) { ?>
            <tr>
                <td>
                    <div id="user-row">
                    <?php
                        $hasImage = (!empty($post['user_image']) && file_exists("../assets/users-images/" . $post['user_image']));
                        $nameParts = explode(" ", trim($post['username']));
                        $initials = strtoupper(substr($nameParts[0], 0, 1));
                        if (count($nameParts) > 1) {
                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                        }
                    ?>
                    <?php if ($hasImage): ?>
                        <img src="../assets/users-images/<?php echo $post['user_image']; ?>" class="user-icon">
                    <?php else: ?>
                        <div class="user-icon">
                            <?php echo $initials; ?>
                        </div>
                    <?php endif; ?>
                    <?php echo explode(' ', trim($post['username']))[0]; ?>
                    </div>
                </td>
                <td><?php echo $post['title'] ?></td>
                <td><?php echo $post['body'] ?></td>
                <td>
                    <?php 
                        $images = json_decode($post['image'], true);
                        if (!empty($images)) {
                            foreach ($images as $image) {
                                echo "<img src='../assets/community-images/$image' style='width:100px; margin:2px;'>";
                            }
                        }
                    ?>
                </td>
                <td><?php echo $post['category'] ?></td>
                <td><?php echo $post['created_at'] ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>