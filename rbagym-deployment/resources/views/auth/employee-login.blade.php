<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login - Silencio System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('{{ asset("images/gym-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 lg:p-10 w-full max-w-sm sm:max-w-md lg:max-w-lg mx-auto">
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Silencio System</h1>
            <p class="text-sm sm:text-base text-gray-600">Employee Login</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-xs sm:text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('employee.auth.login') }}">
            @csrf
            
            <div class="mb-4 sm:mb-6">
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Your email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xs sm:text-sm min-h-[40px] sm:min-h-[44px]"
                    placeholder="name@example.com"
                    required
                >
            </div>

            <div class="mb-4 sm:mb-6">
                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Your password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xs sm:text-sm min-h-[40px] sm:min-h-[44px]"
                    required
                >
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-2 sm:gap-0">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-3 w-3 sm:h-4 sm:w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-xs sm:text-sm text-gray-700">Remember me</label>
                </div>
                <a href="#" class="text-xs sm:text-sm text-blue-600 hover:text-blue-500 text-center sm:text-right">Sign up</a>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 sm:py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 text-sm sm:text-base font-medium min-h-[40px] sm:min-h-[44px]"
            >
                Login
            </button>
        </form>

        <div class="mt-4 sm:mt-6 text-center">
            <p class="text-xs sm:text-sm text-gray-600 flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-1">
                <a href="{{ route('login.show') }}" class="text-blue-600 hover:text-blue-500">Admin Login</a>
                <span class="hidden sm:inline">|</span>
                <a href="{{ route('member.register') }}" class="text-blue-600 hover:text-blue-500">Member Registration</a>
            </p>
        </div>
    </div>
</body>
</html>
