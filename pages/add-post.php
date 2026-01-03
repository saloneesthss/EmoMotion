<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';
    
$uploadPath = '../assets/community-images';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $body = $_POST['body'];
    $category = $_POST['category'];
    // $created_at = $_POST['created_at'];

    $imageName = null;
    if(is_uploaded_file($_FILES['image']['tmp_name'])) {
        $imageName = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . "/" . $imageName);
    }
    $sql = "insert into community_posts set user_id='$user_id', title='$title', body='$body', image='$imageName', category='$category', created_at=NOW()";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    header("Location:community.php?success=Post added successfully.");
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/add-post.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class="create-post-container">
        <div class="community-header">
            <h2>Community</h2>
            <div class="title">
                <div class="hashtag">#fitness</div>
            </div>
        </div>
        <div class="sidebar">
            <ul>
                <li><i class="fa-solid fa-dumbbell"></i>#fitness</li>
                <li><i class="fa-regular fa-star"></i>#before-after-results</li>
                <li><i class="fa-solid fa-globe"></i>#fitness-journeys</li>
                <li><i class="fa-regular fa-message"></i>#off-topic</li>
                <li><i class="fa-regular fa-lightbulb"></i>#feedback</li>
                <li><i class="fa-solid fa-wrench"></i>#tech-support</li>
            </ul>
        </div>

        <div class="main">    
            <div class="create-post-header">
                <a href="community.php" class="back-btn">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="message" style="color:green;">
                <?php if(isset($_GET['success'])) { ?>
                    <?php echo $_GET['success']; ?>
                <?php }?>
            </div>

            <div class="create-post-card">
                <div class="card-header">
                    <h2>Create A New Post</h2>
                    <button class="close-btn" onclick="location.href='community.php'">✕</button>
                </div>

                <form action="" method="post" enctype="multipart/form-data">
                    <label class="form-label">Post title</label>
                    <input required type="text" name="title" class="input-box" placeholder="Write a meaningful post title">

                    <label class="form-label">Post details</label>
                    <textarea required class="textarea-box" maxlength="3000" name="body" id="body" placeholder="Write a detailed post"></textarea>

                    <div class="char-count">0/3000 characters</div>

                    <div class="attach-row">
                        <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png, .webp" style="display: none;">
                        <label for="image" class="attach-link">+ Attach Images</label>
                    </div>

                    <label class="form-label">Select A Tag</label>
                    <select class="dropdown-box" name="category" required>
                        <option>Select a tag</option>
                        <option value="#fitness">#fitness</option>
                        <option value="#brfore-after-results">#before-after-results</option>
                        <option value="#fitness-journeys">#fitness-journeys</option>
                        <option value="#off-topic">#off-topic</option>
                        <option value="#feedback">#feedback</option>
                        <option value="#tech-support">#tech-support</option>
                    </select>

                    <div class="footer-actions">
                        <button type="reset" class="cancel-btn">Cancel</button>
                        <button type="submit" class="create-btn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const bodyField = document.querySelector("#body");
        const charCount = document.querySelector(".char-count");

        bodyField.addEventListener("input", () => {
            let currentLength = bodyField.value.length;
            if (currentLength > 3000) {
                bodyField.value = bodyField.value.substring(0, 3000);
                currentLength = 3000;
            }
            charCount.textContent = `${currentLength}/3000 characters`;
        });
    </script>
</body>
</html>