<?php
session_start();
require_once "../connection.php";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql="select * from admin where username='$username' and password='$password'";
    $stmt=$con->prepare($sql);
    $stmt->execute();

    $loginAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($loginAdmin) {
        if (isset($_POST['rememberme'])) {
            setcookie('rememberme',$loginAdmin['username'],time()+3600*24,'/');
        }
        $_SESSION['admin_login']=true;
        $_SESSION['username']=$loginAdmin['username'];
        header("Location: dashboard.php");
        die;
    } else {
        header("Location: login.php?error=Your entered credintials do not match our records.");
        die;
    }
}
if (isset($_COOKIE['rememberme'])) {
    header("Location:dashboard.php");
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styles/admin/admin-login.css">
</head>
<body>
<div class="container">
    <div class="illustration">
        <img src="../assets/images/dumbbell.png" alt="Illustration">
    </div>

    <div class="login-card">
        <h1>Admin Login</h1>
        <p class="subtitle">
            Login to EmoMotion Admin Panel <br>
            A safe space for physical and mental fitness
        </p>
        <?php if(isset($_GET['error'])) { ?>
            <div class="error" style="color:red; margin-bottom:10px;">
                <?php echo $_GET['error']; ?>
            </div>
        <?php } ?>

        <form action="" method="POST">
            <div class="input-group">
                <span class="icon">👤</span>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <span class="icon">👁️‍🗨️</span>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="small-row">
                <label><input type="checkbox" name="rememberme"> Remember me</label>
                <a href="" class="forgot">Forget password</a>
            </div>

            <button class="login-btn" type="submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>
