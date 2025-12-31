<?php
require_once "../connection.php";
$stmt = $con->prepare("select * from users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
    </div>

    <div class="header">
        <div class="left-side">Hello, Admin</div>
        <div class="right-side">Logout</div>
    </div>
    
    <div class="content">
        <div class="page-title">Users</div>
        <div class="subtitle">List of all registered users in the system</div>
        <table>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Name</th>
                <th>Email Address</th>
                <th>BMI</th>
                <th>Country</th>
                <th></th>
            </tr>

            <?php foreach ($users as $user) { ?>
            <tr>
                <td><input type="checkbox"></td>
                <td><?php echo $user['id'] ?></td>
                <td>
                    <div class="row">
                        <?php if (!empty($user['image']) && file_exists('../assets/users-images/' . $user['image'])) { ?>
                            <img width="100" src="../assets/users-images/<?php echo $user['image']; ?>" class="user-icon">
                        <?php } ?>
                        <?php echo $user['name'] ?>
                    </div>
                </td>
                <td><?php echo $user['email'] ?></td>
                <td><?php echo $user['bmi'] ?></td>
                <td><?php echo $user['country'] ?></td>
                <td><span class="trash">🗑</span></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>