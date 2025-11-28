<?php require '../../_base.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="<?= $rootDir ?>/images/icon.png">
    <link rel="stylesheet" href="<?= $rootDir ?>/css/admin.css">
    <link rel="stylesheet" href="<?= $rootDir ?>/css/admin-response.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<header>
    <div class="container flex justify-between">
        <a href="#" class="logo" data-link="home">
            <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width: 70px; height: 90px;" />
            Fix&Go
        </a>
        <nav class="desktop-nav">
            <ul class="flex">
                <li><a href="<?= $rootDir ?>#">Products</a></li>
                <li><a href="<?= $rootDir ?>#">Orders</a></li>
            </ul>
        </nav>

        <!-- Menu Toggle Button (appears on all sizes) -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="mobile-dropdown" id="mobileDropdown">
            <ul>
                <li><a href="<?= $rootDir ?>#">Products</a></li>
                <li><a href="<?= $rootDir ?>#">Orders</a></li>
            </ul>
        </div>
    </div>
</header>