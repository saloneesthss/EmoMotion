<?php
require_once "../connection.php";

$query = $_GET['query'] ?? '';
$search = "%" . $query . "%";

$videoStmt = $con->prepare("
    SELECT id as video_id, title, 'video' AS type
    FROM workout_videos
    WHERE title LIKE :search
");
$videoStmt->execute(['search' => $search]);
$videos = $videoStmt->fetchAll(PDO::FETCH_ASSOC);

$planStmt = $con->prepare("
    SELECT id as plan_id, plan_name AS title, 'plan' AS type
    FROM workout_plans
    WHERE plan_name LIKE :search
");
$planStmt->execute(['search' => $search]);
$plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);

$results = array_merge($videos, $plans);

if (count($results) === 0) {
    echo "<div class='no-results'>No matches found</div>";
    exit;
}

foreach ($results as $item) {
    $typeLabel = $item['type'] === 'video' ? "🎬 Video" : "📘 Plan";

    echo "
        <a href='" . ($item['type'] === 'video' 
            ? "../pages/workout-videos.php"
            : "../pages/plan-details.php?id=".$item['plan_id']) . "' class='search-result'>
            <div class='result-item'>
                <span class='result-title'>{$item['title']}</span>
                <span class='result-type'>$typeLabel</span>
            </div>
        </a>
    ";
}
?>
