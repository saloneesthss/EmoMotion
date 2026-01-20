<?php
require_once "logincheck.php";

$users = [];
$totalUsers = 0;

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start = $_GET['start_date'];
    $end = $_GET['end_date'];

    $stmt = $con->prepare("SELECT * FROM users WHERE joined_date BETWEEN ? AND ?");
    $stmt->execute([$start, $end]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalUsers = count($users);
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
    <link rel="stylesheet" href="../styles/admin/report.css">
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

    <main class="main">
        <div class="header-title">
            <h1>User Report</h1>
        </div>

        <div class="filter-box">
            <form method="GET">
                <div class="filter-row">
                    <div class="filter-item">
                        <label>From Date</label>
                        <input type="date" name="start_date" required>
                    </div>

                    <div class="filter-item">
                        <label>To Date</label>
                        <input type="date" name="end_date" required>
                    </div>

                    <div class="filter-item btn-box">
                        <button type="submit" class="apply-btn">Generate Report</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($users)): ?>
        <div class="summary-box">
            <h3>Total Users Registered: <span><?php echo $totalUsers; ?></span></h3>
        </div>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td>
                            <div class="row">
                                <?php if (!empty($user['image']) && file_exists('../assets/users-images/' . $user['image'])) { ?>
                                    <img width="100" src="../assets/users-images/<?php echo $user['image']; ?>" class="user-icon">
                                <?php } ?>
                                <?php echo $user['name'] ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= date("M d, Y h:i A", strtotime($user['joined_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>