<?php
session_start();
session_unset();
session_destroy();

require '_base.php';
if(temp('USER_ROLE') == 'Admin')
    redirect(homePageURL());
$_title = 'Fix & Go';
include '_head.php';
include 'pages/homepage.php'
?>

<?php
include '_foot.php';
