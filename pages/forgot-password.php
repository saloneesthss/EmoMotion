<?php
session_start();
require_once "../connection.php";

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['reset_email'] = $email;
            header("Location: reset-password.php");
            die;
        } else {
            $error = 'No account found with that email.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EmoMotion - Forgot Password</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Inter", sans-serif; }
    body { height: 100vh; display: flex; justify-content: center; align-items: center; background-image: url('../assets/images/login-background.avif'); }
    .card { background: white; padding: 55px 50px; width: 480px; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); text-align: center; }
    .icon-wrapper { margin-bottom: 20px; }
    .icon-circle { width: 65px; height: 65px; background: rgba(125, 60, 255, 0.15); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 20px auto; }
    h1 { font-size: 26px; font-weight: 600; margin-bottom: 8px; }
    p { color: #666; font-size: 14px; margin-bottom: 28px; line-height: 1.5; }
    .label { text-align: left; font-size: 14px; margin-bottom: 5px; font-weight: 500; }
    input { width: 100%; padding: 12px 15px; border-radius: 6px; border: 1px solid #ddd; font-size: 14px; margin-bottom: 5px; }
    input:focus { outline: none; border-color: #7d3cff; box-shadow: 0 0 0 2px rgba(125, 60, 255, 0.2); }
    .hint { text-align: left; font-size: 12px; color: #777; margin-bottom: 18px; }
    .error { color: #d9534f; font-size: 13px; margin-bottom: 12px; text-align: left; display: none; }
    button { width: 100%; background: #7d3cff; padding: 12px; border-radius: 6px; border: none; color: white; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 10px; }
    button:hover { background: #6a32e6; }
    .back-link { margin-top: 18px; font-size: 14px; }
    .back-link a { color: #666; text-decoration: none; }
    .back-link a:hover { text-decoration: underline; }
</style>
</head>
<body>
    <div class="card">
        <form method="POST" action="" id="emailForm">
            <div class="icon-wrapper">
                <div class="icon-circle">🔒</div>
            </div>

            <h1>Reset your password</h1>
            <p>Enter the email address associated with your account.</p>

            <div class="label">Email</div>
            <input type="email" name="email" id="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>">
            <div class="hint">We'll use this to identify your account.</div>

            <div class="error" id="errorMsg" style="<?php echo $error ? 'display:block;' : 'display:none;'; ?>"><?php echo htmlspecialchars($error); ?></div>

            <button type="submit">Continue</button>

            <div class="back-link">
                ← <a href="../login.php">Back to log in</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('emailForm');
        const email = document.getElementById('email');
        const errorMsg = document.getElementById('errorMsg');

        form.addEventListener('submit', function(e) {
            if (!email.value || !/.+@.+\..+/.test(email.value)) {
                e.preventDefault();
                errorMsg.textContent = 'Please enter a valid email address.';
                errorMsg.style.display = 'block';
            }
        });
    </script>
</body>
</html>
