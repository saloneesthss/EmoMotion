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
                    <!-- <div class="search-tag"><i class="fa-solid fa-magnifying-glass"></i>Search</div> -->
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