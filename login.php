<?php
// require_once "./components/navbar.php";
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
            
                <input type="text" name="emailLogin" id="emailLogin" placeholder="Email">
                <input type="password" name="passwordLogin" id="passwordLogin" placeholder="Password">

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