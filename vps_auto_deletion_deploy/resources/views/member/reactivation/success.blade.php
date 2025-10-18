<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Reactivated - Silencio Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        .card-body {
            padding: 40px;
        }
        .member-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .btn-login {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        .next-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-card">
                <div class="card-header">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1>Account Successfully Reactivated!</h1>
                    <p class="mb-0">Welcome back to Silencio Gym, {{ $member->first_name }}!</p>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-thumbs-up"></i> Great News!</h5>
                        <p class="mb-0">Your account has been successfully reactivated and is now fully active. You can continue using all Silencio Gym services.</p>
                    </div>

                    <div class="member-info">
                        <h5><i class="fas fa-user-check"></i> Account Status</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> {{ $member->full_name }}<br>
                                <strong>Member Number:</strong> {{ $member->member_number }}<br>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $member->email }}<br>
                                <strong>Status:</strong> <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <strong>Reactivated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}
                        </div>
                    </div>

                    <div class="next-steps">
                        <h5><i class="fas fa-list-check"></i> What's Next?</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-sign-in-alt text-primary"></i> <strong>Log in to your member portal</strong> to access your dashboard</li>
                            <li class="mb-2"><i class="fas fa-id-card text-primary"></i> <strong>Use your RFID card</strong> to check in at the gym</li>
                            <li class="mb-2"><i class="fas fa-dumbbell text-primary"></i> <strong>Enjoy all gym facilities</strong> and services</li>
                            <li class="mb-2"><i class="fas fa-calendar text-primary"></i> <strong>Check your membership status</strong> and renewal dates</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('login.show') }}" class="btn btn-primary btn-login">
                            <i class="fas fa-sign-in-alt"></i> Log In to Member Portal
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            <i class="fas fa-heart text-danger"></i> 
                            Thank you for choosing Silencio Gym! We're excited to have you back.
                        </p>
                        <p class="text-muted">
                            <i class="fas fa-question-circle"></i> 
                            Questions? Contact us at the gym or via email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
