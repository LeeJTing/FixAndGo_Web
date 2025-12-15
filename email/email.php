<?php

require __DIR__ . '/../library/PHPMailer.php';
require __DIR__ . '/../library/SMTP.php';

function get_mail()
{


    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $m->Username = 'AACS3173@gmail.com';
    $m->Password = 'xxna ftdu plga hzxl'; // App Password
    $m->CharSet = 'UTF-8';

    $m->setFrom($m->Username, 'Admin');

    return $m;
}
