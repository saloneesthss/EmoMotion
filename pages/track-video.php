<?php
session_start();
header('Content-Type: application/json');
require_once "../connection.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['video_id'])) {
    echo json_encode(['success'=>false, 'error'=>'video_id missing']);
    exit;
}

$user_id = $_SESSION['user_id'];
$video_id = (int)$data['video_id'];

try {
    $stmt = $con->prepare("
        SELECT id, video_id FROM user_history
        WHERE user_id=:uid AND video_id IS NOT NULL
        ORDER BY clicked_at desc
        LIMIT 1
    ");
    $stmt->execute([':uid'=>$user_id]);
    $history = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($history) {
        $videos = json_decode($history['video_id'], true);
        if (!is_array($videos)) $videos = [];

        if (!in_array($video_id, $videos)) {
            $videos[] = $video_id;

            $update = $con->prepare("
                UPDATE user_history
                SET video_id=:vids, clicked_at=NOW()
                WHERE id=:id
            ");
            $update->execute([
                ':vids' => json_encode($videos),
                ':id' => $history['id']
            ]);
        } else {
            $update = $con->prepare("
                UPDATE user_history
                SET clicked_at=NOW()
                WHERE id=:id
            ");
            $update->execute([':id' => $history['id']]);
        }
    } else {
        $videos = [$video_id];
        $insert = $con->prepare("
            INSERT INTO user_history (user_id, plan_id, video_id, clicked_at)
            VALUES (:uid, NULL, :vids, NOW())
        ");
        $insert->execute([
            ':uid' => $user_id,
            ':vids' => json_encode($videos)
        ]);
    }

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
