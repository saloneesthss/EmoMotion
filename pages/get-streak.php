<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);

require_once "../connection.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'error'=>'User not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    // Fetch all activity dates
    $stmt = $con->prepare("SELECT activity_date FROM user_activity WHERE user_id=:uid ORDER BY activity_date DESC");
    $stmt->execute([':uid'=>$user_id]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $streak = 0;
    $prevDate = null;

    foreach ($dates as $date) {
        if (!$prevDate) {
            $streak = 1;
        } else {
            $diff = (strtotime($prevDate) - strtotime($date)) / 86400;
            if ($diff === 1) {
                $streak++;
            } else {
                break;
            }
        }
        $prevDate = $date;
    }

    echo json_encode(['success'=>true, 'streak'=>$streak]);

} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
