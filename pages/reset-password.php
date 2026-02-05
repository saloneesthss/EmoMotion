<?php
session_start();
require_once "../connection.php";

// if($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $password = $_POST['password'];
//     $confirm = $_POST['confirm_password'];
//     $sql = "update users set password = :password";
//     $stmt = $con->prepare($sql);
//     $stmt->bindParam(':password', $password);
//     if($stmt->execute()) {
//         header("Location: login.php?success=Password updated successfully.");
//         die;
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EmoMotion</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Inter", sans-serif;
    }

    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-image: url('../assets/images/login-background.avif');
    }

    .card {
        background: white;
        padding: 55px 50px;
        width: 480px;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: center;
    }

    .icon-wrapper {
        margin-bottom: 20px;
    }

    .icon-circle {
        width: 65px;
        height: 65px;
        background: rgba(125, 60, 255, 0.15);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 20px auto;
    }

    h1 {
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    p {
        color: #666;
        font-size: 14px;
        margin-bottom: 28px;
        line-height: 1.5;
    }

    .label {
        text-align: left;
        font-size: 14px;
        margin-bottom: 5px;
        font-weight: 500;
    }

    input {
        width: 100%;
        padding: 12px 15px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 14px;
        margin-bottom: 5px;
    }

    input:focus {
        outline: none;
        border-color: #7d3cff;
        box-shadow: 0 0 0 2px rgba(125, 60, 255, 0.2);
    }

    .hint {
        text-align: left;
        font-size: 12px;
        color: #777;
        margin-bottom: 18px;
    }

    .error {
        color: #d9534f;
        font-size: 13px;
        margin-bottom: 12px;
        text-align: left;
        display: none;
    }

    button {
        width: 100%;
        background: #7d3cff;
        padding: 12px;
        border-radius: 6px;
        border: none;
        color: white;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
    }

    button:hover {
        background: #6a32e6;
    }

    .back-link {
        margin-top: 18px;
        font-size: 14px;
    }

    .back-link a {
        color: #666;
        text-decoration: none;
    }

    .back-link a:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>
    <div class="card">
        <form method="POST" action="" id="resetForm">
            <div class="icon-wrapper">
                <div class="icon-circle">🔒</div>
            </div>

            <h1>Set new password</h1>
            <p>Your new password must be different to previously used passwords.</p>

            <div class="label">Password</div>
            <input type="password" name="password" id="password" required placeholder="••••••••">
            <div class="hint">Must be at least 8 characters.</div>

            <div class="label">Confirm password</div>
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="••••••••">

            <div class="error" id="errorMsg">Passwords do not match.</div>

            <button type="submit">Reset password</button>

            <div class="back-link">
                ← <a href="../login.php">Back to log in</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('resetForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const errorMsg = document.getElementById('errorMsg');

        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                errorMsg.style.display = 'block';
            }
        });
    </script>
</body>
</html>
