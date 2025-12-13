<?php

$identify = $password = null;

if(is_post()){
    $identify = post("identify");
    $password = post("password");
}