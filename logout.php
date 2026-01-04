<?php
session_start();

unset($_SESSION['user_login']);
unset($_SESSION['username']);
unset($_SESSION['user_id']);
session_destroy();

if (isset($_COOKIE['remember_user'])) {
    setcookie("remember_user", "", time() - 3600, "/");
}

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

header("Location:pages/index.php?success=You are logged out successfully.");
die;
?>