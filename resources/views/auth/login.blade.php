<x-guest-layout>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Login to inTime</h2>
        
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md" 
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-black focus:ring-black" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="text-sm text-black hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition flex items-center justify-center">
                {{ __('Login') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.707-5.707a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293z" clip-rule="evenodd" />
                </svg>
            </button>
        </form>
        
        <!-- Divider -->
        <div class="flex items-center my-4">
            <div class="flex-grow bg-gray-300 h-px"></div>
            <span class="px-3 text-gray-500 text-sm">or</span>
            <div class="flex-grow bg-gray-300 h-px"></div>
        </div>
        
        <!-- Google Login -->
        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
            <img src="https://imagepng.org/wp-content/uploads/2019/08/google-icon.png" alt="Google Logo" class="w-5 h-5 mr-2">
            {{ __('Continue with Google') }}
        </a>
        
        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-black font-medium hover:underline">Register now</a>
            </p>
        </div>
    </div>
</x-guest-layout>