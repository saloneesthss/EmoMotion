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

    <div class="container">
        <h2>BMI Calculator</h2>

        <label for="weight">Weight (kg):</label>
        <input type="number" id="weight" required min="1" placeholder="Enter weight" />

        <label for="height">Height (cm):</label>
        <input type="number" id="height" required min="1" placeholder="Enter height" />

        <button onclick="calculateBMI()">Calculate BMI</button>

        <div class="result" id="result"></div>
    </div>

    <div class="container result-box">
        <h2>Result</h2>
        <div class="bmi-display">
            <h3 id="bmiValue">BMI = --</h3>
            <p id="bmiCategory">Category: --</p>
        </div>

        <ul class="details">
            <li id="healthyRange">Healthy BMI range: -- kg/m²</li>
            <li id="healthyWeight">Healthy weight for height: -- kg</li>
            <li id="bmiPrime">BMI Prime: --</li>
            <li id="ponderalIndex">Ponderal Index: -- kg/m³</li>
        </ul>
    </div>

    <script>
        function calculateBMI() {
            let weight = parseFloat(document.getElementById("weight").value);
            let height = parseFloat(document.getElementById("height").value);
            let result = document.getElementById("result");
            let bmiResult = document.getElementById("bmiValue");
            let bmiCategory = document.getElementById("bmiCategory");

            // Algorithm: BMI = weight / (height^2)
            if (!weight || !height || height <= 0) {
                result.innerHTML = "Please enter valid weight and height.";
                return;
            }

            let bmi = weight * 10000 / (height * height);
            let category = "";

            if (bmi < 18.5) category = "Underweight";
            else if (bmi < 24.9) category = "Normal";
            else if (bmi < 29.9) category = "Overweight";
            else category = "Obese";

            bmiResult.innerHTML = `BMI = ${bmi.toFixed(2)}`;
            bmiCategory.innerHTML = `Category = ${category}`;

            document.getElementById("healthyRange").innerHTML = `Healthy BMI range: 18.5 - 25 kg/m²`;
            document.getElementById("healthyWeight").innerHTML = `Healthy weight for height: 47.4 kg - 64 kg`;
            document.getElementById("bmiPrime").innerHTML = `BMI Prime: 0.86`;
            document.getElementById("ponderalIndex").innerHTML = `Ponderal Index: 13.4 kg/m³`;
        }
    </script>
</body>
</html>