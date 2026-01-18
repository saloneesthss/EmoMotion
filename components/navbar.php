<?php
require_once "../connection.php";
$query = $_GET['query'] ?? '';
$search = "%$query%";
$videoStmt = $con->prepare("SELECT * FROM workout_videos WHERE title LIKE :q OR target_area LIKE :q");
$videoStmt->execute([':q' => $search]);
$videos = $videoStmt->fetchAll(PDO::FETCH_ASSOC);

$planStmt = $con->prepare("SELECT * FROM workout_plans WHERE plan_name LIKE :q OR target_area LIKE :q");
$planStmt->execute([':q' => $search]);
$plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);

$results = array_merge($videos, $plans);
if (count($results) === 0) {
    echo "<div class='no-results'>No matches found</div>";
    exit;
}

foreach ($results as $item) {
    // $typeLabel = $item['type'] === 'video' ? "🎬 Video" : "📘 Plan";

    echo "
        <a href='" . ($item['type'] === 'video' 
            ? "video.php?id=".$item['id'] 
            : "plan.php?id=".$item['id']) . "' class='search-result'>
            <div class='result-item'>
                <span class='result-title'>{$item['title']}</span>
            </div>
        </a>
    ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>
        EmoMotion
    </title>
</head>
<body>
    <div class="header">
        <div class="left-section">
            <a href="">EmoMotion</a>
        </div>

        <div class="middle-section">
            <a href="../pages/index.php">Home</a>
            <a href="../pages/workout-videos.php">Workout Videos</a>
            <a href="../pages/workout-plans.php">Workout Plans</a>
            <a href="../pages/calculator.php">Calculator</a>
            <a href="../pages/about.php">About</a>
        </div>

        <div class="right-section">
            <input type="text" class="search" placeholder="Search..">
            <div class="search-wrapper">
                <img src="../assets/icons/search.svg" alt="Search" id="search-icon">
                <input type="text" id="search-field" placeholder="Search..." />
            </div>

            <a href="../login.php">
                <img src="../assets/icons/user.svg" alt="User Profile" id="user-profile">
            </a>
        </div>

        <div class="hamburger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <script>
        const hamburger = document.querySelector(".hamburger-menu");
        const navLinks = document.querySelector(".middle-section");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navLinks.classList.toggle("active");
        });

        const searchIcon = document.getElementById('search-icon');
        const searchField = document.getElementById('search-field');

        searchIcon.addEventListener('click', () => {
            searchField.classList.toggle('active');
            searchField.focus();
        });

        // Live search fetch to PHP
        searchField.addEventListener('keyup', function () {
            let query = this.value;

            fetch("search.php?query=" + query)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("results-container").innerHTML = data;
                });
        });
    </script>
</body>
</html>