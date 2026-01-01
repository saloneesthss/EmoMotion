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

        <?php if (!empty($row['image'])): ?>
            <img style="width:150px;" src="../assets/community-images/<?php echo $row['image']; ?>">
        <?php endif; ?>

        <h3><?php echo $row['title']; ?></h3>
        <p><?php echo $row['body']; ?></p>
    </div>
<?php
}
?>
