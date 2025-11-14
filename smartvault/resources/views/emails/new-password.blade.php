<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your New Password') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .password-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛡️ SmartDataVault</div>
            <h1>{{ __('Your New Password') }}</h1>
        </div>

        <p>{{ __('Hello') }} {{ $user->name }},</p>

        <p>{{ __('You have requested a new password for your SmartDataVault account.') }}</p>

        <p><strong>{{ __('Your new password is:') }}</strong></p>

        <div class="password-box">
            {{ $password }}
        </div>

        <div class="warning">
            <strong>⚠️ {{ __('Important Security Notice:') }}</strong><br>
            {{ __('Please change this password immediately after logging in for security reasons.') }}
        </div>

        <p>{{ __('You can now use this password to log in to your account.') }}</p>

        <p style="text-align: center;">
            <a href="{{ route('login') }}" class="button">{{ __('Log In Now') }}</a>
        </p>

        <p>{{ __('If you did not request this password reset, please contact our support team immediately.') }}</p>

        <div class="footer">
            <p>{{ __('This is an automated email. Please do not reply to this message.') }}</p>
            <p>&copy; {{ date('Y') }} SmartDataVault. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>

