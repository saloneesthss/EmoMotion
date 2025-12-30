<?php
require_once '../components/navbar.php';
require_once '../connection.php';
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
                <li class="active"><i class="fa-solid fa-dumbbell"></i>#fitness</li>
                <li><i class="fa-regular fa-star"></i>#before-after-results</li>
                <li><i class="fa-solid fa-globe"></i>#fitness-journeys</li>
                <li><i class="fa-regular fa-message"></i>#off-topic</li>
                <li><i class="fa-regular fa-lightbulb"></i>#feedback</li>
                <li><i class="fa-solid fa-wrench"></i>#tech-support</li>
            </ul>
        </div>

        <div class="main">    
            <div class="create-post-header">
                <a href="#" class="back-btn">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="create-post-card">
                <div class="card-header">
                    <h2>Create A New Post</h2>
                    <button class="close-btn">✕</button>
                </div>

                <form>
                    <label class="form-label">Post title</label>
                    <input type="text" class="input-box" placeholder="Write a meaningful post title">

                    <label class="form-label">Post details</label>
                    <textarea class="textarea-box" placeholder="Write a detailed post"></textarea>

                    <div class="char-count">0/3000 characters</div>

                    <div class="attach-row">
                        <a href="#" class="attach-link">+ Attach Images</a>
                    </div>

                    <label class="form-label">Select A Tag</label>
                    <select class="dropdown-box">
                        <option>Select a tag</option>
                        <option>#fitness</option>
                        <option>#food</option>
                        <option>#before-after-results</option>
                    </select>

                    <div class="footer-actions">
                        <button type="button" class="cancel-btn">Cancel</button>
                        <button type="submit" class="create-btn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>