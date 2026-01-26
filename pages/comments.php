<?php
session_start();
require_once '../connection.php';

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

$post_id = intval($_GET['post_id']);
$stmt = $con->prepare("
    SELECT c.*, u.name, u.image AS user_image
    FROM post_comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id=?
    ORDER BY c.id DESC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$comments){
    echo "<p style='margin:5px;'><strong>No comments yet.</strong><br>Be the first to comment.</p>";
    exit;
}

foreach($comments as $c){
    echo "
    <div class='comment-item'>
        <img src='../assets/users-images/{$c['user_image']}' class='comment-avatar'>
        <div class='comment-content'>
            <div class='comment-top'>
                <strong>{$c['name']}</strong>
                <span class='comment-time'>".timeAgo($c['created_at'])."</span>
            </div>
            <div class='comment-text'>{$c['comment']}</div>
        </div>
    </div>";
}
?>
