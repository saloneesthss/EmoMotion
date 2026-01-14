<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$user_id = $_SESSION['user_id'];
$userstmt = $con->prepare("SELECT * FROM users WHERE id = $user_id");
$userstmt->execute();
$user = $userstmt->fetch(PDO::FETCH_ASSOC);

$posts = $con->prepare("SELECT p.*, u.name, u.image AS user_image FROM community_posts p JOIN users u ON p.user_id = u.id WHERE p.user_id=$user_id ORDER BY p.id DESC");
$posts->execute();
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
    <link rel="stylesheet" href="../styles/community.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <a href="user-profile.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" onclick="return confirm('Are you sure to logout?')" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-part">
        <div class="main my-posts">
            <!-- <div class="tag-header">
                <select class="hashtag" name="category" id="category">
                    <option class="category-tag" value="#all-posts">#all-posts</option>
                    <option class="category-tag" value="#fitness">#fitness</option>
                    <option class="category-tag" value="#before-after-results">#before-after-results</option>
                    <option class="category-tag" value="#fitness-journeys">#fitness-journeys</option>
                    <option class="category-tag" value="#off-topic">#off-topic</option>
                    <option class="category-tag" value="#feedback">#feedback</option>
                    <option class="category-tag" value="#tech-support">#tech-support</option>
                </select>
            </div> -->
            <div id="post-list">
                <?php while($row = $posts->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="post-card">
                        <div class="post-header-btn">
                            <div class="post-header">
                                <?php if (!empty($row['user_image'])): ?>
                                    <img class="post-image" src="../assets/users-images/<?php echo $row['user_image']; ?>">
                                <?php endif; ?>

                                <span class="username">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </span>

                                <span class="time">
                                    <?php echo date("M d, Y • h:i A", strtotime($row['created_at'])); ?>
                                </span>
                            </div>
                            <button class="edit-btn" onclick="location.href='edit-post.php?id=<?php echo $row['id'];?>'">
                                Edit
                            </button>
                        </div>

                        <h3><?php echo $row['title']; ?></h3>
                        <p><?php echo $row['body']; ?></p>
                        <?php 
                        $images = json_decode($row['image'], true);
                        if (!empty($images)) {
                            foreach ($images as $image) {
                                echo "<img src='../assets/community-images/$image' style='width:150px; margin:10px;'>";
                            }
                        }
                        ?>

                        <div class="post-actions">
                            <button><i class="fa-regular fa-heart"></i> Like</button>
                            <button><i class="fa-regular fa-comment"></i> Comment</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- <script>
        document.getElementById("category").addEventListener("change", function() {
            this.form.submit();  
        });
    </script> -->
</body>
</html>