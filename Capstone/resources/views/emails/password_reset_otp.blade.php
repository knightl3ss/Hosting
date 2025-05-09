<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Code</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">
        <h2 style="color: #333;">Password Reset Verification</h2>
        
        <p>You have requested to reset your password. Please use the following code to complete the process:</p>
        
        <div style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0;">
            <h1 style="font-size: 32px; letter-spacing: 5px; margin: 0; color: #333;">{{ $otp }}</h1>
        </div>
        
        <p>This code will expire in 10 minutes.</p>
        
        <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
        
        <p style="margin-top: 30px; font-size: 12px; color: #777;">
            This is an automated email, please do not reply.
        </p>
    </div>
</body>
</html> 