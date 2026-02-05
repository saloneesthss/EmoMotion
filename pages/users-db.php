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
    $plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($plans as &$p) {
        $p['score'] = bmiScore($p, $bmi);
    }
    usort($plans, fn($a, $b) => $b['score'] <=> $a['score']);
    $recommended = array_slice($plans, 0, 8);
} else {
    $tagWeights = [
        'target_area' => [],
        'mood' => [],
        'fitness_level' => [],
        'intensity' => []
    ];
    foreach ($favorites as $fav) {
        $tagWeights['target_area'][$fav['target_area']] = 
            ($tagWeights['target_area'][$fav['target_area']] ?? 0) + 1;

        $tagWeights['mood'][$fav['mood']] = 
            ($tagWeights['mood'][$fav['mood']] ?? 0) + 1;

        $tagWeights['fitness_level'][$fav['fitness_level']] = 
            ($tagWeights['fitness_level'][$fav['fitness_level']] ?? 0) + 1;

        $tagWeights['intensity'][$fav['intensity']] = 
            ($tagWeights['intensity'][$fav['intensity']] ?? 0) + 1;
    }
    // $stmt = $con->prepare("
    //     SELECT *
    //     FROM workout_plans
    //     WHERE id NOT IN (SELECT plan_id FROM favorites WHERE user_id = ?)
    // ");
    // $stmt->execute([$user_id]);
    // $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // $fitness_map = ["Beginner"=>1, "Intermediate"=>2, "Advanced"=>3];
    // $intensity_map = ["Low"=>1, "Medium"=>2, "High"=>3];

    // $unique_targets = [];
    // $unique_moods = [];

    // foreach ($plans as $p) {
    //     if (!in_array($p['target_area'], $unique_targets)) $unique_targets[] = $p['target_area'];
    //     if (!in_array($p['mood'], $unique_moods)) $unique_moods[] = $p['mood'];
    // }
    // $user_vector_sum = [];
    // $favorite_vectors = [];

    // foreach ($favorites as $fav) {
    //     $vec = [];
    //     $vec[] = $fitness_map[$fav['fitness_level']] ?? 1;

    //     foreach ($unique_targets as $t) {
    //         $vec[] = ($fav['target_area'] === $t) ? 1 : 0;
    //     }

    //     foreach ($unique_moods as $m) {
    //         $vec[] = ($fav['mood'] === $m) ? 1 : 0;
    //     }

    //     $vec[] = $intensity_map[$fav['intensity']] ?? 1;
    //     $favorite_vectors[] = $vec;
    // }
    // $vector_length = count($favorite_vectors[0]);
    // $user_profile = array_fill(0, $vector_length, 0);

    // foreach ($favorite_vectors as $vec) {
    //     foreach ($vec as $i => $v) {
    //         $user_profile[$i] += $v;
    //     }
    // }

    // foreach ($user_profile as $i => $value) {
    //     $user_profile[$i] = $value / count($favorite_vectors);
    // }

    $recommended = [];
    foreach ($plansChallenge as $p) {
        $planId = $p['id'];
        $isFavorite = false;
        foreach ($favorites as $f) {
            if ($f['id'] == $planId) { $isFavorite = true; break; }
        }
        if ($isFavorite) continue;

        // $vec = [];
        // $vec[] = $fitness_map[$p['fitness_level']] ?? 1;

        // foreach ($unique_targets as $t) {
        //     $vec[] = ($p['target_area'] === $t) ? 1 : 0;
        // }

        // foreach ($unique_moods as $m) {
        //     $vec[] = ($p['mood'] === $m) ? 1 : 0;
        // }

        // $vec[] = $intensity_map[$p['intensity']] ?? 1;
        // $dot = 0; $magA = 0; $magB = 0;
        // foreach ($vec as $i => $v) {
        //     $dot += $v * $user_profile[$i];
        //     $magA += $v * $v;
        //     $magB += $user_profile[$i] * $user_profile[$i];
        // }
        // $magA = sqrt($magA);
        // $magB = sqrt($magB);
        // $similarity = ($magA > 0 && $magB > 0) ? ($dot / ($magA * $magB)) : 0;

        $contentScore = 0;
        $contentScore += $tagWeights['target_area'][$p['target_area']] ?? 0;
        $contentScore += $tagWeights['mood'][$p['mood']] ?? 0;
        $contentScore += $tagWeights['fitness_level'][$p['fitness_level']] ?? 0;
        $contentScore += $tagWeights['intensity'][$p['intensity']] ?? 0;

        if ($contentScore > 10) $contentScore = 10;

        $bmiScoreValue = bmiScore($p, $bmi);
        $p['score'] = ($contentScore * 0.7) + ($bmiScoreValue * 0.3);
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

$customizeStmt = $con->prepare("SELECT * FROM customized_plans WHERE user_id = $user_id");
$customizeStmt->execute();
$customized = $customizeStmt->fetchAll(PDO::FETCH_ASSOC);
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
            <?php if (!empty($customized)): ?>
                <a href="#created-by-me"><button class="filter-btn">Created by me</button></a>
            <?php endif; ?>
            <a href="#challenges"><button class="filter-btn">Challenges</button></a>
            <a href="#full-body"><button class="filter-btn">Full Body</button></a>

            <button class="create-workout" onclick="location.href='../pages/customize-plans.php'">+ Create your own plan</button>
        </div>
        
        <div id="search-results" class="search-results"></div>

        <div id="plans-results"></div>

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
    
        <?php if (!empty($customized)): ?>
        <h2 class="section-title" id="created-by-me">Customized by you</h2>
        <div class="plans-grid">
            <?php foreach ($customized as $video): ?>
            <a href="customized-details.php?id=<?= $video['id'] ?>" class="plan-link">
                <div class="plans-card">
                    <img class="exercise-image" src="../assets/userplan-thumbnail/<?php echo $video['file_path']; ?>" alt="Workout Plan">
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
        <?php endif; ?>

        <h2 class="section-title" id="challenges">Workout Challenges</h2>
        <div class="plans-grid">
            <?php $latestFour = array_slice($plansChallenge, 0, 4); ?>
            <?php foreach ($latestFour as $video): ?>
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
            <?php $latestFour = array_slice($plansChallenge, 0, 4); ?>
            <?php foreach ($latestFour as $video): ?>
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

        <!-- <h2 class="section-title">Browse by your current mood</h2>
        <div class="horizontal-scroll">
            <div class="long-card">Happy</div>
            <div class="long-card">Sad</div>
            <div class="long-card">Angry</div>
            <div class="long-card">Tired</div>
            <div class="long-card">Energized</div>
        </div> -->
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

        const searchInput = document.getElementById("search-hint");
        const searchBox = document.getElementById("search-results");

        searchInput.addEventListener("keyup", function() {
            let query = this.value;

            if (query.trim() === "") {
                searchBox.style.display = "none";
                searchBox.innerHTML = "";
                return;
            }

            fetch("../pages/search-plans.php?query=" + encodeURIComponent(query))
                .then(res => res.text())
                .then(data => {
                    searchBox.innerHTML = data;
                    searchBox.style.display = "block";
                });
        });

        document.addEventListener("click", function(e) {
            if (!searchBox.contains(e.target) && e.target !== searchInput) {
                searchBox.style.display = "none";
            }
        });
    </script>
</body>
</html>
