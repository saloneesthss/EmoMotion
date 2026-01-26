<?php
session_start();
require_once '../connection.php';

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

$check = $con->prepare("SELECT * FROM post_likes WHERE post_id=? AND user_id=?");
$check->execute([$post_id, $user_id]);

if ($check->rowCount() > 0) {
    $con->prepare("DELETE FROM post_likes WHERE post_id=? AND user_id=?")->execute([$post_id, $user_id]);
    $liked = false;
} else {
    $con->prepare("INSERT INTO post_likes (post_id,user_id) VALUES (?,?)")->execute([$post_id, $user_id]);
    $liked = true;
}

$count = $con->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id=?");
$count->execute([$post_id]);
$like_count = $count->fetchColumn();

echo json_encode(["liked" => $liked, "like_count" => $like_count]);
?>
