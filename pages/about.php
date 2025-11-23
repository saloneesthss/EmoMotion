<?php
require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/about.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class="wrap">
        <header class="brand">
        <div class="logo">EM</div>
        <div>
            <h1>EmoMotion</h1>
            <p>Blending mood with movement — intelligent, empathetic exercise.</p>
        </div>
        </header>

        <div class="hero">
            <div class="hero-card">
                <h2>Move how you feel. Feel while you move.</h2>
                <p class="lead">EmoMotion connects your emotional state with adaptive movement routines — a gentle fusion of mood-aware micro-workouts, breath, and mobility designed to meet you where you are.</p>

                <div class="grid-3" style="margin-top:18px">
                    <div class="card">
                        <h3>Adaptive Workouts</h3>
                        <p class="pill">Short · Personalized · Mood-led</p>
                        <p style="color:var(--muted);margin-top:8px">Workouts adapt duration and intensity to your current mood and energy level.</p>
                    </div>

                    <div class="card">
                        <h3>Emotional Tracking</h3>
                        <p class="pill">Journaling · Tags · Trends</p>
                        <p style="color:var(--muted);margin-top:8px">Subtle prompts help you notice patterns between how you feel and how you move.</p>
                    </div>

                    <div class="card">
                        <h3>Science-Backed</h3>
                        <p class="pill">Evidence · Safe · Respectful</p>
                        <p style="color:var(--muted);margin-top:8px">Built on psychology and exercise physiology to support mental and physical well‑being.</p>
                    </div>
                </div>
            </div>

            <div class="hero-image">
                <img src="../assets/images/main.png" alt="EmoMotion UI preview">
            </div>
        </div>

        <section class="how">
            <div>
                <h3>How EmoMotion Works</h3>
                <div class="step">
                    <h4>1. Check-in</h4>
                    <p style="color:var(--muted);margin:0">A quick mood check with simple emojis, words, or a short voice input.</p>
                </div>
                <div class="step" style="margin-top:12px">
                    <h4>2. Smart Match</h4>
                    <p style="color:var(--muted);margin:0">Our algorithm maps mood to movement patterns — calming flows for overwhelm, energizing mobility for low-energy days.</p>
                </div>
                <div class="step" style="margin-top:12px">
                    <h4>3. Tiny Routines</h4>
                    <p style="color:var(--muted);margin:0">Micro-workouts (2–12 minutes) that fit into any moment, with breathing cues and progress nudges.</p>
                </div>
            </div>

            <div>
                <h3>Benefits</h3>
                <div class="benefits">
                    <div class="card"><strong>Reduce stress</strong><div style="color:var(--muted);margin-top:6px">Short practices lower acute tension and improve mood stability.</div></div>
                    <div class="card"><strong>Increase consistency</strong><div style="color:var(--muted);margin-top:6px">Shorter, mood-aligned sessions are easier to sustain over weeks.</div></div>
                    <div class="card"><strong>Improve awareness</strong><div style="color:var(--muted);margin-top:6px">Track emotional trends and learn what movement helps you most.</div></div>
                </div>
            </div>
        </section>

        <section class="faq">
            <h3>Questions & Answers</h3>

            <div class="qa">
                <div class="q" onclick="toggleQA(this)"><strong>What is EmoMotion?</strong><span>+</span></div>
                <div class="a">EmoMotion is a mood-aware movement app that suggests short, evidence-informed exercises based on your emotional check-ins. It prioritizes safety, accessibility, and gentle progression.</div>
            </div>

            <div class="qa">
                <div class="q" onclick="toggleQA(this)"><strong>Is it a replacement for therapy or medical treatment?</strong><span>+</span></div>
                <div class="a">No. EmoMotion is a self-care and movement tool. It is not a substitute for professional mental health treatment. For clinical concerns, please consult a licensed professional.</div>
            </div>

            <div class="qa">
                <div class="q" onclick="toggleQA(this)"><strong>How long are sessions?</strong><span>+</span></div>
                <div class="a">Sessions are micro-routines between 2 and 12 minutes. You can combine multiple routines for longer sessions if desired.</div>
            </div>

            <div class="qa">
                <div class="q" onclick="toggleQA(this)"><strong>Do I need equipment?</strong><span>+</span></div>
                <div class="a">No. Most routines use bodyweight and mindful movement. Occasionally we suggest a chair or small props for comfort.</div>
            </div>
        </section>

        <footer>
            <div>Made with care — EmoMotion • © <span id="year"></span></div>
        </footer>

    </div>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
        function toggleQA(el){
            const a = el.parentElement.querySelector('.a');
            const sign = el.querySelector('span');
            if (a.style.display === 'block') { a.style.display = 'none'; sign.textContent = '+'; }
            else { a.style.display = 'block'; sign.textContent = '−'; }
        }
    </script>
</body>
</html>