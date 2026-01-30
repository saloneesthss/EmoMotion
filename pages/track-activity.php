<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);

require_once "../connection.php";

if (!$con) {
    echo json_encode(['success'=>false, 'error'=>'DB connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['user_id'], $data['plan_id'])) {
    echo json_encode(['success'=>false, 'error'=>'Invalid input']);
    exit;
}

$user_id = (int)$data['user_id'];
$plan_id = (int)$data['plan_id'];
$today = date('Y-m-d');

try {
    $stmt = $con->prepare("SELECT * FROM user_activity WHERE user_id=:uid AND activity_date=:today");
    $stmt->execute([':uid'=>$user_id, ':today'=>$today]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activity) {
        $plans = json_decode($activity['plan_id'], true);
        if (!is_array($plans)) $plans = [];

        if (!in_array($plan_id, $plans)) {
            $plans[] = $plan_id;
            $update = $con->prepare("UPDATE user_activity SET plan_id=:plans WHERE id=:id");
            $update->execute([
                ':plans' => json_encode($plans),
                ':id' => $activity['id']
            ]);

        }

        $checkHistory = $con->prepare("
            SELECT id FROM user_history
            WHERE user_id = :uid AND activity_id = :act_id
            LIMIT 1
        ");
        $checkHistory->execute([
            ':uid' => $user_id,
            ':act_id' => $activity['id']
        ]);
        $existingHistory = $checkHistory->fetch(PDO::FETCH_ASSOC);
       
        if ($existingHistory) {
            $updateHistory = $con->prepare("
                UPDATE user_history 
                SET plan_id = :plans, clicked_at = :created 
                WHERE id = :id
            ");
            $updateHistory->execute([
                ':plans' => json_encode($plans),
                ':created' => date('Y-m-d H:i:s'),
                ':id' => $existingHistory['id']
            ]);
        } else {
            $insertHistory = $con->prepare("
                INSERT INTO user_history (user_id, activity_id, plan_id, video_id, clicked_at)
                VALUES (:uid, :act_id, :plan_id, NULL, :created)
            ");
            $insertHistory->execute([
                ':uid' => $user_id,
                ':act_id' => $activity['id'],
                ':plan_id' => json_encode($plans), 
                ':created' => date('Y-m-d H:i:s')
            ]);
        }
    } else {
        $plans = [$plan_id];
        $insert = $con->prepare("
            INSERT INTO user_activity (user_id, plan_id, activity_date)
            VALUES (:uid, :plans, :today)
        ");
        $insert->execute([
            ':uid'=>$user_id,
            ':plans'=>json_encode($plans),
            ':today'=>$today
        ]);

        $newActivityId = $con->lastInsertId();
        $history = $con->prepare("
            INSERT INTO user_history (user_id, plan_id, activity_id)
            VALUES (:uid, :plans, :act_id)
        ");
        $history->execute([
            ':uid' => $user_id,
            ':plans'=>json_encode($plans),
            ':act_id' => $newActivityId,
        ]);
    }

    $stmt = $con->prepare("
        SELECT activity_date 
        FROM user_activity 
        WHERE user_id=:uid 
        ORDER BY activity_date DESC
    ");
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

    echo json_encode([
        'success' => true,
        'streak' => $streak,
        'todayPlans' => $plans
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success'=>false,
        'error'=>$e->getMessage()
    ]);
}
