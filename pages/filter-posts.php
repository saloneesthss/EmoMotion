<?php
require_once '../connection.php';

$category = $_POST['category'];

$stmt = $con->prepare("
    SELECT p.*, u.name, u.image AS user_image
    FROM community_posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.category = ?
    ORDER BY p.id DESC
");
$stmt->execute([$category]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
?>
    <div class="post-card">
        <div class="post-header">
            <img class="avatar" src="../assets/users-images/<?php echo $row['user_image']; ?>">

            <span class="username"><?php echo $row['name']; ?></span>
            <span class="time"><?php echo date("M d, Y • h:i A", strtotime($row['created_at'])); ?></span>
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

        <div class='post-actions'>
            <button class='like-btn' data-post-id="<?php echo $row['id']; ?>">
                <i class='fa-regular fa-heart'></i>
                <span class='like-count' id="like-count-<?php echo $row['id']; ?>"></span>
            </button>

            <button class='comment-btn' data-post-id="<?php echo $row['id']; ?>">
                <i class='fa-regular fa-comment'></i>
                <span class='comment-count' id="comment-count-<?php echo $row['id']; ?>"></span>
            </button>
        </div>
    </div>
<?php } ?>
