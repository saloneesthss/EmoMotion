<?php
require_once 'calculator.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
</head>
<body>
    <div class="container body-fat-container">
        <h2>Body Fat Calculator</h2>

        <label>Gender:</label>
        <select id="gender" required onchange="toggleHip()">
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <label>Age:</label>
        <input type="number" id="age" required min="1" placeholder="Enter age">

        <label>Height (cm):</label>
        <input type="number" id="height" required min="1" placeholder="Enter height">

        <label>Neck (cm):</label>
        <input type="number" id="neck" required min="1" placeholder="Enter neck circumference">

        <label>Waist (cm):</label>
        <input type="number" id="waist" required min="1" placeholder="Enter waist circumference">

        <div id="hipContainer" style="display:none;">
        <label>Hip (cm) — only for females:</label>
        <input type="number" id="hip" min="1" placeholder="Enter hip circumference">
        </div>

        <button onclick="calculateBodyFat()">Calculate Body Fat %</button>
    </div>

    <div class="container body-fat-result-box">
        <h2>Result</h2>
        <ul class="details">
            <li id="bodyFat">Body Fat: -- %</li>
            <li id="fatMass">Fat Mass: -- kg</li>
            <li id="leanMass">Lean Body Mass: -- kg</li>
        </ul>
    </div>

    <script>
        function toggleHip() {
        let gender = document.getElementById("gender").value;
        document.getElementById("hipContainer").style.display = gender === "female" ? "block" : "none";
        }

        function calculateBodyFat() {
            let gender = document.getElementById("gender").value;
            let age = parseInt(document.getElementById("age").value);
            let height = parseFloat(document.getElementById("height").value);
            let neck = parseFloat(document.getElementById("neck").value);
            let waist = parseFloat(document.getElementById("waist").value);
            let hip = gender === "female" ? parseFloat(document.getElementById("hip").value) : 0;

            if (!age || !height || !neck || !waist || (gender === "female" && !hip)) return;

            // US Navy Formula
            let bodyFat = 0;
            if (gender === "male") {
                bodyFat = 495 / (1.0324 - 0.19077 * Math.log10(waist - neck) + 0.15456 * Math.log10(height)) - 450;
            } else {
                bodyFat = 495 / (1.29579 - 0.35004 * Math.log10(waist + hip - neck) + 0.22100 * Math.log10(height)) - 450;
            }

            // Weight estimation for fat mass (requires user weight if needed)
            let weight = (height - 100) + (age * 0.1);

            let fatMass = weight * (bodyFat / 100);
            let leanMass = weight - fatMass;

            document.getElementById("bodyFat").innerHTML = `Body Fat: ${bodyFat.toFixed(1)} %`;
            document.getElementById("fatMass").innerHTML = `Fat Mass: ${fatMass.toFixed(1)} kg`;
            document.getElementById("leanMass").innerHTML = `Lean Body Mass: ${leanMass.toFixed(1)} kg`;
        }
    </script>
</body>
</html>