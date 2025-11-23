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
            <a href="">
                <img src="../assets/icons/search.svg" alt="Search" id="search">
            </a>
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
    </script>
</body>
</html>