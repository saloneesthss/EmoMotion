<?php
session_start();
require_once "../connection.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location:../login.php?error=You are not logged in, please login first." );
    exit;
}
?>