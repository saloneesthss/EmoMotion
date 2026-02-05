<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$user_id = $_SESSION['user_id'];

$stmt = $con->prepare("
    SELECT uh.id AS history_id, uh.clicked_at,
           ua.id AS activity_id, ua.activity_date, uh.plan_id
    FROM user_history uh
    LEFT JOIN user_activity ua ON uh.activity_id = ua.id
    WHERE uh.user_id = ?
    ORDER BY uh.clicked_at DESC
");
$stmt->execute([$user_id]);
$historyRaw = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$history = [];

foreach ($historyRaw as $item) {
    $plans = [];
    if (!empty($item['plan_id'])) {
        $planIds = json_decode($item['plan_id'], true);
        if (is_array($planIds) && count($planIds) > 0) {
            $in = str_repeat('?,', count($planIds) - 1) . '?';
            $planStmt = $con->prepare("SELECT * FROM workout_plans WHERE id IN ($in)");
            $planStmt->execute($planIds);
            $plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    $item['plans'] = $plans; 
    $history[] = $item;
}

$historyVid = $con->prepare("
    SELECT video_id
    FROM user_history
    WHERE user_id = ?
");
$historyVid->execute([$user_id]);
$rows = $historyVid->fetchAll(PDO::FETCH_ASSOC);

$videos = [];

foreach ($rows as $row) {
    $videoIds = json_decode($row['video_id'], true);
    if (!is_array($videoIds)) {
        if ($videoIds !== null) {
            $videoIds = [$videoIds];
        } else {
            $videoIds = [];
        }
    }

    if (count($videoIds) > 0) {
        $in = str_repeat('?,', count($videoIds) - 1) . '?';
        $stmt = $con->prepare("SELECT * FROM workout_videos WHERE id IN ($in)");
        $stmt->execute($videoIds);
        $videos = array_merge($videos, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/settings.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/my-history.css">
    <link rel="stylesheet" href="../styles/user-profile.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h2>EmoMotion</h2>

            <a href="user-profile.php?id=<?php echo $_SESSION['user_id'];?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-history.php?id=<?php echo $_SESSION['user_id'];?>"><i class="fa-solid fa-clock-rotate-left"></i> My History</a>
            <a href="my-plans.php?id=<?php echo $_SESSION['user_id'];?>"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php?id=<?php echo $_SESSION['user_id'];?>"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php?id=<?php echo $_SESSION['user_id'];?>"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php?id=<?php echo $_SESSION['user_id'];?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" onclick="return confirm('Are you sure to logout?')" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
    <div class="page-container">
        <h3>Watch history</h3>
        <!-- <div class="date-filters">
            <div>
                <label>From:</label>
                <input type="date" id="fromDate">
            </div>
            <div>
                <label>To:</label>
                <input type="date" id="toDate">
            </div>
            <button id="filter-btn"><img src="../assets/icons/filter-icon.png">Filter</button>
        </div> -->

        <h3 style="font-size:18px; margin-bottom:10px;">Videos History</h3>

        <div class="shorts-wrapper">
            <button class="scroll-btn left-btn" onclick="scrollShorts(-1)" style="display:none;">&lt;</button>
            <div class="shorts-row" id="shortsRow">
                <?php foreach ($videos as $video): ?>
                    <div class="short-card">
                        <img src="<?= '../assets/gifs/' . htmlspecialchars($video['file_path']) ?>" />
                        <div class="video-title"><?= htmlspecialchars($video['title']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn right-btn" onclick="scrollShorts(1)">&gt;</button>
        </div>
        
        <?php foreach ($history as $item): ?>
            <?php if (!empty($item['plans'])): ?>
                <?php foreach($item['plans'] as $plan): ?>
                    <div class="video-item">
                        <img class="video-thumbnail"
                            src="<?= !empty($plan['file_path']) ? '../assets/plans-thumbnail/' . htmlspecialchars($plan['file_path']) : 'https://via.placeholder.com/180x100' ?>"
                            alt="Workout Thumbnail">
                        <div class="video-info">
                            <h3><?= htmlspecialchars($plan['plan_name']) ?></h3>
                            <div class="video-meta">
                                Viewed on <?= date("M d, Y", strtotime($item['clicked_at'])) ?>
                            </div>
                            <p class="video-desc"><?= !empty($plan['description']) ? htmlspecialchars($plan['description']) : 'No description available.' ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
           
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <script>
        function scrollShorts(direction) {
            const row = document.getElementById("shortsRow");
            row.scrollBy({ left: direction * 220, behavior: "smooth" });
        }
        const shortsRow = document.getElementById('shortsRow');
        const leftBtn = document.querySelector('.left-btn');
        const rightBtn = document.querySelector('.right-btn');
        const card = shortsRow.querySelector('.short-card');
        const cardWidth = card.offsetWidth + 10; 
        const totalCards = shortsRow.querySelectorAll('.short-card').length;

        const visibleCards = 4;
        let scrollIndex = 0;
        updateButtons();

        function scrollShorts(direction) {
            scrollIndex += direction;
            if (scrollIndex < 0) scrollIndex = 0;
            if (scrollIndex > totalCards - visibleCards) scrollIndex = totalCards - visibleCards;

            shortsRow.scrollTo({
                left: scrollIndex * cardWidth,
                behavior: 'smooth'
            });
            updateButtons();
        }

        function updateButtons() {
            if (scrollIndex > 0) {
                leftBtn.style.display = 'block';
            } else {
                leftBtn.style.display = 'none';
            }

            if ((totalCards > visibleCards || totalCards === visibleCards) && scrollIndex < totalCards - visibleCards) {
                rightBtn.style.display = 'block';
            } else {
                rightBtn.style.display = 'none';
            }
        }

        const fromInput = document.getElementById('fromDate');
        const toInput = document.getElementById('toDate');
        const filterBtn = document.getElementById('filter-btn');

        filterBtn.addEventListener('click', () => {
            const fromDate = fromInput.value;
            const toDate = toInput.value;
            if (!fromDate && !toDate) return; 
            fetchFilteredVideos(fromDate, toDate);
        });

        function fetchFilteredVideos(from, to) {
            fetch('../pages/filter-history.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ from, to })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    displayVideos(data.videos);
                } else {
                    alert(data.error);
                }
            })
            .catch(err => console.error(err));
        }

        function displayVideos(videos) {
            shortsRow.innerHTML = '';
            videos.forEach(video => {
                const card = document.createElement('div');
                card.classList.add('short-card');
                card.innerHTML = `
                    <img src="../assets/gifs/${video.file_path}" />
                    <div class="video-title">${video.title}</div>
                `;
                shortsRow.appendChild(card);
            });

            shortsRow.scrollLeft = 0;
            scrollIndex = 0;
            updateButtons();
        }
    </script>
</body>
</html>
