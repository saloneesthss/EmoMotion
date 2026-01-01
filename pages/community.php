<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$posts = $con->prepare("SELECT p.*, u.name, u.image AS user_image FROM community_posts p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC");
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
            <div class="title-tag">
                <div class="hashtag">#all_posts</div>
                <div class="right">
                    <div class="search-tag"><i class="fa-solid fa-magnifying-glass"></i>Search</div>
                    <button class="create-post" onclick="location.href='add-post.php'">Create Post</button>
                </div>
            </div>
        </div>

        <div class="main-part">
            <div class="sidebar">
                <ul>
                    <li class="category-btn" data-category="#fitness"><i class="fa-solid fa-dumbbell"></i>#fitness</li>
                    <li class="category-btn" data-category="#before-after-results"><i class="fa-regular fa-star"></i>#before-after-results</li>
                    <li class="category-btn" data-category="#fitness-journeys"><i class="fa-solid fa-globe"></i>#fitness-journeys</li>
                    <li class="category-btn" data-category="#off-topic"><i class="fa-regular fa-message"></i>#off-topic</li>
                    <li class="category-btn" data-category="#feedback"><i class="fa-regular fa-lightbulb"></i>#feedback</li>
                    <li class="category-btn" data-category="#tech-support"><i class="fa-solid fa-wrench"></i>#tech-support</li>
                </ul>
            </div>

            <div class="main">
                <div id="post-list">
                    <?php while($row = $posts->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="post-card">
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

                        <h3><?php echo $row['title']; ?></h3>
                        <p><?php echo $row['body']; ?></p>
                        <?php if (!empty($row['image'])): ?>
                            <img src="../assets/community-images/<?php echo $row['image']; ?>" style="width:150px;">
                        <?php endif; ?>

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
        </div>
    </div>
    <script>
        document.querySelectorAll('.category-btn').forEach(item => {
            item.addEventListener('click', function () {
                document.querySelectorAll('.category-btn').forEach(li => li.classList.remove('active'));
                this.classList.add('active');
                const category = this.dataset.category;
        
                let text = item.innerText.trim();
                document.querySelector(".hashtag").innerText = text;

                fetch("filter-posts.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "category=" + encodeURIComponent(category)
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById("post-list").innerHTML = data;
                });
            });
        });
    </script>
</body>
</html>