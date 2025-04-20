<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Driver Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <!-- Include Tailwind CSS -->
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-4 py-4 flex items-center justify-between bg-white shadow-sm">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="" class="font-medium">Home</a>
                <a href="" class="font-medium">About</a>
                <a href="" class="font-medium">Services</a>
                <a href="" class="font-medium">Contact</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            @if(Auth::check())
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden">
                @if(Auth::user()->profile_picture)
                    <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                @else
                    <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                @endif
            </div>
            @else
            <a href="{{ route('login') }}" class="font-medium">Login</a>
            <a href="{{ route('register') }}" class="font-medium px-4 py-2 bg-black text-white rounded-md">Register</a>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Become an inTime Driver</h1>
            </div>
            
            <!-- Registration Form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('driver.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        
                        <!-- Hidden user_id field -->
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        
                        <!-- Driver Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Driver Information</h3>
                            
                            <!-- License Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">License Number</label>
                                    <input type="text" id="license_number" name="license_number" placeholder="Enter your license number" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('license_number') border-red-500 @enderror">
                                    @error('license_number')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="license_expiry" class="block text-sm font-medium text-gray-700 mb-1">License Expiry Date</label>
                                    <input type="text" id="license_expiry" name="license_expiry" placeholder="Select date" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('license_expiry') border-red-500 @enderror">
                                    @error('license_expiry')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- License Photo -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">License Photo</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <div class="flex flex-col items-center justify-center pt-7">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <p class="pt-1 text-sm text-gray-400">Upload a photo of your driver's license</p>
                                        </div>
                                        <input type="file" name="license_photo" class="hidden">
                                    </label>
                                </div>
                                @error('license_photo')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Insurance Document -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Document</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <div class="flex flex-col items-center justify-center pt-7">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <p class="pt-1 text-sm text-gray-400">Upload your insurance document</p>
                                        </div>
                                        <input type="file" name="insurance_document" class="hidden">
                                    </label>
                                </div>
                                @error('insurance_document')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Certificate of Good Conduct -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Certificate of Good Conduct</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <div class="flex flex-col items-center justify-center pt-7">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <p class="pt-1 text-sm text-gray-400">Upload your certificate of good conduct</p>
                                        </div>
                                        <input type="file" name="good_conduct_certificate" class="hidden">
                                    </label>
                                </div>
                                @error('good_conduct_certificate')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Vehicle Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Vehicle Information</h3>
                            
                            <!-- Vehicle Type -->
                            <div class="mb-4">
                                <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                                <select id="vehicle_type" name="type" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('type') border-red-500 @enderror">
                                    <option value="basic">Basic</option>
                                    <option value="comfort">Comfort</option>
                                    <option value="black">Black (Premium)</option>
                                    <option value="wav">WAV (Wheelchair Accessible)</option>
                                </select>
                                @error('type')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Vehicle Photo -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Photo</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <div class="flex flex-col items-center justify-center pt-7">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <p class="pt-1 text-sm text-gray-400">Upload a photo of your vehicle</p>
                                        </div>
                                        <input type="file" name="vehicle_photo" class="hidden">
                                    </label>
                                </div>
                                @error('vehicle_photo')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Vehicle Make and Model -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="make" class="block text-sm font-medium text-gray-700 mb-1">Make</label>
                                    <input type="text" id="make" name="make" placeholder="e.g. Toyota" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('make') border-red-500 @enderror">
                                    @error('make')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                    <input type="text" id="model" name="model" placeholder="e.g. Camry" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('model') border-red-500 @enderror">
                                    @error('model')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Year and Color -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                    <input type="number" id="year" name="year" placeholder="e.g. 2020" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('year') border-red-500 @enderror">
                                    @error('year')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                    <input type="text" id="color" name="color" placeholder="e.g. Black" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('color') border-red-500 @enderror">
                                    @error('color')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Plate Number and Capacity -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-1">Plate Number</label>
                                    <input type="text" id="plate_number" name="plate_number" placeholder="e.g. ABC123" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('plate_number') border-red-500 @enderror">
                                    @error('plate_number')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                                    <select id="capacity" name="capacity" class="block w-full py-3 px-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('capacity') border-red-500 @enderror">
                                        <option value="1">1 person</option>
                                        <option value="2">2 people</option>
                                        <option value="3">3 people</option>
                                        <option value="4">4 people</option>
                                        <option value="5">5 people</option>
                                        <option value="6">6 people</option>
                                    </select>
                                    @error('capacity')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Insurance and Registration Expiry -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="insurance_expiry" class="block text-sm font-medium text-gray-700 mb-1">Insurance Expiry</label>
                                    <input type="text" id="insurance_expiry" name="insurance_expiry" placeholder="Select date" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('insurance_expiry') border-red-500 @enderror">
                                    @error('insurance_expiry')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="registration_expiry" class="block text-sm font-medium text-gray-700 mb-1">Registration Expiry</label>
                                    <input type="text" id="registration_expiry" name="registration_expiry" placeholder="Select date" class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-black focus:border-black @error('registration_expiry') border-red-500 @enderror">
                                    @error('registration_expiry')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Vehicle Features -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Features</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <div class="flex items-center">
                                        <input id="has_ac" name="features[]" value="ac" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded">
                                        <label for="has_ac" class="ml-2 text-sm text-gray-700">Air Conditioning</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="has_wifi" name="features[]" value="wifi" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded">
                                        <label for="has_wifi" class="ml-2 text-sm text-gray-700">WiFi</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="has_child_seat" name="features[]" value="child_seat" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded">
                                        <label for="has_child_seat" class="ml-2 text-sm text-gray-700">Child Seat</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="has_usb_charger" name="features[]" value="usb_charger" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded">
                                        <label for="has_usb_charger" class="ml-2 text-sm text-gray-700">USB Charger</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="has_pet_friendly" name="features[]" value="pet_friendly" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded">
                                        <label for="has_pet_friendly" class="ml-2 text-sm text-gray-700">Pet Friendly</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="has_luggage_carrier" name="features[]" value="luggage_carrier" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded">
                                        <label for="has_luggage_carrier" class="ml-2 text-sm text-gray-700">Luggage Carrier</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" name="terms" type="checkbox" class="focus:ring-black h-4 w-4 text-black border-gray-300 rounded @error('terms') border-red-500 @enderror">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700">I agree to the <a href="#" class="text-black underline">Terms and Conditions</a> and <a href="#" class="text-black underline">Privacy Policy</a></label>
                                </div>
                            </div>
                            @error('terms')
                                <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="text-right">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                Register as Driver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white py-6 mt-8">
        <div class="container mx-auto px-4">
            <p class="text-center text-gray-500 text-sm">© 2025 inTime. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            flatpickr("#license_expiry", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            
            flatpickr("#insurance_expiry", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            
            flatpickr("#registration_expiry", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
        });
    </script>
</body>
</html>