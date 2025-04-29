<x-guest-layout>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Register</h2>
        
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

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <!-- Profile Picture -->
            <div>
                <label for="profile_picture" class="block text-gray-700 font-medium mb-1">{{ __('Profile Photo') }}</label>
                <div class="flex items-center">
                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden relative mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <img id="preview-image" src="#" alt="Preview" class="h-full w-full object-cover hidden">
                    </div>
                    <div class="flex-1">
                        <label for="profile_picture" class="cursor-pointer inline-block bg-gray-100 px-3 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-200">
                            Upload photo
                        </label>
                        <input id="profile_picture" type="file" name="profile_picture" class="hidden" accept="image/*" onchange="previewImage(this)">
                        <p class="text-xs text-gray-500 mt-1">Your profile photo</p>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
            </div>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" class="text-gray-700 font-medium" />
                <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md" 
                            type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md" 
                            type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            
            <!-- Phone Number -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" class="text-gray-700 font-medium" />
                <x-text-input id="phone" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md" 
                            type="text" name="phone" :value="old('phone')" required autocomplete="phone" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            
            <!-- Birthday -->
            <div>
                <x-input-label for="birthday" :value="__('Date of Birth')" class="text-gray-700 font-medium" />
                <x-text-input id="birthday" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md" 
                            type="date" name="birthday" :value="old('birthday')" required autocomplete="birthday" />
                <x-input-error :messages="$errors->get('birthday')" class="mt-2" />
            </div>

            <!-- Gender Selection -->
            <div>
                <label for="gender" class="block text-gray-700 font-medium">Gender</label>
                <select name="gender" id="gender" class="block w-full mt-1 border-gray-300 focus:border-black focus:ring-black rounded-md">
                    <option value="male" selected>Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            
            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-gray-700 font-medium">Register as</label>
                <select name="role" id="role" class="block w-full mt-1 border-gray-300 focus:border-black focus:ring-black rounded-md">
                    <option value="passenger" selected>Passenger</option>
                    <option value="driver">Driver</option>
                </select>
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 font-medium" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Terms of Service -->
            <div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black" required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-600">
                            I agree to the <a href="#" class="text-black underline">Terms of Service</a> and <a href="#" class="text-black underline">Privacy Policy</a>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Register Button -->
            <button type="submit" class="w-full bg-black text-white py-3 px-4 rounded-md font-medium hover:bg-gray-800 transition flex items-center justify-center">
                {{ __('Register') }}
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
            {{ __('Sign up with Google') }}
        </a>
        
        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-black font-medium hover:underline">Log in</a>
            </p>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const previewImg = document.getElementById('preview-image');
            const icon = input.parentElement.parentElement.querySelector('svg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    icon.classList.add('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-guest-layout>