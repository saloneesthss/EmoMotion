<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$user_id = $_SESSION['user_id'];
$id=(int) $_GET['id'];

if (!isset($_GET['id'])) {
    header("Location:my-posts.php?error=Please provide a valid ID for the post.");
    die;
}

$sql="select * from community_posts where id=$id";
$stmt=$con->prepare($sql);
$stmt->execute();
$posts=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$posts) {
    header("Location:my-posts.php?error=No post found with the given ID.");
    die;
}

$images = json_decode($posts['image'], true); 
if (!is_array($images)) {
    $images = [];
}

if($_SERVER['REQUEST_METHOD']==='POST') {
    //handle login submit
    $title=$_POST['title'];
    $body=$_POST['body'];
    $category=$_POST['category'];

    $oldImages = json_decode($_POST['image'], true);
    if (!is_array($oldImages)) $oldImages = [];

    $newImages = [];

    if (!empty($_FILES['image_name']['name'][0])) {
        foreach ($_FILES['image_name']['tmp_name'] as $index => $tmpName) {
            if (is_uploaded_file($tmpName)) {
                $newName = $_FILES['image_name']['name'][$index];
                move_uploaded_file($tmpName, "../assets/community-images/" . $newName);
                $newImages[] = $newName;
            }
        }
        foreach ($oldImages as $oldImg) {
            $path = '../assets/community-images/' . $oldImg;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $allImages = $newImages;
    } else {
        $allImages = $oldImages;
    }

    $finalImagesJson = json_encode($allImages);
        
    $sql="update community_posts set title='$title', body='$body', image='$finalImagesJson', category='$category', created_at=NOW() where id=$id";
    $empStmt=$con->prepare($sql);
    $empStmt->execute();

    header("Location:my-posts.php?success=Post updated successfully.");
    die;
}
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
    <link rel="stylesheet" href="../styles/add-post.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <a href="user-profile.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
    <div class="container">
        <div class="title">Edit Post</div>
            <form method="POST" enctype="multipart/form-data">
                <label class="form-label">Post title</label>
                <input type="text" class="input-box" value="<?php echo $posts['title'] ?>" name="title" required />

                <label class="form-label">Post details</label>
                <textarea class="textarea-box" maxlength="3000" name="body" required><?php echo $posts['body'] ?></textarea>

                <div class="char-count"><?php echo strlen($posts['body']); ?>/3000 characters</div>

                <div class="attach-row">
                    <label>Images</label>

                    <input type="file" accept=".webp, .jpg, .jpeg, .png" 
                        name="image_name[]" multiple id="imageInput" />

                    <input type="hidden" name="old_images" value='<?php echo $posts['images']; ?>'>

                    <div class="old-images" style="margin-top: 10px;">
                        <?php foreach ($images as $img) { 
                            if (file_exists('../assets/community-images/' . $img)) { ?>
                                <img width="100" 
                                    style="margin-right:10px; border-radius:6px;" 
                                    src="../assets/community-images/<?php echo $img; ?>">
                        <?php } } ?>
                    </div>

                    <div id="newPreview" style="margin-top: 10px;"></div>
                </div>

                <label class="form-label">Select a Tag</label>
                <select name="category" id="category" class="dropdown-box" required>
                    <?php 
                    $tags = ["#fitness", "#before-after-results", "#fitness-journeys", "#off-topic", "#feedback", "#tech-support"];
                    foreach ($tags as $tag) { ?>
                        <option value="<?php echo $tag; ?>" 
                            <?php echo ($tag == $posts['category']) ? 'selected' : ''; ?>>
                            <?php echo $tag; ?>
                        </option>
                    <?php } ?>
                </select>

                <div class="btn-row">
                    <button type="reset" class="btn cancel">Cancel</button>
                    <button type="submit" class="btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const textarea = document.querySelector('.textarea-box');
        const counter = document.querySelector('.char-count');

        function updateCount() {
            counter.textContent = textarea.value.length + "/3000 characters";
        }
        updateCount();
        textarea.addEventListener("input", updateCount);

        const input = document.getElementById("imageInput");
        input.addEventListener("change", function () {
            const previewBox = document.getElementById("newPreview");
            previewBox.innerHTML = ""; // Clear old previews

            [...this.files].forEach(file => {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.width = 100;
                    img.style.marginRight = "10px";
                    previewBox.appendChild(img);
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>