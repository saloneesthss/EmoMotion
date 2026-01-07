<?php
require_once "logincheck.php";

if (!isset($_GET['id'])) {
    header("Location: videos-list.php?error=No video found with the given ID.");
    die;
}

$id=(int) $_GET['id'];

$sql="delete from workout_videos where id=$id";
$stmt=$con->prepare($sql);
$stmt->execute();

header("Location:videos-list.php?success=Selected video is deleted successfully.");
die;