<?php
session_start();

unset($_SESSION['admin_login']);
session_destroy();

if (isset($_COOKIE['rememberme'])) {
    setcookie("rememberme", "", time() - 3600, "/");
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

header("Location:login.php?success=You are logged out successfully.");
die;
?>