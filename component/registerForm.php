<link rel="stylesheet" href="<?= $rootDir ?>/css/registerForm.css">

<div class="register-form-container">
        <div class="form-container">
            <div class="logo"><img src="<?= $rootDir ?>/images/logo.png" alt="Fix & Go">Fix & Go</div>
            <h1>Create Your Account</h1>
            <form id="registerForm">
                <div class="form-group">
                    <label for="userID">User ID</label>
                    <input class="form-input" type="text" id="userID" name="userID" placeholder="Enter your user ID" required>
                    <div class="error" id="userIDError">User ID must be at least 4 characters long</div>
                </div>
                
                <div class="form-group">
                    <label for="userName">User Name</label>
                    <input class="form-input" type="text" id="userName" name="userName" placeholder="Enter your full name" required>
                    <div class="error" id="userNameError">Please enter a valid name</div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-input" type="email" id="email" name="email" placeholder="Enter your email" autocomplete="email" required>
                    <div class="error" id="emailError">Please enter a valid email address</div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password" placeholder="Create a password" required>
                    <div class="password-requirements">Password must be at least 8 characters with letters and numbers</div>
                    <div class="error" id="passwordError">Password must be at least 8 characters with letters and numbers</div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input class="form-input" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                    <div class="error" id="confirmPasswordError">Passwords do not match</div>
                    <div class="success" id="passwordMatch">Passwords match!</div>
                </div>
                
                <button type="submit">Create Account</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="#">Sign in</a>
            </div>
        </div>
        
        <div class="banner">
            <h2>Join Our E-commerce Community</h2>
            <p>Create an account to enjoy exclusive deals, faster checkout, and personalized shopping experiences.</p>
            <p>With ShopEase, you'll get access to:</p>
            <ul style="text-align: left; margin-top: 15px;">
                <li>Personalized recommendations</li>
                <li>Order tracking and history</li>
                <li>Wishlist and saved items</li>
                <li>Exclusive member discounts</li>
            </ul>
        </div>
    </div>