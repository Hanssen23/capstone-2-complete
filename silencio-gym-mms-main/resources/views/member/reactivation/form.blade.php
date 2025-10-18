<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reactivate Your Account - Silencio Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .reactivation-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .reactivation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .card-body {
            padding: 40px;
        }
        .member-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .btn-reactivate {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-reactivate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reactivation-container">
            <div class="reactivation-card">
                <div class="card-header">
                    <h1><i class="fas fa-user-check"></i> Reactivate Your Account</h1>
                    <p class="mb-0">Welcome back to Silencio Gym!</p>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="warning-box">
                        <h5><i class="fas fa-exclamation-triangle text-warning"></i> Account Scheduled for Deletion</h5>
                        <p class="mb-0">Your account is currently scheduled for automatic deletion due to inactivity. By reactivating your account, you can continue using all Silencio Gym services.</p>
                    </div>

                    <div class="member-info">
                        <h5><i class="fas fa-user"></i> Account Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> {{ $member->full_name }}<br>
                                <strong>Member Number:</strong> {{ $member->member_number }}<br>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $member->email }}<br>
                                <strong>Status:</strong> <span class="badge bg-warning">{{ ucfirst($member->status) }}</span>
                            </div>
                        </div>
                        
                        @if($member->deletion_warning_sent_at)
                            <div class="mt-3">
                                <strong>First Warning Sent:</strong> {{ $member->deletion_warning_sent_at->format('F j, Y \a\t g:i A') }}
                            </div>
                        @endif
                        
                        @if($member->final_warning_sent_at)
                            <div class="mt-2">
                                <strong>Final Warning Sent:</strong> {{ $member->final_warning_sent_at->format('F j, Y \a\t g:i A') }}
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('member.reactivate.process', $member) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <h5><i class="fas fa-check-circle"></i> Reactivation Confirmation</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confirm_reactivation" id="confirm_reactivation" value="1" required>
                                <label class="form-check-label" for="confirm_reactivation">
                                    <strong>Yes, I want to reactivate my Silencio Gym account</strong>
                                </label>
                            </div>
                            @error('confirm_reactivation')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="contact_reason" class="form-label">
                                <i class="fas fa-comment"></i> Additional Comments (Optional)
                            </label>
                            <textarea class="form-control" id="contact_reason" name="contact_reason" rows="3" 
                                placeholder="Let us know if you have any questions or if there's anything we can help you with..."></textarea>
                            <div class="form-text">This information helps us improve our service.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-reactivate">
                                <i class="fas fa-user-check"></i> Reactivate My Account
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            After reactivation, you can log in to your member portal and continue using all gym services.
                        </p>
                        <a href="{{ route('login.show') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-in-alt"></i> Go to Login Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
