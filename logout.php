<?php
include_once './includes/operatons.php';
session_start();
$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), "", time() - 42000, "/");
}
session_destroy();
redirect_to("login.php?logout=1");
?>