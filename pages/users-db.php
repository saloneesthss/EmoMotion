<?php
require_once '../logincheck.php';
require_once '../components/user-navbar.php';

$user_id = $_SESSION['user_id'];

$favStmt = $con->prepare("
    SELECT p.*
    FROM favorites f
    JOIN workout_plans p ON f.plan_id = p.id
    WHERE f.user_id = ?
");
$favStmt->execute([$user_id]);
$favorites = $favStmt->fetchAll(PDO::FETCH_ASSOC);

$planStmt = $con->prepare("select * from workout_plans");
$planStmt->execute();
$plansChallenge = $planStmt->fetchAll(PDO::FETCH_ASSOC);

$bmiStmt = $con->prepare("SELECT bmi from users where id=?");
$bmiStmt -> execute([$user_id]);
$bmi = $bmiStmt->fetch(PDO::FETCH_ASSOC);

if (count($favorites) === 0) {
    $sql = "SELECT * FROM workout_plans ORDER BY RAND() LIMIT 12";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($plans as &$p) {
        $p['score'] = bmiScore($p, $bmi);
    }
    usort($plans, fn($a, $b) => $b['score'] <=> $a['score']);
    $recommended = array_slice($plans, 0, 12);
} else {
    $stmt = $con->prepare("
        SELECT *
        FROM workout_plans
        WHERE id NOT IN (SELECT plan_id FROM favorites WHERE user_id = ?)
    ");
    $stmt->execute([$user_id]);
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fitness_map = ["Beginner"=>1, "Intermediate"=>2, "Advanced"=>3];
    $intensity_map = ["Low"=>1, "Medium"=>2, "High"=>3];

    $unique_targets = [];
    $unique_moods = [];

    foreach ($plans as $p) {
        if (!in_array($p['target_area'], $unique_targets)) $unique_targets[] = $p['target_area'];
        if (!in_array($p['mood'], $unique_moods)) $unique_moods[] = $p['mood'];
    }
    $user_vector_sum = [];
    $favorite_vectors = [];

    foreach ($favorites as $fav) {
        $vec = [];
        $vec[] = $fitness_map[$fav['fitness_level']] ?? 1;

        foreach ($unique_targets as $t) {
            $vec[] = ($fav['target_area'] === $t) ? 1 : 0;
        }

        foreach ($unique_moods as $m) {
            $vec[] = ($fav['mood'] === $m) ? 1 : 0;
        }

        $vec[] = $intensity_map[$fav['intensity']] ?? 1;
        $favorite_vectors[] = $vec;
    }
    $vector_length = count($favorite_vectors[0]);
    $user_profile = array_fill(0, $vector_length, 0);

    foreach ($favorite_vectors as $vec) {
        foreach ($vec as $i => $v) {
            $user_profile[$i] += $v;
        }
    }

    foreach ($user_profile as $i => $value) {
        $user_profile[$i] = $value / count($favorite_vectors);
    }

    $recommended = [];
    foreach ($plans as $p) {
        $vec = [];
        $vec[] = $fitness_map[$p['fitness_level']] ?? 1;

        foreach ($unique_targets as $t) {
            $vec[] = ($p['target_area'] === $t) ? 1 : 0;
        }

        foreach ($unique_moods as $m) {
            $vec[] = ($p['mood'] === $m) ? 1 : 0;
        }

        $vec[] = $intensity_map[$p['intensity']] ?? 1;
        $dot = 0; $magA = 0; $magB = 0;
        foreach ($vec as $i => $v) {
            $dot += $v * $user_profile[$i];
            $magA += $v * $v;
            $magB += $user_profile[$i] * $user_profile[$i];
        }
        $magA = sqrt($magA);
        $magB = sqrt($magB);
        $similarity = ($magA > 0 && $magB > 0) ? ($dot / ($magA * $magB)) : 0;

        $bmi = bmiScore($p, $bmi);
        $p['score'] = $similarity * 0.7 + $bmi * 0.3;
        $recommended[] = $p;
    }

    usort($recommended, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    $recommended = array_slice($recommended, 0, 8);
}

function bmiScore($plan, $bmi) {
    $score = 0;
    if ($bmi < 18.5) {
        // Underweight → Strength + Low Impact
        if ($plan['intensity'] == 'Low') $score += 3;
        if (in_array($plan['target_area'], ['Arms', 'Legs', 'Full Body'])) $score += 2;
        if ($plan['fitness_level'] == 'Beginner') $score += 3;
    } 
    elseif ($bmi < 25) {
        // Normal → Balanced
        if ($plan['intensity'] == 'Medium') $score += 3;
        if ($plan['fitness_level'] == 'Intermediate') $score += 2;
        $score += 1; // small bias to all
    } 
    elseif ($bmi < 30) {
        // Overweight → Fat Burn
        if ($plan['intensity'] == 'Medium' || $plan['intensity'] == 'High') $score += 3;
        if ($plan['target_area'] == 'Full Body') $score += 2;
        if ($plan['mood'] == 'Energetic') $score += 1;
    } 
    else {
        // Obese → Low Impact + Mobility
        if ($plan['intensity'] == 'Low') $score += 5;
        if (in_array($plan['target_area'], ['Legs', 'Back', 'Mobility'])) $score += 3;
        if ($plan['fitness_level'] == 'Beginner') $score += 3;
    }
    return $score;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/plan-details.css">
    <link rel="stylesheet" href="../styles/users-db.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class="dashboard">
        <div class="top-actions">
            <div class="search-box">
                <input type="text" id="search-hint" placeholder="Search for plans...">
            </div>
            <a href="#recommended"><button class="filter-btn">Recommended</button></a>
            <a href="#created-by-me"><button class="filter-btn">Created by me</button></a>
            <a href="#challenges"><button class="filter-btn">Challenges</button></a>
            <a href="#full-body"><button class="filter-btn">Full Body</button></a>

            <button class="create-workout">+ Create your own plan</button>
        </div>

        <h2 class="section-title top-title" id="recommended">Recommended for you</h2>
        <div class="plans-grid">
            <?php foreach ($recommended as $video): ?>
            <a href="plan-details.php?id=<?= $video['id'] ?>" class="plan-link">
                <div class="plans-card">
                    <img class="exercise-image" src="../assets/plans-thumbnail/<?php echo $video['file_path']; ?>" alt="Workout Plan">
                    <button class="play-btn" onclick="openWorkoutDialog(event)">▶</button>
                    <h3><?php echo htmlspecialchars($video['plan_name']); ?></h3>
                    <?php
                        $seconds = $video['time_duration'];
                        $minutes = floor($seconds / 60);
                        $remainingSeconds = $seconds % 60;
                        $readable = sprintf("%d:%02d", $minutes, $remainingSeconds);
                    ?>     
                    <p><?= $readable ?> • <?php echo $video['fitness_level']; ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title" id="created-by-me">Customized by you</h2>
        <div class="plans-grid">
            <?php foreach ($recommended as $video): ?>
            <a href="plan-details.php?id=<?= $video['id'] ?>" class="plan-link">
                <div class="plans-card">
                    <img class="exercise-image" src="../assets/plans-thumbnail/<?php echo $video['file_path']; ?>" alt="Workout Plan">
                    <button class="play-btn" onclick="openWorkoutDialog(event)">▶</button>
                    <h3><?php echo htmlspecialchars($video['plan_name']); ?></h3>
                    <?php
                        $seconds = $video['time_duration'];
                        $minutes = floor($seconds / 60);
                        $remainingSeconds = $seconds % 60;
                        $readable = sprintf("%d:%02d", $minutes, $remainingSeconds);
                    ?>     
                    <p><?= $readable ?> • <?php echo $video['fitness_level']; ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title" id="challenges">Workout Challenges</h2>
        <div class="plans-grid">
            <?php foreach ($plansChallenge as $video): ?>
            <?php if (stripos($video['plan_name'], 'Challenge') !== false): ?>
            <a href="plan-details.php?id=<?= $video['id'] ?>" class="plan-link">
                <div class="plans-card">
                    <img class="exercise-image" src="../assets/plans-thumbnail/<?php echo $video['file_path']; ?>" alt="Workout Plan">
                    <button class="play-btn" onclick="openWorkoutDialog(event)">▶</button>
                    <h3><?php echo htmlspecialchars($video['plan_name']); ?></h3>              
                    <?php
                        $seconds = $video['time_duration'];
                        $minutes = floor($seconds / 60);
                        $remainingSeconds = $seconds % 60;
                        $readable = sprintf("%d:%02d", $minutes, $remainingSeconds);
                    ?>
                    <p><?= $readable ?> • <?php echo $video['fitness_level']; ?></p>
                </div>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title" id="full-body">Full Body Workouts</h2>
        <div class="plans-grid">
            <?php foreach ($plansChallenge as $video): ?>
            <?php if ($video['target_area'] === 'Full Body'): ?>
            <a href="plan-details.php?id=<?= $video['id'] ?>" class="plan-link">
                <div class="plans-card">
                    <img class="exercise-image" src="../assets/plans-thumbnail/<?php echo $video['file_path']; ?>" alt="Workout Plan">
                    <button class="play-btn" onclick="openWorkoutDialog(event)">▶</button>
                    <h3><?php echo htmlspecialchars($video['plan_name']); ?></h3>              
                    <?php
                        $seconds = $video['time_duration'];
                        $minutes = floor($seconds / 60);
                        $remainingSeconds = $seconds % 60;
                        $readable = sprintf("%d:%02d", $minutes, $remainingSeconds);
                    ?>
                    <p><?= $readable ?> • <?php echo $video['fitness_level']; ?></p>
                </div>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title">Browse by your current mood</h2>
        <div class="horizontal-scroll">
            <div class="long-card">Happy</div>
            <div class="long-card">Sad</div>
            <div class="long-card">Angry</div>
            <div class="long-card">Tired</div>
            <div class="long-card">Energized</div>
        </div>
    </div>

    <script>
        const buttons = document.querySelectorAll(".filter-btn");
        buttons.forEach(btn => {
            btn.addEventListener("click", () => {
                buttons.forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                const target = btn.getAttribute("data-target");
                if (target) {
                    location.hash = target;
                }
            });
        });
    </script>
</body>
</html>
