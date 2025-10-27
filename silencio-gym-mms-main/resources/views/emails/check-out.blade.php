<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-out Confirmation - Silencio Gym</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #DC2626;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
        }
        .value {
            color: #1f2937;
            font-weight: 600;
        }
        .footer {
            background: #1f2937;
            color: #9ca3af;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 14px;
        }
        .success-icon {
            font-size: 48px;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👋 Check-out Successful!</h1>
        <p style="margin: 10px 0 0 0; font-size: 16px;">Thank you for visiting Silencio Gym</p>
    </div>
    
    <div class="content">
        <div class="success-icon">🎯</div>
        
        <p style="font-size: 18px; text-align: center; margin: 0 0 20px 0;">
            Goodbye <strong>{{ $memberName }}</strong>!
        </p>
        
        <p style="text-align: center; color: #6b7280;">
            You have successfully checked out from Silencio Gym. Great job on completing your workout!
        </p>
        
        <div class="info-box">
            <div class="info-row">
                <span class="label">Member Name:</span>
                <span class="value">{{ $memberName }}</span>
            </div>
            <div class="info-row">
                <span class="label">Check-out Time:</span>
                <span class="value">{{ $checkOutTime }}</span>
            </div>
            <div class="info-row">
                <span class="label">Session Duration:</span>
                <span class="value">{{ $duration }}</span>
            </div>
        </div>
        
        <p style="text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px;">
            Keep up the great work! We look forward to seeing you again soon. 💪
        </p>
    </div>
    
    <div class="footer">
        <p style="margin: 0 0 10px 0;"><strong>Silencio Gym</strong></p>
        <p style="margin: 0; font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>

