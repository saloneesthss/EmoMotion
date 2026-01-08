<?php
session_start();
header('Content-Type: application/json');

require_once "../connection.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "not_logged_in"]);
    exit;
}

$userId = $_SESSION['user_id'];

// Validate POST data
if (!isset($_POST['id']) || !isset($_POST['isPlan'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$id = (int)$_POST['id'];
$isPlan = $_POST['isPlan'];  // "1" for plan, "0" for video

// Determine proper column values
$videoId = ($isPlan == "0") ? $id : NULL;
$planId  = ($isPlan == "1") ? $id : NULL;

// FIRST: Check if already favorited
$stmt = $con->prepare("
    SELECT * FROM favorites 
    WHERE user_id = :user_id
    AND (
        (video_id = :video_id AND :video_id IS NOT NULL)
        OR
        (plan_id = :plan_id AND :plan_id IS NOT NULL)
    )
");

$stmt->execute([
    ':user_id'  => $userId,
    ':video_id' => $videoId,
    ':plan_id'  => $planId
]);

$exists = $stmt->fetch(PDO::FETCH_ASSOC);

// If exists → remove it
if ($exists) {
    $delete = $con->prepare("
        DELETE FROM favorites 
        WHERE id = :id
    ");

    $delete->execute([':id' => $exists['id']]);

    echo json_encode(["status" => "removed"]);
    exit;
}

// If not exists → insert new favorite
$insert = $con->prepare("
    INSERT INTO favorites (user_id, video_id, plan_id)
    VALUES (:user_id, :video_id, :plan_id)
");

try {
    $insert->execute([
        ':user_id'  => $userId,
        ':video_id' => $videoId,
        ':plan_id'  => $planId
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}

echo json_encode(["status" => "added"]);
exit;
?>
