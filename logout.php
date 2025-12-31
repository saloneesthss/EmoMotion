<?php
session_start();

unset($_SESSION['user_login']);
unset($_SESSION['username']);
unset($_SESSION['user_id']);
session_destroy();

if (isset($_COOKIE['remember_user'])) {
    setcookie("remember_user", "", time() - 3600, "/");
}

header("Location:login.php?success=You are logged out successfully.");
die;
?>