<?php
require_once '../components/navbar.php';
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
    <title>EmoMotion</title>
</head>
<body>
    <div class="calculator">
        <div class="left">
            <h3>Calculator</h3>
            <div class="navbar">
                <a href="bmi-calculator.php">BMI Calculator</a>
                <a href="calorie-calculator.php">Calorie Calculator</a>
                <a href="body-fat-calculator.php">Body Fat Calculator</a>
                <a href="tdee-calculator.php">TDEE Calculator</a>
            </div>
        </div>
        <div class="middle hide">
        </div>
    </div>
</body>
</html>