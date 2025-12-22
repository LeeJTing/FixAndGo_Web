<?php

/**
 * Authorization enforcement helper.
 *
 * Rules:
 * - Guest (no temp('USER_ROLE')): may access only /pages/guest/*, /pages/product/product-list.php,
 *   and /pages/product/product-detail.php. All other PHP pages redirect to login.
 * - Member: may access any non-admin pages.
 * - Admin: may access admin pages under /pages/admin/ and other pages.
 */
function enforce_authorization(): void
{
    // Allow when running from CLI or headers already sent
    if (php_sapi_name() === 'cli') {
        return;
    }

    global $rootDir, $pathPrefix;

    $role = temp('USER_ROLE') ?? 'Guest';

    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';

    // Normalize path by removing any deployment prefix (e.g. /FixAndGo_Web)
    $pathPrefix = $pathPrefix ?? '';
    $normalizedPath = $path;
    if ($pathPrefix !== '' && str_starts_with($normalizedPath, $pathPrefix)) {
        $normalizedPath = substr($normalizedPath, strlen($pathPrefix));
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }
    }

    // Allow static asset requests (css/js/images/fonts)
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $staticExt = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'map', 'otf'];
    if ($ext !== '' && in_array($ext, $staticExt, true)) {
        return;
    }

    // Admin area protection
    if (str_starts_with($normalizedPath, '/pages/admin/')) {
        if ($role !== 'Admin') {
            header('Location: ' . ($rootDir ?? '') . $pathPrefix . '/pages/member/memberHome.php');
            exit();
        }
        return;
    }

    // If user is Admin and requesting a non-admin page, redirect them to admin dashboard.
    if ($role === 'Admin') {
        // Allow admin to access logout endpoint so they can sign out
        $adminAllowedExceptions = ['/_logout.php'];
        if (!in_array($normalizedPath, $adminAllowedExceptions, true)) {
            header('Location: ' . ($rootDir ?? '') . $pathPrefix . '/pages/admin/adminDashboard.php');
            exit();
        }
        return;
    }

    // Member can access everything except the admin folder (handled above)
    if ($role === 'Member') {
        return;
    }

    // Guest access: restrict to guest pages and product listing/detail
    $guestAllowed = [
        '/pages/guest/',
        '/pages/product/product-list.php',
        '/pages/product/product-detail.php',
        '/index.php',
    ];

    foreach ($guestAllowed as $allow) {
        if (str_starts_with($normalizedPath, $allow) || $normalizedPath === $allow) {
            return;
        }
    }

    // Default: redirect guests to login
    header('Location: ' . ($rootDir ?? '') . $pathPrefix . '/pages/guest/login.php');
    exit();
}
