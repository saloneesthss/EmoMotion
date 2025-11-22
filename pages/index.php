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
    <title>EmoMotion</title>
</head>
<body>
    <div class="container">
        <div class='main' id='main'>
            <div class='main-text'>
                <p>Move with your Mood.</p>
                <div class='txt1'>
                    Your digital coach for physical and emotional wellness.
                </div>
                <div class='txt2'>
                    <span>Start your fitness journey now</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
                <div class='buttons'>
                    <button class='signup' onclick="window.location.href='../signup.php'">Sign Up</button>
                    <button class='login' onclick="window.location.href='../login.php'">Log In</button>
                </div>
            </div>
                
            <div class='main-image'>
                <div></div>
                <img src="../assets/images/main.png" />
            </div>
        </div>

        <div class="workout-videos">
            <div class="title-bar">
                <span>Workout Videos</span>
                <button id="view-all-button">View All Videos</button>
            </div>
            
            <div id="exercise-container" class="exercise-grid"></div>
        </div>

        <div class="workout-plans">
            <div class="title-bar">
                <span>Workout Plans</span>
                <button>View All Plans</button>
            </div>
            <div class="plans">
                <div class="video">video 1</div>
                <div class="video">video 2</div>
                <div class="video">video 3</div>
                <div class="video">video 4</div>
                <div class="video">video 5</div>
                <div class="video">video 6</div>
            </div>
        </div>
    </div>

    <script src="../apis/fetch-data.js"></script>
</body>
</html>