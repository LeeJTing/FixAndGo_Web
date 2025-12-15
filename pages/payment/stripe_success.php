<?php
require "../../_base.php";

$user_id = temp('USER_ID');
if (!$user_id) { header("Location: {$rootDir}/pages/auth/login.php"); exit; }

$order_id = (int)($_GET['order_id'] ?? 0);
?>
<h2>Thanks! Payment received.</h2>
<p>We are confirming your payment…</p>
<a href="<?= $rootDir ?>/pages/order/order_detail.php?order_id=<?= $order_id ?>">Back to Order</a>
