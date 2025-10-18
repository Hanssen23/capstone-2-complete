<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set/Reset Password - Silencio Gym</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <i class="fas fa-key text-6xl text-blue-600 mb-4"></i>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Set/Reset Password</h1>
            <p class="text-gray-600">Enter your email address and we'll send you a password setup/reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('member.password.email') }}" class="mb-6">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                <i class="fas fa-paper-plane mr-2"></i>
                Send Password Setup/Reset Link
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login.show') }}" 
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <i class="fas fa-arrow-left mr-1"></i>
                Back to Login
            </a>
        </div>
    </div>
</body>
</html>
