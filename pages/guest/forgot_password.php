<?php
require_once "../../_base.php";
require_once '../../dao/security_dao.php';
require_once "../../controller/login-controller.php";
$_title = "Fix & Go | Forget Password";
include_once "../../_head.php";
?>

<link rel="stylesheet" href="<?= $rootDir ?>/css/forgot-password.css">
<script src="<?= $rootDir ?>/js/validation.js"></script>
<script src="<?= $rootDir ?>/js/forget-password.js"></script>

<main>
    <?php if (get('error') == 'true') : ?>
        <script>
            alert("Invalid email/no user exist!! Please try again!");
        </script>
    <?php endif ?>
    <div class="forget-password-container">
        <h2><i class="fas fa-unlock-alt"></i> Forget Password</h2>
        <p>Enter your email address below and we'll send you a link to reset your password.</p>

        <form id="forgetPasswordForm" method="POST" action="<?= $rootDir ?>/controller/otp-controller.php?check=email">
            <div class="form-group">
                <label>Email Address <span style="color:red;">*</span></label>
                <input type="email" name="email" id="email" class="form-input" placeholder="you@example.com" required>
                <div class="error-message" id="emailError">Invalid email</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">
                    <span class="btn-text">Send OTP</span>
                    <span class="btn-loading" disabled><i class="fas fa-spinner fa-spin"></i> Sending...</span>
                </button>
            </div>

            <div class="remember-link">
                Remembered your password? <a href="login.php">Login</a>
            </div>
        </form>
    </div>

    <script>
        // Optional JS for showing loading spinner
        const form = document.getElementById('forgetPasswordForm');
        form.addEventListener('submit', function(e) {
            const btnText = this.querySelector('.btn-text');
            const btnLoading = this.querySelector('.btn-loading');
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
        });
    </script>
</main>

<?php
include_once "../../_foot.php";
