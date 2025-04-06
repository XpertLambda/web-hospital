<?php
/**
 * Email Sender Class
 * Handles sending various types of emails for the application
 */
class EmailSender {
    /**
     * Send verification email to newly registered users
     * 
     * @param string $email Recipient email address
     * @param string $name Recipient name
     * @param string $verificationLink Link for email verification
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendVerificationEmail($email, $name, $verificationLink) {
        // Email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: MediCENTER <no-reply@medicenter.com>" . "\r\n";
        
        // Email subject
        $subject = "MediCENTER - Verify Your Email";
        
        // Email body
        $body = "
        <html>
        <head>
            <title>Verify Your Email</title>
        </head>
        <body>
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #3c8dbc; color: white; padding: 20px; text-align: center;'>
                    <h1>Welcome to MediCENTER</h1>
                </div>
                <div style='padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;'>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>Thank you for registering with MediCENTER. To complete your registration, please verify your email address by clicking the button below:</p>
                    <p style='text-align: center;'>
                        <a href='$verificationLink' style='background-color: #3c8dbc; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block;'>Verify Email</a>
                    </p>
                    <p>If the button above doesn't work, please copy and paste the following link into your browser:</p>
                    <p>$verificationLink</p>
                    <p>This link will expire in 24 hours.</p>
                    <p>Best regards,<br>The MediCENTER Team</p>
                </div>
                <div style='text-align: center; padding: 10px; font-size: 12px; color: #666;'>
                    <p>&copy; 2025 MediCENTER. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        try {
            // Attempt to send the email
            $mailSent = mail($email, $subject, $body, $headers);
            return $mailSent;
        } catch (Exception $e) {
            // Log the error (in a real application, you would use a proper logging system)
            error_log("Failed to send verification email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send password reset email
     * 
     * @param string $email Recipient email address
     * @param string $name Recipient name
     * @param string $resetLink Link for password reset
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendPasswordResetEmail($email, $name, $resetLink) {
        // Email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: MediCENTER <no-reply@medicenter.com>" . "\r\n";
        
        // Email subject
        $subject = "MediCENTER - Password Reset Request";
        
        // Email body
        $body = "
        <html>
        <head>
            <title>Password Reset</title>
        </head>
        <body>
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #3c8dbc; color: white; padding: 20px; text-align: center;'>
                    <h1>MediCENTER Password Reset</h1>
                </div>
                <div style='padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;'>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>We received a request to reset your password. Click the button below to create a new password:</p>
                    <p style='text-align: center;'>
                        <a href='$resetLink' style='background-color: #3c8dbc; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block;'>Reset Password</a>
                    </p>
                    <p>If you didn't request a password reset, please ignore this email or contact our support team if you have concerns.</p>
                    <p>The link will expire in 1 hour.</p>
                    <p>Best regards,<br>The MediCENTER Team</p>
                </div>
                <div style='text-align: center; padding: 10px; font-size: 12px; color: #666;'>
                    <p>&copy; 2025 MediCENTER. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        try {
            // Attempt to send the email
            $mailSent = mail($email, $subject, $body, $headers);
            return $mailSent;
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to send password reset email: " . $e->getMessage());
            return false;
        }
    }
}
?>
