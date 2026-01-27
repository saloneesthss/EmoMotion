<?php
session_start();
require_once "../connection.php";

$user_id = $_SESSION['user_id'] ?? 0;
$query = $_GET['query'] ?? "";
$query = trim($query);

if ($query === "") {
    echo "<p style='padding:20px;'>Start typing to search plans...</p>";
    exit;
}

$sql1 = $con->prepare("
    SELECT * FROM workout_plans 
    WHERE plan_name LIKE :query
");
$sql1->execute([':query' => "%$query%"]);
$workoutPlans = $sql1->fetchAll(PDO::FETCH_ASSOC);

$sql2 = $con->prepare("
    SELECT * FROM customized_plans 
    WHERE plan_name LIKE :query AND user_id = :uid
");
$sql2->execute([
    ':query' => "%$query%",
    ':uid'   => $user_id
]);
$customPlans = $sql2->fetchAll(PDO::FETCH_ASSOC);

if (empty($workoutPlans) && empty($customPlans)) {
    echo "<p style='padding:20px;'>No plans found.</p>";
    exit;
}

foreach ($workoutPlans as $p) {
    echo "
        <div class='search-result-item result-item' onclick=\"location.href='plan-details.php?id={$p['id']}'\">
            <span class='result-name'>{$p['plan_name']}</span>
            <span class='result-type'>{$p['target_area']}</span>
        </div>
    ";
}

foreach ($customPlans as $p) {
    echo "
        <div class='search-result-item result-item' onclick=\"location.href='customized-details.php?id={$p['id']}'\">
            <span class='result-name'>{$p['plan_name']}</span>
            <span class='result-type'>{$p['target_area']}</span>
        </div>
    ";
}
?>
