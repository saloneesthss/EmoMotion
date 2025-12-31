<?php
session_start();

unset($_SESSION['admin_login']);
session_destroy();

if (isset($_COOKIE['rememberme'])) {
    setcookie("rememberme", "", time() - 3600, "/");
}

header("Location:login.php?success=You are logged out successfully.");
die;
?>