<?php
require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/index.css">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="../styles/workout-videos.css">
</head>
<body>
    <div class='container'>
        <div class="collection-filter">
            <button class="collection-button">Browse By Collection ▾</button>
            <ul class="collection-menu">
                <li data-filter="abs">Abs</li>
                <li data-filter="arms">Arms</li>
                <li data-filter="legs">Legs</li>
                <li data-filter="back">Back</li>
                <li data-filter="chest">Chest</li>
                <li data-filter="shoulders">Shoulders</li>
                <li data-filter="fullbody">Full Body</li>
                <li data-filter="cardio">Cardio</li>
                <li data-filter="stretch">Stretch</li>
            </ul>
        </div>
        <div class="exercise-grid"></div>
    </div>

    <script type="module" src="../scripts/workout-videos.js"></script>
</body>
</html>