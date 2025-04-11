<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #4CAF50;
        }

        .content {
            padding: 20px;
            text-align: left;
        }

        .content p {
            font-size: 16px;
            color: #333;
        }

        .otp-code {
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: inline-block;
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding-top: 20px;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Your OTP Code</h1>
        </div>
        <div class="content">
            <p>Your OTP code is: <span class="otp-code">{{ $otp }}</span></p>
            <p>Please use this code to verify your request.</p>
        </div>
        <div class="footer">
            <p>Thank you for using our service!</p>
        </div>
    </div>
</body>
</html>
