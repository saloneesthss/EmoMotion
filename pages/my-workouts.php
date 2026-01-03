<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/settings.css">
    <link rel="stylesheet" href="../styles/user-profile.css">
    <link rel="stylesheet" href="../styles/navbar.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h2>EmoMotion</h2>

            <a href="user-profile.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</body>
</html>