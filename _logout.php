<?php
ini_set('session.cookie_lifetime', 0);
session_start();

if (isset($_POST['logout']) && $_POST['logout'] == true) {
    // Destroy session
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    session_unset();
    session_destroy();
}
