<x-layout>
    <div class="flex items-center justify-center h-screen w-screen">
        <div class="flex items-center justify-center w-450 h-250 rounded-lg shadow-lg overflow-hidden">
            <div class="flex flex-col flex-1 justify-center items-center gap-5">
                <h1 class="text-5xl font-bold">Silencio System</h1>
                <h2 class="text-[16px] text-gray-500">Log in to continue</h2>
                
                @if ($errors->any())
                    <div class="w-full max-w-sm bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="w-full max-w-sm bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="w-full max-w-sm bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login.post') }}" class="flex flex-col w-100 mx-auto">
                    @csrf
                    <div class="mb-5">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Your email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@example.com" required />
                    </div>
                    <div class="mb-5">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Your password</label>
                        <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    </div>
                    <div class="flex items-start mb-5">
                        <a href="{{ route('member.register') }}" class="text-sm text-blue-600 hover:underline mr-4">Sign up</a>
                        <div class="flex items-center h-5">
                            <input id="remember" name="remember" type="checkbox" value="1" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" />
                        </div>
                        <label for="remember" class="ms-2 text-sm font-medium text-gray-500">Remember me</label>
                    </div>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login</button>
                </form>
            </div>
            <div class="flex-2">
                <img class="object-cover w-full h-full rounded-r-lg" src="{{ asset('images/gym-image.png') }}" alt="Gym Image">
            </div>
        </div>
    </div>
</x-layout>