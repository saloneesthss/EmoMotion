<?php
require_once "logincheck.php";
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
        <div class="page-title">Users</div>
        <div class="subtitle">List of all registered users in the system</div>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email Address</th>
                <th>BMI</th>
                <th>Joined On</th>
            </tr>

            <?php foreach ($users as $user) { ?>
            <tr>
                <!-- <td><input type="checkbox"></td> -->
                <td><?php echo $user['id'] ?></td>
                <td>
                    <div class="row">
                        <?php
                            $hasImage = (!empty($user['image']) && file_exists("../assets/users-images/" . $user['image']));
                            $nameParts = explode(" ", trim($user['name']));
                            $initials = strtoupper(substr($nameParts[0], 0, 1));
                            if (count($nameParts) > 1) {
                                $initials .= strtoupper(substr(end($nameParts), 0, 1));
                            }
                        ?>
                        <?php if ($hasImage): ?>
                            <img src="../assets/users-images/<?php echo $user['image']; ?>" class="user-icon">
                        <?php else: ?>
                            <div class="user-icon">
                                <?php echo $initials; ?>
                            </div>
                        <?php endif; ?>
                        <?php echo $user['name'] ?>
                    </div>
                </td>
                <td><?php echo $user['email'] ?></td>
                <td><?php echo $user['bmi'] ?></td>
                <td><?php echo $user['joined_date'] ?></td>
                <!-- <td><span class="trash">🗑</span></td> -->
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>