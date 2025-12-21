<?php
require_once __DIR__ . "/../../_base.php";
$_title = "Fix & Go | Change Password";
include_once __DIR__ . "/../../_head.php";

?>

<style>
    main {
        background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .auth-container {
        width: 100%;
        padding: 20px;
    }

    .auth-card {
        background: #ffffff;
        max-width: 420px;
        margin: auto;
        padding: 35px 30px;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .auth-card h2 {
        margin-bottom: 10px;
    }

    .auth-desc {
        font-size: 14px;
        color: #666;
        margin-bottom: 25px;
    }

    .auth-form {
        text-align: left;
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
    }

    input {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
    }

    input:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }

    .primary-btn {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        background: #f59e0b;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .primary-btn:hover {
        background: #d97706;
    }

    .auth-footer {
        margin-top: 20px;
        text-align: center;
    }

    .auth-footer a {
        color: #f59e0b;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .error {
        color: red;
        display: none;
    }

    .password-strength {
        margin-top: 5px;
        height: 5px;
        border-radius: 5px;
        background-color: #eee;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s, background-color 0.3s;
    }

    button:disabled {
        cursor: no-drop;
        background-color: #cccccc !important;
    }
</style>

<script src="<?= $rootDir ?>/js/validation.js"></script>


<main>

    <div class="auth-container">
        <div class="auth-card">
            <h2>Change Password</h2>
            <p class="auth-desc">
                Please create a new password for your account.
            </p>

            <form method="POST" action="<?= $rootDir ?>/controller/otp-controller.php?change=password" class="auth-form">

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <div class="error" id="newPasswordError">Password must be 8-12 characters with at least one letter and one number.</div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <div id="confirmPasswordCorrect"></div>
                    <div class="error" id="confirmPasswordError"></div>
                </div>

                <button type="submit" class="primary-btn" id="update-btn" disabled>
                    Update Password
                </button>

                <div class="auth-footer">
                    <a href="login.php">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
    <script src="<?= $rootDir ?>/js/changePassword.js"></script>
</main>
<?php
include_once __DIR__ . "/../../_foot.php";
