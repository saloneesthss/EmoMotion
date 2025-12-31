<?php
require_once '../components/user-navbar.php';
require_once '../connection.php';
$posts = $con->prepare("SELECT * FROM community_posts ORDER BY id DESC");
$posts->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/community.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class='container'>
        <div class="community-header">
            <h2>Community</h2>
            <div class="title">
                <div class="hashtag">#fitness</div>
                <div class="right">
                    <div class="search-tag"><i class="fa-solid fa-magnifying-glass"></i>Search</div>
                    <button class="create-post" onclick="location.href='add-post.php'">Create Post</button>
                </div>
            </div>
        </div>

        <div class="main-part">
            <div class="sidebar">
                <ul>
                    <li class="active"><i class="fa-solid fa-dumbbell"></i>#fitness</li>
                    <li><i class="fa-regular fa-star"></i>#before-after-results</li>
                    <li><i class="fa-solid fa-globe"></i>#fitness-journeys</li>
                    <li><i class="fa-regular fa-message"></i>#off-topic</li>
                    <li><i class="fa-regular fa-lightbulb"></i>#feedback</li>
                    <li><i class="fa-solid fa-wrench"></i>#tech-support</li>
                </ul>
            </div>

            <div class="main">
                <div id="post-list">
                    <?php while($row = $posts->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <img src="avatar.png" class="avatar">
                            <span class="username"><?php echo $row['username']; ?></span>
                            <span class="time"><?php echo $row['created_at']; ?></span>
                        </div>

                        <h3><?php echo $row['title']; ?></h3>
                        <p><?php echo $row['body']; ?></p>

                        <div class="post-actions">
                            <button>💬 Reply</button>
                            <button>⬆ 13</button>
                            <button>💾 Save</button>
                            <button>↗ Share</button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div id="popup" class="popup">
                <form class="popup-content" method="POST" action="submit_post.php">
                    <h2>Create New Post</h2>
                    <input type="text" name="username" placeholder="Your name" required>
                    <input type="text" name="title" placeholder="Post title" required>
                    <textarea name="body" placeholder="Write your post..." required></textarea>
                    <select name="category">
                        <option value="#fitness">#fitness</option>
                        <option value="#brfore-after-results">#before-after-results</option>
                        <option value="#fitness-journeys">#fitness-journeys</option>
                        <option value="#off-topic">#off-topic</option>
                        <option value="#feedback">#feedback</option>
                        <option value="#tech-support">#tech-support</option>
                    </select>

                    <div class="popup-buttons">
                        <button type="button" onclick="closePostPopup()">Cancel</button>
                        <button type="submit">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>