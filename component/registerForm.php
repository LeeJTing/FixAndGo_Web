<link rel="stylesheet" href="<?= $rootDir ?>/css/registerForm.css">

<div class="register-form-container">
    <div class="form-container">
        <div class="logo"><img src="<?= $rootDir ?>/images/logo.png" alt="Fix & Go">Fix & Go</div>
        <h1>Create Your Account</h1>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="error"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form id="registerForm" method="POST" action="">
            <div class="form-group">
                <label for="userID">User ID</label>
                <input class="form-input" type="text" id="userID" name="userID" placeholder="Enter your user ID (max 12 characters)" value="<?php echo htmlspecialchars($userID); ?>" required>
                <?php if (!empty($errors['userID'])): ?>
                    <div class="error"><?php echo $errors['userID']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="userName">User Name</label>
                <input class="form-input" type="text" id="userName" name="userName" placeholder="Enter your full name (no number)" value="<?php echo htmlspecialchars($userName); ?>" required>
                <?php if (!empty($errors['userName'])): ?>
                    <div class="error"><?php echo $errors['userName']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-input" type="email" id="email" name="email" placeholder="Enter your email" autocomplete="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password" placeholder="Create a password (8-12 characters)" required>
                <div class="password-requirements">Password must be 8-12 characters with at least one letter and one number</div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <div class="error"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input class="form-input" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                <?php if (!empty($errors['confirmPassword'])): ?>
                    <div class="error"><?php echo $errors['confirmPassword']; ?></div>
                <?php endif; ?>
                <div class="success" id="passwordMatch" style="display: none;">Passwords match!</div>
            </div>
            
            <button type="submit">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="<?= $rootDir ?>/login.php">Sign in</a>
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

    <script src="<?= $rootDir ?>/js/registerForm.js"></script>