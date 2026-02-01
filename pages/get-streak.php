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
    $stmt = $con->prepare("SELECT activity_date FROM user_activity WHERE user_id=:uid ORDER BY activity_date DESC");
    $stmt->execute([':uid'=>$user_id]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($dates) === 0) {
        echo json_encode([
            'success' => true,
            'streak' => 0,
            'streak_broken' => true
        ]);
        exit;
    }
    $today = date("Y-m-d");
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $latest = $dates[0];
    if ($latest !== $today && $latest !== $yesterday) {
        echo json_encode([
            'success' => true,
            'streak' => 0,
            'streak_broken' => true
        ]);
        exit;
    }

    $streak = 1;
    $prevDate = $latest;

    for ($i = 1; $i < count($dates); $i++) {
        $current = $dates[$i];

        $diff = (strtotime($prevDate) - strtotime($current)) / 86400;

        if ($diff === 1) {
            $streak++;
            $prevDate = $current;
        } else {
            break;
        }
    }

    echo json_encode(['success'=>true, 'streak'=>$streak, 'streak_broken' => false]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
