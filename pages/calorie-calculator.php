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
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/calculator.css">
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
    
    <div class="container calorie-container">
        <h2>Calorie Calculator</h2>

        <label>Age (years):</label>
        <input type="number" id="age" required min="1" placeholder="Enter age" />

        <label>Gender:</label>
        <select id="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <label>Weight (kg):</label>
        <input type="number" id="weight" required min="1" placeholder="Enter weight" />

        <label>Height (cm):</label>
        <input type="number" id="height" required min="1" placeholder="Enter height" />

        <label>Activity Level:</label>
        <select id="activity" required>
            <option value="1.2">Sedentary (little exercise)</option>
            <option value="1.375">Lightly active</option>
            <option value="1.55">Moderately active</option>
            <option value="1.725">Very active</option>
            <option value="1.9">Super active</option>
        </select>

        <button onclick="calculateCalories()">Calculate Calories</button>
    </div>

    <div class="container calorie-result-box">
        <h2>Result</h2>
        <ul class="details">
            <li id="bmr">BMR: -- kcal/day</li>
            <li id="tdee">TDEE: -- kcal/day</li>
            <li id="lose">To Lose Weight: -- kcal/day</li>
            <li id="gain">To Gain Weight: -- kcal/day</li>
        </ul>
    </div>
    
    <script>
        function calculateCalories() {
            let age = parseInt(document.getElementById("age").value);
            let gender = document.getElementById("gender").value;
            let weight = parseFloat(document.getElementById("weight").value);
            let height = parseFloat(document.getElementById("height").value);
            let activity = parseFloat(document.getElementById("activity").value);

            if (!age || !weight || !height) return;

            // BMR using Mifflin-St Jeor Equation
            let bmr = 0;
            if (gender === "male") {
                bmr = 88.362 + (13.397 * weight) + (4.799 * height) - (5.677 * age);
            } else {
                bmr = 447.593 + (9.247 * weight) + (3.098 * height) - (4.330 * age);
            }

            let tdee = bmr * activity;
            let lose = tdee - 500;
            let gain = tdee + 500;

            document.getElementById("bmr").innerHTML = `BMR: ${bmr.toFixed(1)} kcal/day`;
            document.getElementById("tdee").innerHTML = `TDEE: ${tdee.toFixed(1)} kcal/day`;
            document.getElementById("lose").innerHTML = `To Lose Weight: ${lose.toFixed(1)} kcal/day`;
            document.getElementById("gain").innerHTML = `To Gain Weight: ${gain.toFixed(1)} kcal/day`;
        }
    </script>
</body>
</html>