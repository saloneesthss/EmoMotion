<?php
session_start();
header('Content-Type: application/json');
require_once "../connection.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$from = $data['from'] ?? null;
$to = $data['to'] ?? null;

try {
    $query = "
        SELECT video_id, clicked_at
        FROM user_history
        WHERE user_id = :uid
        AND video_id IS NOT NULL
    ";
    $params = [':uid' => $user_id];

    if ($from) {
        $query .= " AND clicked_at >= :from";
        $params[':from'] = $from . " 00:00:00";
    }

    if ($to) {
        $query .= " AND clicked_at <= :to";
        $params[':to'] = $to . " 23:59:59";
    }

    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $videos = [];
    foreach ($rows as $row) {
        $videoIds = json_decode($row['video_id'], true);
        if (!is_array($videoIds)) {
            if ($videoIds !== null) $videoIds = [$videoIds];
            else $videoIds = [];
        }

        if (count($videoIds) > 0) {
            $in = str_repeat('?,', count($videoIds) - 1) . '?';
            $vStmt = $con->prepare("SELECT * FROM workout_videos WHERE id IN ($in)");
            $vStmt->execute($videoIds);
            $videos = array_merge($videos, $vStmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }
    echo json_encode(['success'=>true, 'videos'=>$videos]);

} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
