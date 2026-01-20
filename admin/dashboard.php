<?php
require_once "logincheck.php";

$users = $con->prepare("SELECT COUNT(*) AS total FROM users");
$users->execute();
$row = $users->fetch(PDO::FETCH_ASSOC);
$totalUsers = $row['total'];

$videos = $con->prepare("SELECT COUNT(*) AS total FROM workout_videos");
$videos->execute();
$row = $videos->fetch(PDO::FETCH_ASSOC);
$totalVideos = $row['total'];

$plans = $con->prepare("SELECT COUNT(*) AS total FROM workout_plans");
$plans->execute();
$row = $plans->fetch(PDO::FETCH_ASSOC);
$totalPlans = $row['total'];

$posts = $con->prepare("SELECT COUNT(*) AS total FROM community_posts");
$posts->execute();
$row = $posts->fetch(PDO::FETCH_ASSOC);
$totalPosts = $row['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion - Admin Panel</title>
    <link rel="icon" type="image/svg+xml" href="../assets/icons/title-logo.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <div class="top-boxes">
            <div class="box">
                <h3>Total Users</h3>
                <h1><?php echo $totalUsers; ?></h1>
            </div>
            <div class="box">
                <h3>Total Videos</h3>
                <h1><?php echo $totalVideos; ?></h1>
            </div>
            <div class="box">
                <h3>Total Plans</h3>
                <h1><?php echo $totalPlans; ?></h1>
            </div>
            <div class="box">
                <h3>Total Posts</h3>
                <h1><?php echo $totalPosts; ?></h1>
            </div>
        </div>

        <div class="charts">
            <div class="chart-box large">
                <canvas id="waveChart"></canvas>
            </div>
            <div class="chart-box">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        // WAVE CHART
        new Chart(document.getElementById('waveChart'), {
            type: 'line',
            data: {
                labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                datasets: [{
                    label: 'Logged In Rate',
                    data: [3,6,4,8,5,9,7],
                    fill: true,
                    backgroundColor: 'rgba(255,178,26,0.3)',
                    borderColor: '#ffb21a',
                    tension: 0.4
                },{
                    label: 'Sign Up Rate',
                    data: [2,4,3,5,6,7,5],
                    fill: true,
                    backgroundColor: 'rgba(11,47,82,0.3)',
                    borderColor: '#0b2f52',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' }}
            }
        });
        
        // DONUT CHART
        const centerText = {
            id: 'centerText',
            afterDraw(chart, args, options) {
                const { ctx } = chart;
                ctx.save();

                const centerX = chart.getDatasetMeta(0).data[0].x;
                const centerY = chart.getDatasetMeta(0).data[0].y;

                ctx.font = options.fontSize + 'px ' + options.fontFamily;
                ctx.fillStyle = options.color;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(options.text, centerX, centerY);
                ctx.restore();
            }
        };

        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            plugins: [centerText],
            data: {
                labels: ['Active Users','Inactive Users'],
                datasets: [{
                    data: [45,55],
                    backgroundColor: ['#0b2f52','#ffb21a'],
                    cutout: '70%'
                }]
            },
            options: {
                plugins: {
                    centerText: {
                        text: '45%',
                        color: '#0b2f52',
                        fontSize: 30,
                        fontFamily: 'Poppins'
                    },
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>