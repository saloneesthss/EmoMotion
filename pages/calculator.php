<?php
session_start();

if (isset($_SESSION['user_id'])) {
    require_once '../components/user-navbar.php';
} else {
    require_once '../components/navbar.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/calculator.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>EmoMotion</title>
</head>
<body>
    <div class="calculator">
        <div class="left">
            <h3 onclick="location.href='calculator.php'">Calculator</h3>
            <div class="navbar">
                <a href="bmi-calculator.php">BMI Calculator</a>
                <a href="calorie-calculator.php">Calorie Calculator</a>
                <a href="body-fat-calculator.php">Body Fat Calculator</a>
                <a href="tdee-calculator.php">TDEE Calculator</a>
            </div>
        </div>

        <div class="main-content hide">
            <h1>Welcome to Your Health Dashboard</h1>

            <div class="stats">
                <div class="stat-card">
                    <h3>BMI</h3>
                    <p>Healthy Range: 18.5 - 24.9</p>
                </div>
                <div class="stat-card">
                    <h3>Body Fat</h3>
                    <p>Men: 10% - 20% | Women: 18% - 28%</p>
                </div>
                <div class="stat-card">
                    <h3>TDEE</h3>
                    <p>Average: 2000 - 2500 cal/day</p>
                </div>
                <div class="stat-card">
                    <h3>Calories</h3>
                    <p>Maintain weight: 2000 cal/day</p>
                </div>
            </div>

            <div class="cards">
                <a href="tdee-calculator.php" class="card tdee">
                    <i class="bi bi-lightning-fill"></i>
                    <h3>TDEE Calculator</h3>
                    <p>Find your daily energy needs.</p>
                </a>
                <a href="bmi-calculator.php" class="card bmi">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <h3>BMI Calculator</h3>
                    <p>Check your body mass index.</p>
                </a>
                <a href="body-fat-calculator.php" class="card bodyfat">
                    <i class="bi bi-person-fill"></i>
                    <h3>Body Fat Calculator</h3>
                    <p>Measure your body fat percentage.</p>
                </a>
                <a href="calorie-calculator.php" class="card calories">
                    <i class="bi bi-egg-fried"></i>
                    <h3>Calorie Calculator</h3>
                    <p>Track your daily calories.</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>