<?php
session_start();
require_once "../connection.php";

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
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/calculator.css">
    <link rel="stylesheet" href="../styles/navbar.css">
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
    </div>
    
    <div class="container tdee-container">
        <h2>TDEE Calculator</h2>
        <label>Age (years):</label>
        <input type="number" id="age" required min="1" placeholder="Enter age">

        <label>Gender:</label>
        <select id="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <label>Weight (kg):</label>
        <input type="number" id="weight" required min="1" placeholder="Enter weight">

        <label>Height (cm):</label>
        <input type="number" id="height" required min="1" placeholder="Enter height">

        <label>Activity Level:</label>
        <select id="activity" required>
            <option value="1.2">Sedentary (little or no exercise)</option>
            <option value="1.375">Lightly active (light exercise 1-3 days/week)</option>
            <option value="1.55">Moderately active (moderate exercise 3-5 days/week)</option>
            <option value="1.725">Very active (hard exercise 6-7 days/week)</option>
            <option value="1.9">Extra active (very hard exercise & physical job)</option>
        </select>

        <button type="button" onclick="calculateTDEE()">Calculate TDEE</button>
    </div>

    <div class="container tdee-result-box">
        <h2>Result</h2>
        <p id="result">Your TDEE will appear here.</p>
    </div>

    <script>
        function calculateTDEE() {
            const age = parseInt(document.getElementById('age').value);
            const gender = document.getElementById('gender').value;
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            const activity = parseFloat(document.getElementById('activity').value);

            if (!age || !gender || !weight || !height || !activity) {
                return;
            }

            // BMR calculation (Mifflin-St Jeor)
            let bmr;
            if (gender === "male") {
                bmr = 10 * weight + 6.25 * height - 5 * age + 5;
            } else {
                bmr = 10 * weight + 6.25 * height - 5 * age - 161;
            }

            // TDEE calculation
            const tdee = bmr * activity;
            document.getElementById('result').innerText = `Your TDEE is ${tdee.toFixed(0)} calories/day.`;
        }
    </script>
</body>
</html>