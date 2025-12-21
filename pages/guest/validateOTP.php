<?php
require_once __DIR__ . "/../../_base.php";
$_title = "Fix & Go | Validate OTP";
include_once __DIR__ . "/../../_head.php";

?>

<style>
    .otp-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .otp-card {
        background: white;
        padding: 40px;
        border-radius: 16px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .otp-card h2 {
        margin-bottom: 10px;
        font-size: 26px;
    }

    .otp-card p {
        font-size: 14px;
        color: #666;
        margin-bottom: 30px;
    }

    .otp-input-group input {
        width: 100%;
        padding: 15px;
        font-size: 22px;
        text-align: center;
        letter-spacing: 6px;
        border-radius: 10px;
        border: 2px solid #ddd;
        transition: 0.3s;
    }

    .otp-input-group input:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }

    .otp-btn {
        margin-top: 25px;
        width: 100%;
        padding: 14px;
        background: #f59e0b;
        border: none;
        border-radius: 10px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .otp-btn:hover {
        background: #d97706;
    }

    .error-message {
        color: #e74c3c;
        font-size: 13px;
        margin-top: 10px;
        display: none;
    }

    .otp-footer {
        margin-top: 25px;
        font-size: 14px;
    }

    .otp-footer a {
        color: #f59e0b;
        text-decoration: none;
        font-weight: 600;
    }

    .otp-footer a:hover {
        text-decoration: underline;
    }
</style>

<main>
    <?php if (get('error') == 'true'): ?>
        <script>
            alert('The OTP is invalid! Please try again!');
        </script>
    <?php endif ?>
    <div class="otp-container">
        <div class="otp-card">
            <h2>Verify OTP</h2>
            <p>Please enter the 6-digit OTP sent to your email.</p>

            <form id="otpForm" method="POST" action="<?= $rootDir ?>/controller/otp-controller.php?validate=otp">
                <div class="otp-input-group">
                    <input type="text" name="otp" id="otp" maxlength="6" placeholder="Enter OTP" required>
                </div>

                <div class="error-message" id="otpError"></div>

                <button type="submit" class="otp-btn">Verify OTP</button>
            </form>

            <div class="otp-footer">
                <span>Didn't receive the code?</span>
                <a href="<?= $rootDir ?>/controller/otp-controller.php?otp=resend">Resend OTP</a>
            </div>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . "/../../_foot.php";
