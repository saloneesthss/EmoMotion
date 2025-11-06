<?php
// require_once "./components/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="styles/signup.css">
    <title>EmoMotion</title>
</head>
<body>
    <div class="container">
        <div class="form-container sign-up">
            <form action="" method="post" id="login-form">
                <h1>Create Account</h1>
                <div class="headline">
                    <div>Start your fitness journey today!</div>
                    <a href="login.php">
                        Existing user?
                    </a>
                </div> 

                <input type="text" name="name" id="name" placeholder="Name">
                <input type="email" name="email" id="email" placeholder="Email">
                <input type="password" name="password" id="password" placeholder="Password">
                <input type="retype-password" name="retype-password" id="retype-password" placeholder="Re-type Password">

                <button class="signup">Sign Up</button>
                <div class="or">or</div>
                <button class="signup-google">
                    <img src="assets/icons/google-logo.webp" alt="Google Logo">
                    Sign up with Google
                </button>
            </form>
        </div>
    </div>
</body>
</html>