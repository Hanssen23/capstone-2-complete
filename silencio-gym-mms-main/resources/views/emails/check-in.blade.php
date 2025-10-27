<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Confirmation - Silencio Gym</title>
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
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
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
            border-left: 4px solid #059669;
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
        <h1>✅ Check-in Successful!</h1>
        <p style="margin: 10px 0 0 0; font-size: 16px;">Welcome to Silencio Gym</p>
    </div>
    
    <div class="content">
        <div class="success-icon">🏋️</div>
        
        <p style="font-size: 18px; text-align: center; margin: 0 0 20px 0;">
            Hello <strong>{{ $memberName }}</strong>!
        </p>
        
        <p style="text-align: center; color: #6b7280;">
            You have successfully checked in to Silencio Gym. Have a great workout!
        </p>
        
        <div class="info-box">
            <div class="info-row">
                <span class="label">Member Name:</span>
                <span class="value">{{ $memberName }}</span>
            </div>
            <div class="info-row">
                <span class="label">Check-in Time:</span>
                <span class="value">{{ $checkInTime }}</span>
            </div>
            <div class="info-row">
                <span class="label">Current Plan:</span>
                <span class="value">{{ $currentPlan }}</span>
            </div>
            <div class="info-row">
                <span class="label">Membership Expires:</span>
                <span class="value">{{ $membershipExpiry }}</span>
            </div>
        </div>
        
        <p style="text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px;">
            Stay hydrated and enjoy your workout! 💪
        </p>
    </div>
    
    <div class="footer">
        <p style="margin: 0 0 10px 0;"><strong>Silencio Gym</strong></p>
        <p style="margin: 0; font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>

