 $(document).ready(function() {
            // Hide all success messages initially
            $('.success').hide();
            
            // Real-time password strength indicator
            $('#password').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#passwordStrengthBar');
                
                // Reset strength bar
                strengthBar.css({
                    'width': '0%',
                    'background-color': '#eee'
                });
                
                if (password.length > 0) {
                    let strength = 0;
                    
                    // Length check (8-12 characters)
                    if (password.length >= 8 && password.length <= 12) {
                        strength += 25;
                    } else if (password.length > 12) {
                        // Penalty for exceeding 12 characters
                        strength -= 10;
                    }
                    
                    // Contains letters
                    if (/[a-zA-Z]/.test(password)) strength += 25;
                    
                    // Contains numbers
                    if (/[0-9]/.test(password)) strength += 25;
                    
                    // Contains special characters
                    if (/[^a-zA-Z0-9]/.test(password)) strength += 25;
                    
                    // Ensure strength is between 0 and 100
                    strength = Math.max(0, Math.min(100, strength));
                    
                    // Update strength bar
                    strengthBar.css('width', strength + '%');
                    
                    // Change color based on strength
                    if (strength <= 25) {
                        strengthBar.css('background-color', '#e74c3c'); // Red
                    } else if (strength <= 50) {
                        strengthBar.css('background-color', '#f39c12'); // Orange
                    } else if (strength <= 75) {
                        strengthBar.css('background-color', '#f1c40f'); // Yellow
                    } else {
                        strengthBar.css('background-color', '#2ecc71'); // Green
                    }
                }
            });
            
            // Real-time password confirmation validation
            $('#confirmPassword').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                
                $('#passwordMatch').hide();
                
                if (confirmPassword && password === confirmPassword) {
                    $('#passwordMatch').show();
                }
            });
            
            // Add focus effects using jQuery
            $('.form-input').on('focus', function() {
                $(this).css({
                    'border-color': 'var(--primary-color)',
                    'box-shadow': '0 0 0 2px rgba(74, 109, 229, 0.2)'
                });
            }).on('blur', function() {
                $(this).css({
                    'border-color': 'var(--border-color)',
                    'box-shadow': 'none'
                });
            });
        });