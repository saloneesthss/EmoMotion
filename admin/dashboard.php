<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa-solid fa-gear"></i> Admin</h2>

        <a href="#"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="#"><i class="fa-solid fa-users"></i> Users</a>
        <a href="#"><i class="fa-solid fa-video"></i> Workout Videos</a>
        <a href="#"><i class="fa-solid fa-dumbbell"></i> Workout Plans</a>
        <a href="#"><i class="fa-solid fa-chart-column"></i> Charts</a>
        <a href="#"><i class="fa-solid fa-layer-group"></i> Components</a>
        <a href="#"><i class="fa-solid fa-file"></i> Layouts</a>
        <a href="#"><i class="fa-solid fa-circle-info"></i> About</a>
    </div>

    <div class="main">
        <div class="cards">
            <div class="card blue">
                <h3>Total Users</h3>
                <div class="value" id="totalUsers">0</div>
            </div>
            <div class="card green">
                <h3>Active Users</h3>
                <div class="value" id="activeUsers">0</div>
            </div>
            <div class="card red">
                <h3>Inactive Users</h3>
                <div class="value" id="inactiveUsers">0</div>
            </div>
        </div>

        <div class="row">
            <!-- CHART -->
            <div class="chart-box">
                <h3>Users Chart</h3>
                <canvas id="barChart"></canvas>
            </div>

            <div class="notif-box">
                <h3>Notifications</h3>
                <div class="notif-item">
                    <span><i class="fa-solid fa-comment"></i> New user registered</span>
                    <span>1 min ago</span>
                </div>
                <div class="notif-item">
                    <span><i class="fa-solid fa-comment"></i> New comment</span>
                    <span>20 min ago</span>
                </div>
                <div class="notif-item">
                    <span><i class="fa-solid fa-comment"></i> System update</span>
                    <span>1 hour ago</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Dummy data for users
        let totalUsers = 12;
        let activeUsers = 8;
        let inactiveUsers = totalUsers - activeUsers;

        document.getElementById("totalUsers").innerText = totalUsers;
        document.getElementById("activeUsers").innerText = activeUsers;
        document.getElementById("inactiveUsers").innerText = inactiveUsers;

        // Bar Chart
        // new Chart(document.getElementById("barChart"), {
        //     type: "bar",
        //     data: {
        //         labels: ["Mon", "Tue", "Wed", "Thu", "Fri"],
        //         datasets: [{
        //             label: "Users Joined",
        //             data: [12, 19, 3, 5, 2],
        //             backgroundColor: "#3b82f6",
        //         }]
        //     },
        //     options: { responsive: true, maintainAspectRatio: false }
        // });
    </script>
</body>
</html>