<?php
session_start();
require_once '../connection.php';

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$comment = $_POST['comment'];

$stmt = $con->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $user_id, $comment]);

echo "success";
?>
