<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silencio Gym Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 60px;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .status {
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        .info {
            background: #f3f4f6;
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
        }
        .value {
            color: #111827;
        }
        .buttons {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-secondary {
            background: #10b981;
            color: white;
        }
        .footer {
            margin-top: 30px;
            color: #6b7280;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏋️ Silencio Gym</h1>
        <h2>Management System</h2>
        
        <div class="status">
            ✅ System Online
        </div>
        
        <div class="info">
            <div class="info-item">
                <span class="label">Status:</span>
                <span class="value">Operational</span>
            </div>
            <div class="info-item">
                <span class="label">Laravel:</span>
                <span class="value">{{ app()->version() }}</span>
            </div>
            <div class="info-item">
                <span class="label">PHP:</span>
                <span class="value">{{ PHP_VERSION }}</span>
            </div>
            <div class="info-item">
                <span class="label">Environment:</span>
                <span class="value">{{ config('app.env') }}</span>
            </div>
            <div class="info-item">
                <span class="label">RFID Endpoint:</span>
                <span class="value">/rfid-test.php</span>
            </div>
        </div>
        
        <div class="buttons">
            <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
            <a href="/login" class="btn btn-secondary">Login</a>
        </div>
        
        <div class="footer">
            <p>Deployed: {{ date('F j, Y') }}</p>
            <p>VPS: 156.67.221.184</p>
        </div>
    </div>
</body>
</html>

