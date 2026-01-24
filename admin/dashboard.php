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

if (isset($_GET['stats'])) {
    header('Content-Type: application/json');
    $today = date("Y-m-d");
    try {
        $totalStmt = $con->query("SELECT COUNT(*) FROM users");
        $totalUsers = (int)$totalStmt->fetchColumn();
        $activeStmt = $con->prepare("
            SELECT COUNT(DISTINCT user_id)
            FROM user_activity
            WHERE activity_date = :today
        ");
        $activeStmt->execute([':today' => $today]);
        $activeUsers = (int)$activeStmt->fetchColumn();
        $inactiveUsers = $totalUsers - $activeUsers;

        $activePercent = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0;
        echo json_encode([
            'success' => true,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'active_percent' => $activePercent
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit; 
}

if (isset($_GET['wave'])) {
    header('Content-Type: application/json');
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $weekday = date("D", strtotime($day)); 

        $loginStmt = $con->prepare("
            SELECT COUNT(*) 
            FROM user_activity 
            WHERE activity_date = :day
        ");
        $loginStmt->execute([':day' => $day]);
        $loginCount = (int)$loginStmt->fetchColumn();

        $signupStmt = $con->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE DATE(joined_date) = :day
        ");
        $signupStmt->execute([':day' => $day]);
        $signupCount = (int)$signupStmt->fetchColumn();

        $data[] = [
            'date' => date('D', strtotime($day)), 
            'logins' => $loginCount,
            'signups' => $signupCount
        ];
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

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
        fetch("dashboard.php?wave=1")
        .then(res => res.json())
        .then(result => {
            if (!result.success) return;

            const labels = result.data.map(row => row.date);
            const loginData = result.data.map(row => row.logins);
            const signupData = result.data.map(row => row.signups);
           
            new Chart(document.getElementById('waveChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Logged In Rate',
                        data: loginData.map(Number),
                        fill: true,
                        backgroundColor: 'rgba(255,178,26,0.3)',
                        borderColor: '#ffb21a',
                        tension: 0.4
                    },{
                        label: 'Sign Up Rate',
                        data: signupData.map(Number),
                        fill: true,
                        backgroundColor: 'rgba(11,47,82,0.3)',
                        borderColor: '#0b2f52',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' }}
                },
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true,
                            precision: 0, 
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    }
                }
            });
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

        fetch('dashboard.php?stats=1')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error(data.error);
                return;
            }

            const active = data.active_users;
            const inactive = data.inactive_users;
            const percent = data.active_percent + '%';
            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                plugins: [centerText],
                data: {
                    labels: ['Active Users','Inactive Users'],
                    datasets: [{
                        data: [active,inactive],
                        backgroundColor: ['#0b2f52','#ffb21a'],
                        cutout: '70%'
                    }]
                },
                options: {
                    plugins: {
                        centerText: {
                            text: percent,
                            color: '#0b2f52',
                            fontSize: 30,
                            fontFamily: 'Poppins'
                        },
                        legend: { position: 'bottom' }
                    }
                }
            });
        });
    </script>
</body>
</html>