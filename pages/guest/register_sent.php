<?php
require_once "../../_base.php";
$_title = "Fix & Go | Registration Sent";
include_once "../../_head.php";
?>
<style>
    main{
        margin: 20vh auto;
    }
</style>

<main>
    <div style="max-width:600px;margin:60px auto;text-align:center;background:#fff;padding:30px;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,0.08);">
        <h1>Check your email</h1>
        <p>A verification email has been sent to the address you provided. Please click "Yes, I am" in that email within 10 minutes to complete your registration.</p>
        <p>If you don't receive the email, check your spam folder or try registering again.</p>
        <p><a href="<?= homePageURL() ?>">Return to Home</a></p>
    </div>
</main>

<?php include_once "../../_foot.php"; ?>
