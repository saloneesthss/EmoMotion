<?php
require_once "logincheck.php";

$userid = isset($_GET['id']) ? $_GET['id'] : '';
$where='';
if (!empty($userid)) {
    $where="WHERE u.id=$userid";
}

$sql="SELECT u.name as username, p.* FROM community_posts p INNER JOIN users u ON u.id=p.user_id $where";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
    <link rel="stylesheet" href="../styles/admin/user-details.css">
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
    
    <div class="content">
        <div class="page-title">Community Posts</div>
        <div class="subtitle">List of all community posts made by registered users in the system</div>
        <table>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Title</th>
                <th>Body</th>
                <th>Image</th>
                <th>Category</th>
                <th>Created at</th>
                <th></th>
            </tr>

            <?php foreach ($posts as $post) { ?>
            <tr>
                <td><input type="checkbox"></td>
                <td><?php echo $post['username'];?></td>
                <td><?php echo $post['title'] ?></td>
                <td><?php echo $post['body'] ?></td>
                <td>
                    <?php if (!empty($post['image']) && file_exists('../assets/community-images/' . $post['image'])) { ?>
                        <img width="100" src="../assets/community-images/<?php echo $post['image']; ?>" alt="">
                    <?php } ?>
                </td>
                <td><?php echo $post['category'] ?></td>
                <td><?php echo $post['created_at'] ?></td>
                <td><span class="trash">🗑</span></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>