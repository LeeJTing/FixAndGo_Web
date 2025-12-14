<?php

session_start();

if (isset($_POST['logout']) && $_POST['logout'] == true) {
    // Destroy session
    session_unset();
    session_destroy();
}
