<?php
require "../../_base.php";
$_title = "Fix & Go | Register";
include "../../_head.php";
?>

<main>

<?php 
    require "../../controller/registerForm-controller.php";
?>

<link rel="stylesheet" href="<?= $rootDir ?>/css/registerForm.css">
<script src="<?= $rootDir ?>/js/validation.js"></script>
<script src="<?= $rootDir ?>/js/registerForm.js"></script>

<script>
    if(<?= $error ?>){
        repeated_id = '<?= $repeatIDMsg ?>';
        repeated_email = '<?= $repeatEmailMsg ?>';
        alert(repeated_id + "\n" + repeated_email + "\nPlease try again.");
    }
</script>

<div class="register-form-container">
    <div class="form-container">
        <div class="logo"><img src="<?= $rootDir ?>/images/logo.png" alt="Fix & Go">Fix & Go</div>
        <h1>Create Your Account</h1>
        
        <form id="registerForm" method="POST" action=>
            <div class="form-group">
                <label for="userID">User ID</label>
                <input class="form-input" type="text" id="userID" name="userID" placeholder="Enter your user ID (max 12 characters)" required>
                <div class="error">User ID must be between 4 and 12 characters.</div>
            </div>
            
            <div class="form-group">
                <label for="userName">User Name</label>
                <input class="form-input" type="text" id="userName" name="userName" placeholder="Enter your full name (no number)" required>
                <div class="error">User name must be between 4 and 50 characters and can not contain any numbers.</div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-input" type="email" id="email" name="email" placeholder="Enter your email" autocomplete="email" required>
                <div class="error">Please enter a valid email address.</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password" placeholder="Create a password (8-12 characters)" required>
                <div class="error">Password must be 8-12 characters with at least one letter and one number.</div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input class="form-input" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                <div class="error">Passwords do not match.</div>
                <div class="success" id="passwordMatch">Passwords match!</div>
            </div>
            
            <button type="submit" disabled>Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="<?= $rootDir ?>/login.php">Login In</a>
        </div>
    </div>
    
    <div class="banner">
        <h2>Join Fix & Go Community</h2>
        <p>Create an account to enjoy exclusive deals, faster checkout, and personalized shopping experiences.</p>
        <p>With Fix & Go, you'll get access to:</p>
        <ul style="text-align: left; margin-top: 15px;">
            <li>Personalized recommendations</li>
            <li>Order tracking and history</li>
            <li>Wishlist and saved items</li>
            <li>Exclusive member discounts</li>
        </ul>
    </div>
</div>

</main>

<?php
include "../../_foot.php";