<?php
require_once "../../_base.php";

$currentUser = getCurrentUser();

$_title = 'Fix & GO | Profile';
include_once "../../_head.php";
?>

<?php if ($currentUser && $currentUser->user_role === 'Member'): ?>
<script>
    document.body.setAttribute('data-theme', 'light');
</script>
<?php endif; ?>

<?php
include_once "../../component/profileInfo.php";
include "../../_foot.php";
