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
            <a href="my-history.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-clock-rotate-left"></i> My History</a>
            <a href="my-plans.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" onclick="return confirm('Are you sure to logout?')" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-part">
        <div class="main my-posts">
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
                            <button class="like-btn" 
                                    data-post-id="<?php echo $row['id']; ?>">
                                <i class="fa-regular fa-heart"></i>
                                <span class="like-count" id="like-count-<?php echo $row['id']; ?>">0</span>
                            </button>

                            <button class="comment-btn" 
                                    data-post-id="<?php echo $row['id']; ?>">
                                <i class="fa-regular fa-comment"></i>
                                <span class="comment-count" id="comment-count-<?php echo $row['id']; ?>">0</span>
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div id="commentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>

                <div id="commentSection"></div>

                <div class="comment-input-box">
                    <input type="text" id="newComment" placeholder="Add a comment...">
                    <button id="postComment">Post</button>
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
                    refreshReactions();
                });
            });
        });

        function refreshReactions() {
            document.querySelectorAll(".like-btn").forEach(btn => {
                let postId = btn.dataset.postId;

                fetch("load-reactions.php?post_id=" + postId)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("like-count-" + postId).innerText = data.likes;
                    document.getElementById("comment-count-" + postId).innerText = data.comment_count;

                    if (data.liked_by_user) {
                        btn.querySelector("i").classList.remove("fa-regular");
                        btn.querySelector("i").classList.add("fa-solid");
                        btn.querySelector("i").style.color = "red";
                    }
                });
            });
        }

        window.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".like-btn").forEach(btn => {
                let postId = btn.dataset.postId;
                fetch("load-reactions.php?post_id=" + postId)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("like-count-" + postId).innerText = data.likes;
                    document.getElementById("comment-count-" + postId).innerText = data.comment_count;

                    if (data.liked_by_user) {
                        btn.querySelector("i").classList.remove("fa-regular");
                        btn.querySelector("i").classList.add("fa-solid");
                        btn.querySelector("i").style.color = "red";
                    }
                });
            });
        });

        document.addEventListener("click", function(e) {
            if (e.target.closest(".like-btn")) {
                let btn = e.target.closest(".like-btn");
                let postId = btn.dataset.postId;

                fetch("toggle-like.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "post_id=" + postId
                })
                .then(res => res.json())
                .then(data => {
                    let heart = btn.querySelector("i");
                    let count = document.getElementById("like-count-" + postId);
                    count.innerText = data.like_count;

                    if (data.liked) {
                        heart.classList.remove("fa-regular");
                        heart.classList.add("fa-solid");
                        heart.style.color = "red";
                    } else {
                        heart.classList.remove("fa-solid");
                        heart.classList.add("fa-regular");
                        heart.style.color = "black";
                    }
                });
            }
        });

        let activePostId = null;
        document.addEventListener("click", function(e){
            if(e.target.closest(".comment-btn")){
                let btn = e.target.closest(".comment-btn");
                activePostId = btn.dataset.postId;
                document.getElementById("commentModal").style.display = "flex";
                loadComments(activePostId);
            }
        });

        document.getElementById("closeModal").onclick = function(){
            document.getElementById("commentModal").style.display = "none";
        };

        function loadComments(postId){
            fetch("comments.php?post_id=" + postId)
            .then(res => res.text())
            .then(data => {
                document.getElementById("commentSection").innerHTML = data;
            });
        }

        document.getElementById("postComment").onclick = function(){
            let comment = document.getElementById("newComment").value.trim();

            if(comment === "") return;

            fetch("add-comment.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "post_id=" + activePostId + "&comment=" + encodeURIComponent(comment)
            })
            .then(res => res.text())
            .then(data => {
                document.getElementById("newComment").value = "";
                loadComments(activePostId);
                refreshReactions(); 
            });
        };
    </script>
</body>
</html>