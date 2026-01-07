<?php
require_once "logincheck.php";

if (!isset($_GET['id'])) {
    header("Location: plans-list.php?error=No plan found with the given ID.");
    die;
}

$id=(int) $_GET['id'];

$sql="delete from workout_plans where id=$id";
$stmt=$con->prepare($sql);
$stmt->execute();

header("Location:plans-list.php?success=Selected plan is deleted successfully.");
die;
?>