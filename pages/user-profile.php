<?php
session_start();
require_once '../connection.php';
require_once '../components/user-navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = intval($_SESSION['user_id']);
$stmt = $con->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(":id", $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found!");
}
$imagePath = (!empty($user['image']) && file_exists("../assets/users-images/" . $user['image']))
            ? "../assets/users-images/" . $user['image']
            : "../assets/default-avatar.png";
            
$bmi = "";
if (!empty($user['height']) && !empty($user['weight'])) {
    $m = $user['height'] / 100; 
    $bmi = number_format($user['weight'] / ($m * $m), 2);

    $updateBmi = $con->prepare("UPDATE users SET bmi = :bmi WHERE id = :id");
    $updateBmi->bindParam(":bmi", $bmi);
    $updateBmi->bindParam(":id", $user_id);
    $updateBmi->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/user-profile.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h2>EmoMotion</h2>

            <a href="user-profile.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" onclick="return confirm('Are you sure to logout?')" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <div class="main-content">
            <div class="topbar">
                <h2 class="page-title">My Profile</h2>
                <button class="edit-btn" onclick="location.href='settings.php?id=<?php echo $user['id'];?>'">Edit</button>
            </div>

            <div class="profile-card">
                <span>
                    <?php
                        $hasImage = (!empty($user['image']) && file_exists("../assets/users-images/" . $user['image']));
                        $nameParts = explode(" ", trim($user['name']));
                        $initials = strtoupper(substr($nameParts[0], 0, 1));
                        if (count($nameParts) > 1) {
                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                        }
                    ?>
                    <?php if ($hasImage): ?>
                        <img src="<?php echo $imagePath; ?>" class="avatar">
                    <?php else: ?>
                        <div class="avatar initials-avatar">
                            <?php echo $initials; ?>
                        </div>
                    <?php endif; ?>
                    <div class="details">
                        <h3><?php echo $user['name']; ?></h3>
                        <p><?php echo $user['country']; ?></p>
                    </div>
                </span>

                <div id="streakDisplay"></div>
            </div>

            <div class="info-card">
                <div class="header-row">
                    <h3>Personal Information</h3>
                </div>

                <div class="info-grid">
                    <div><span>Full Name</span><p><?php echo $user['name']; ?></p></div>
                    <div><span>Date of Birth</span><p><?php echo $user['dob']; ?></p></div>
                    <div><span>Email</span><p><?php echo $user['email']; ?></p></div>
                    <div><span>Weight</span><p><?php echo $user['weight']; ?> kg</p></div>
                    <div><span>Height</span><p><?php echo $user['height']; ?> cm</p></div>
                    <div><span>BMI</span><p><?php echo $bmi; ?></p></div>
                </div>
            </div>

            <div class="info-card">
                <div class="header-row">
                    <h3>Address</h3>
                </div>

                <div class="info-grid">
                    <div><span>Country</span><p><?php echo $user['country']; ?></p></div>
                    <div><span>City</span><p><?php echo $user['city']; ?></p></div>
                    <div><span>Locality</span><p><?php echo $user['locality']; ?></p></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        fetch('../pages/get-streak.php')
        .then(res => res.json())
        .then(data => {
            if(data.success){
                const streakEl = document.getElementById('streakDisplay');
                if(streakEl && data.streak !== 0 && data.streak !== null){
                    const dayText = (data.streak == 1) ? 'day' : 'days';
                    streakEl.textContent = `🔥 Streak: ${data.streak} ${dayText}`;
                }
            } else {
                console.error('Failed to fetch streak:', data.error);
            }
        });
    </script>
</body>
</html>