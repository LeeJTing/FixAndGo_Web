<?php
require "../../_base.php";
require '../../dao/security_dao.php';
require "../../controller/login-controller.php";
$_title = "Fix & Go | Login";
include "../../_head.php";
?>

<link rel="stylesheet" href="<?= $rootDir ?>/css/login.css">
<script src="<?= $rootDir ?>/js/validation.js"></script>
<script src="<?= $rootDir ?>/js/login.js"></script>
<?php if ($error): ?>
<script>
    alert("<?= htmlspecialchars($error) ?>");
</script>
<?php endif; ?>
<main>
    <div class="login-form-container">
        <div class="logo">
            <img src="<?= $rootDir ?>/images/logo.png" alt="Fix & Go">
            Fix & Go
        </div>
        <h1>Welcome Back</h1>
        
        <form id="loginForm" method="POST">
            <div class="form-group">
                <label for="identify">User ID / Email</label>
                <input class="form-input" type="text" id="identify" name="identify" placeholder="Enter your User ID / Email Address" required>
                <div class="error" id="identifyError">Please enter your User ID / Email</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="error" id="passwordError">Please enter your password</div>
            </div>
            
            <!-- <div class="remember-me">
                <input type="checkbox" id="rememberMe" name="rememberMe">
                <label for="rememberMe">Remember me</label>
            </div> -->
            
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            
            <button type="submit" id="loginBtn" disabled>Login</button>
            
            <div class="register-link">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </form>
    </div>
</main>

<?php
include "../../_foot.php";
