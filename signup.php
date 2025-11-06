<?php
session_start();
require_once "connection.php";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $retypePassword = $_POST['retype-password'];

    if ($password !== $retypePassword) {
        header("Location: signup.php?error=Passwords do not match.");
        die;
    }

    $insertSql = "insert into users (name, email, password) values (:name, :email, :password)";
    $insertStmt = $con->prepare($insertSql);
    $insertStmt->bindParam(':name', $name);
    $insertStmt->bindParam(':email', $email);
    $insertStmt->bindParam(':password', $password);

    if($insertStmt->execute()) {
        $_SESSION['user_login'] = true;
        $_SESSION['username'] = $email;
        $_SESSION['userid'] = $con->lastInsertId();
        header("Location: index.php?id=" . $_SESSION['userid']);
        die;
    } else {
        header("Location: signup.php?error=Something went wrong. Please try again.");
        die;
    }
}
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

                <?php if(isset($_GET['error'])) { ?>
                    <div class="error">
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php } ?>

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