<?php
session_start();
require_once '../connection.php';

$post_id = intval($_GET['post_id']);
$user_id = $_SESSION['user_id'];

function timeAgo($datetime) {
    date_default_timezone_set('Asia/Kathmandu');
    $time = strtotime($datetime);
    if ($time === false) return ""; 
    $diff = time() - $time;

    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . "m";
    if ($diff < 86400) return floor($diff / 3600) . "h";
    if ($diff < 604800) return floor($diff / 86400) . "d";
    if ($diff < 2592000) return floor($diff / 604800) . "w";
    if ($diff < 31536000) return floor($diff / 2592000) . "mo";
    return floor($diff / 31536000) . "y";
}

$likes = $con->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id=?");
$likes->execute([$post_id]);
$like_count = $likes->fetchColumn();

$stmt = $con->prepare("
    SELECT c.*, u.name, u.image
    FROM post_comments c 
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id=?
    ORDER BY c.id ASC
");
$stmt->execute([$post_id]);

$comments = [];
$comment_count = 0;
while ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $comments[] = [
        "name" => $c['name'],
        "image" => $c['image'],
        "comment" => $c['comment'],
        "time" => timeAgo($c['created_at'])
    ];
    $comment_count++;
}

$check = $con->prepare("SELECT * FROM post_likes WHERE post_id=? AND user_id=?");
$check->execute([$post_id, $user_id]);
$liked_by_user = $check->rowCount() > 0;

echo json_encode([
    "likes" => $like_count,
    "comments" => $comments,
    "comment_count" => $comment_count,
    "liked_by_user" => $liked_by_user
]);
?>
