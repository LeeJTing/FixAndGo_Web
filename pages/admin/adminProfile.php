<?php
require '../../_base.php';

$currentUser = getCurrentUser();
if (!$currentUser || $currentUser->user_role !== 'Admin') {
    redirect($pathPrefix . '/index.php');
}

include 'adminHeader.php';
include __DIR__ . '/../../component/profileInfo.php';
include 'adminFooter.php';
