<?php
session_start();
require_once "connection.php";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "select * from users where email = :email and password = :password";
    $loginStmt = $con->prepare($sql);
    $loginStmt->bindParam(':email', $email);
    $loginStmt->bindParam(':password', $password);

    $loginStmt->execute();
    $loginUser = $loginStmt->fetch(PDO::FETCH_ASSOC);
   
    if($loginUser) {
        $_SESSION['user_login'] = true;
        $_SESSION['username'] = $loginUser['email'];
        $_SESSION['userid'] = $loginUser['id'];
         if(isset($_POST['remember-me'])) {
            setcookie("remember_user", $loginUser['id'], time() + (86400 * 7), "/"); // store 30 days
        }
        header("Location: pages/users-db.php?id=" . $_SESSION['userid']);
        die;
    } else {
        header("Location: login.php?error=Your entered credintials do not match our records.");
        die;
    }
}

if(isset($_COOKIE['remember_user'])) {
    header("Location: pages/users-db.php?id=" . $_COOKIE['remember_user']);
    exit();
}

if(isset($_SESSION['user_login']) && $_SESSION['user_login'] === true) {
    if(isset($_COOKIE['remember_user'])) {
        header("Location: pages/users-db.php?id=" . $_SESSION['userid']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="styles/login.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class="container">
        <div class="form-container log-in">
            <form action="" method="post" id="login-form">
                <h1>Log In</h1>

                <?php if(isset($_GET['error'])) { ?>
                    <div class="error" style="color: #8B0000;">
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php } ?>
            
                <input required type="text" name="email" id="email" placeholder="Email">
                <input required type="password" name="password" id="password" placeholder="Password">

                <div class="additional-info">
                    <div class="remember-me">
                        <input type="checkbox" name="remember-me" id="remember-me">
                        Remember Me
                    </div>
                    <a class="forgot-password">
                        Forgot your password?
                    </a>
                </div>

                <button class="login">Log In</button>
                <div class="or">or</div>
                <button class="login-google">
                    <img src="assets/icons/google-logo.webp" alt="Google Logo">
                    Log in with Google
                </button>

                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form> 
        </div>
    </div>
</body>
</html>