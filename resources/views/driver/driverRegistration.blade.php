<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Driver Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <!-- Include Tailwind CSS -->
    @vite('resources/css/app.css')
    <!-- Add custom fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .progress-step {
            transition: all 0.3s ease;
        }
        .progress-step.active {
            background-color: black;
            color: white;
        }
        .upload-area:hover {
            border-color: #000;
            background-color: rgba(0, 0, 0, 0.02);
        }
        .custom-checkbox:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        }
        .image-preview {
            max-width: 100%;
            max-height: 150px;
            display: block;
            margin: 10px auto 0;
            border-radius: 6px;
        }
        .preview-container {
            position: relative;
            margin-top: 10px;
        }
        .remove-preview {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="px-6 py-4 flex items-center justify-between bg-white shadow-sm sticky top-0 z-50">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="" class="text-2xl font-bold">inTime</a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-6">
                <a href="" class="font-medium hover:text-black transition duration-150">Home</a>
                <a href="" class="font-medium hover:text-black transition duration-150">About</a>
                <a href="" class="font-medium hover:text-black transition duration-150">Services</a>
                <a href="" class="font-medium hover:text-black transition duration-150">Contact</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            @if(Auth::check())
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden shadow-sm">
                @if(Auth::user()->profile_picture)
                    <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="Profile" class="h-full w-full object-cover">
                @else
                    <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
                @endif
            </div>
            @else
            <a href="{{ route('login') }}" class="font-medium text-gray-700 hover:text-black transition duration-150">Login</a>
            <a href="{{ route('register') }}" class="font-medium px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 transition duration-150">Register</a>
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative bg-black text-white py-16">
        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Drive with inTime</h1>
                <p class="text-xl md:text-2xl mb-8">Be your own boss, set your own schedule, and earn money on your terms.</p>
                <div class="flex space-x-4 items-center mb-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Flexible hours</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Weekly payments</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>24/7 support</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent opacity-90"></div>
        <!-- Background image would be here in a real implementation -->
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Step Progress -->
            <div class="flex justify-between items-center mb-10 px-6">
                <div class="flex items-center">
                    <div class="progress-step active flex items-center justify-center h-10 w-10 rounded-full text-white bg-black font-medium" data-step="1">1</div>
                    <div class="h-1 w-16 bg-black"></div>
                </div>
                <div class="flex items-center">
                    <div class="progress-step flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 font-medium" data-step="2">2</div>
                    <div class="h-1 w-16 bg-gray-200"></div>
                </div>
                <div class="flex items-center">
                    <div class="progress-step flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 font-medium" data-step="3">3</div>
                </div>
            </div>
            
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold mb-3">Join Our Driver Network</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Complete your driver profile to get started with inTime. The more information you provide, the faster we can verify your account.</p>
            </div>
            
            <!-- Registration Form -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <form action="{{ route('driver.store') }}" method="POST" enctype="multipart/form-data" id="driver-form">
                        @csrf
                        @method('post')
                        
                        <!-- Hidden user_id field -->
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        
                        <!-- Driver Information -->
                        <div class="mb-10 form-step" id="step-1">
                            <h3 class="text-xl font-semibold mb-6 flex items-center">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full bg-black text-white text-sm mr-3">1</span>
                                Driver Information
                            </h3>
                            
                            <!-- License Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                                    <input type="text" id="license_number" name="license_number" placeholder="Enter your license number" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('license_number') border-red-500 @enderror">
                                    @error('license_number')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="license_expiry" class="block text-sm font-medium text-gray-700 mb-2">License Expiry Date</label>
                                    <div class="relative">
                                        <input type="text" id="license_expiry" name="license_expiry" placeholder="Select date" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('license_expiry') border-red-500 @enderror">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('license_expiry')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- License Photo -->
                            <div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">License Photo</label>
    <div class="flex items-center justify-center w-full">
        <div class="upload-area flex flex-col w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
            <div class="flex flex-col items-center justify-center pt-7 upload-content">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <p class="mb-1 font-medium text-gray-900">Upload driver's license</p>
                <p class="text-sm text-gray-500">PNG, JPG up to 10MB</p>
            </div>
            <div class="preview-container hidden">
                <img class="image-preview mx-auto my-3 max-h-24 rounded" src="" alt="License preview">
                <button type="button" class="remove-preview absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">×</button>
            </div>
            <input type="file" name="license_photo" class="hidden file-input" accept="image/*">
        </div>
    </div>
    @error('license_photo')
        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
    @enderror
</div>
                            
                            <!-- Document Upload Section with Columns -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Insurance Document -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Document</label>
                                    <div class="flex items-center justify-center w-full">
                                        <label class="upload-area flex flex-col w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                            <div class="flex flex-col items-center justify-center pt-7 upload-content">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="mb-1 font-medium text-gray-900">Upload insurance</p>
                                                <p class="text-sm text-gray-500">PDF, JPG up to 10MB</p>
                                            </div>
                                            <div class="preview-container hidden">
                                                <img class="image-preview" src="" alt="Insurance preview">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                            <input type="file" name="insurance_document" class="hidden file-input">
                                        </label>
                                    </div>
                                    @error('insurance_document')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Certificate of Good Conduct -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Certificate of Good Conduct</label>
                                    <div class="flex items-center justify-center w-full">
                                        <label class="upload-area flex flex-col w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                            <div class="flex flex-col items-center justify-center pt-7 upload-content">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                <p class="mb-1 font-medium text-gray-900">Upload certificate</p>
                                                <p class="text-sm text-gray-500">PDF, JPG up to 10MB</p>
                                            </div>
                                            <div class="preview-container hidden">
                                                <img class="image-preview" src="" alt="Certificate preview">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                            <input type="file" name="good_conduct_certificate" class="hidden file-input">
                                        </label>
                                    </div>
                                    @error('good_conduct_certificate')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Next Button -->
                            <div class="mt-8 text-right">
                                <button type="button" class="next-step px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                    Next: Vehicle Information
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block ml-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Vehicle Information -->
                        <div class="mb-10 form-step hidden" id="step-2">
                            <h3 class="text-xl font-semibold mb-6 flex items-center">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full bg-black text-white text-sm mr-3">2</span>
                                Vehicle Information
                            </h3>
                            
                            <!-- Vehicle Photo -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Photo</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="upload-area flex flex-col w-full h-48 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                        <div class="flex flex-col items-center justify-center pt-7 upload-content">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 17H3v-6l2-5h8l.5 1h3.5l2 5v6h-2m-12 0h10" />
                                            </svg>
                                            <p class="mb-1 font-medium text-gray-900">Upload vehicle photo</p>
                                            <p class="text-sm text-gray-500">A clear photo of your vehicle from the front</p>
                                        </div>
                                        <div class="preview-container hidden">
                                            <img class="image-preview" src="" alt="Vehicle preview">
                                            <button type="button" class="remove-preview">×</button>
                                        </div>
                                        <input type="file" name="vehicle_photo" class="hidden file-input">
                                    </label>
                                </div>
                                @error('vehicle_photo')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Vehicle Type -->
                            <div class="mb-6">
                                <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label class="relative rounded-lg border border-gray-300 overflow-hidden cursor-pointer hover:border-black transition duration-150 vehicle-type-label">
                                        <input type="radio" name="type" value="basic" class="absolute opacity-0 vehicle-type-radio">
                                        <div class="p-4 text-center h-full">
                                            <div class="mb-2 mx-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 17H3v-6l2-5h8l.5 1h3.5l2 5v6h-2m-12 0h10" />
                                                </svg>
                                            </div>
                                            <div class="font-medium">Basic</div>
                                            <div class="text-xs text-gray-500">Economy rides</div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative rounded-lg border border-gray-300 overflow-hidden cursor-pointer hover:border-red transition duration-150 vehicle-type-label">
                                        <input type="radio" name="type" value="comfort" class="absolute opacity-0 vehicle-type-radio">
                                        <div class="p-4 text-center h-full">
                                            <div class="mb-2 mx-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 17H3v-6l2-5h8l.5 1h3.5l2 5v6h-2m-12 0h10" />
                                                </svg>
                                            </div>
                                            <div class="font-medium">Comfort</div>
                                            <div class="text-xs text-gray-500">Extra legroom</div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative rounded-lg border border-gray-300 overflow-hidden cursor-pointer hover:border-black transition duration-150 vehicle-type-label">
                                        <input type="radio" name="type" value="black" class="absolute opacity-0 vehicle-type-radio">
                                        <div class="p-4 text-center h-full">
                                            <div class="mb-2 mx-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 17H3v-6l2-5h8l.5 1h3.5l2 5v6h-2m-12 0h10" />
                                                </svg>
                                            </div>
                                            <div class="font-medium">Black</div>
                                            <div class="text-xs text-gray-500">Premium rides</div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative rounded-lg border border-gray-300 overflow-hidden cursor-pointer hover:border-black transition duration-150 vehicle-type-label">
                                        <input type="radio" name="type" value="wav" class="absolute opacity-0 vehicle-type-radio">
                                        <div class="p-4 text-center h-full">
                                            <div class="mb-2 mx-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 17H3v-6l2-5h8l.5 1h3.5l2 5v6h-2m-12 0h10" />
                                                </svg>
                                            </div>
                                            <div class="font-medium">WAV</div>
                                            <div class="text-xs text-gray-500">Wheelchair accessible</div>
                                        </div>
                                    </label>
                                </div>
                                @error('type')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Vehicle Make and Model -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="make" class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                                    <input type="text" id="make" name="make" placeholder="e.g. Toyota" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('make') border-red-500 @enderror">
                                    @error('make')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                                    <input type="text" id="model" name="model" placeholder="e.g. Camry" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('model') border-red-500 @enderror">
                                    @error('model')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Year and Color -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                    <input type="number" id="year" name="year" placeholder="e.g. 2020" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('year') border-red-500 @enderror">
                                    @error('year')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                   @enderror
                               </div>
                               <div>
                                   <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                   <input type="text" id="color" name="color" placeholder="e.g. Black" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('color') border-red-500 @enderror">
                                   @error('color')
                                       <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                   @enderror
                               </div>
                           </div>
                           
                           <!-- Plate Number and Capacity -->
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                               <div>
                                   <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                                   <input type="text" id="plate_number" name="plate_number" placeholder="e.g. ABC123" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('plate_number') border-red-500 @enderror">
                                   @error('plate_number')
                                       <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                   @enderror
                               </div>
                               <div>
                                   <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                                   <select id="capacity" name="capacity" class="block w-full py-3 px-4 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('capacity') border-red-500 @enderror">
                                       <option value="">Select capacity</option>
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
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                               <div>
                                   <label for="insurance_expiry" class="block text-sm font-medium text-gray-700 mb-2">Insurance Expiry</label>
                                   <div class="relative">
                                       <input type="text" id="insurance_expiry" name="insurance_expiry" placeholder="Select date" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('insurance_expiry') border-red-500 @enderror">
                                       <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                           </svg>
                                       </div>
                                   </div>
                                   @error('insurance_expiry')
                                       <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                   @enderror
                               </div>
                               <div>
                                   <label for="registration_expiry" class="block text-sm font-medium text-gray-700 mb-2">Registration Expiry</label>
                                   <div class="relative">
                                       <input type="text" id="registration_expiry" name="registration_expiry" placeholder="Select date" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-black focus:border-black transition duration-150 @error('registration_expiry') border-red-500 @enderror">
                                       <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                           </svg>
                                       </div>
                                   </div>
                                   @error('registration_expiry')
                                       <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                   @enderror
                               </div>
                           </div>
                           
                           <!-- Vehicle Features -->
                           <div class="mb-6">
                               <label class="block text-sm font-medium text-gray-700 mb-4">Vehicle Features</label>
                               <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                   <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition duration-150">
                                       <input id="has_ac" name="features[]" value="ac" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox">
                                       <label for="has_ac" class="ml-3 text-sm text-gray-700 font-medium">Air Conditioning</label>
                                   </div>
                                   <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition duration-150">
                                       <input id="has_wifi" name="features[]" value="wifi" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox">
                                       <label for="has_wifi" class="ml-3 text-sm text-gray-700 font-medium">WiFi</label>
                                   </div>
                                   <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition duration-150">
                                       <input id="has_child_seat" name="features[]" value="child_seat" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox">
                                       <label for="has_child_seat" class="ml-3 text-sm text-gray-700 font-medium">Child Seat</label>
                                   </div>
                                   <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition duration-150">
                                       <input id="has_usb_charger" name="features[]" value="usb_charger" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox">
                                       <label for="has_usb_charger" class="ml-3 text-sm text-gray-700 font-medium">USB Charger</label>
                                   </div>
                                   <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition duration-150">
                                       <input id="has_pet_friendly" name="features[]" value="pet_friendly" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox">
                                       <label for="has_pet_friendly" class="ml-3 text-sm text-gray-700 font-medium">Pet Friendly</label>
                                   </div>
                                   <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition duration-150">
                                       <input id="has_luggage_carrier" name="features[]" value="luggage_carrier" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox">
                                       <label for="has_luggage_carrier" class="ml-3 text-sm text-gray-700 font-medium">Luggage Carrier</label>
                                   </div>
                               </div>
                           </div>
                           
                           <!-- Navigation Buttons -->
                           <div class="mt-8 flex justify-between">
                               <button type="button" class="prev-step px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                       <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                   </svg>
                                   Previous: Driver Information
                               </button>
                               <button type="button" class="next-step px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                   Next: Additional Information
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block ml-1" viewBox="0 0 20 20" fill="currentColor">
                                       <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                   </svg>
                               </button>
                           </div>
                       </div>
                       
                       <!-- Additional Information & Terms -->
                       <div class="mb-10 form-step hidden" id="step-3">
                           <h3 class="text-xl font-semibold mb-6 flex items-center">
                               <span class="flex items-center justify-center h-8 w-8 rounded-full bg-black text-white text-sm mr-3">3</span>
                               Additional Information
                           </h3>
                           
                           <!-- Earning Potential -->
                           <div class="bg-gray-50 p-6 rounded-lg mb-8">
                               <h4 class="text-lg font-medium mb-4">Estimated Earning Potential</h4>
                               <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                   <div class="bg-white p-4 rounded-lg shadow-sm">
                                       <div class="text-2xl font-bold text-black mb-1">$700-900</div>
                                       <div class="text-sm text-gray-500">Weekly (20-30 hours)</div>
                                   </div>
                                   <div class="bg-white p-4 rounded-lg shadow-sm">
                                       <div class="text-2xl font-bold text-black mb-1">$1,400-1,800</div>
                                       <div class="text-sm text-gray-500">Weekly (40-50 hours)</div>
                                   </div>
                                   <div class="bg-white p-4 rounded-lg shadow-sm">
                                       <div class="text-2xl font-bold text-black mb-1">$25-35</div>
                                       <div class="text-sm text-gray-500">Average hourly</div>
                                   </div>
                               </div>
                               <p class="text-xs text-gray-500 mt-2">*Earnings vary based on location, hours, and demand</p>
                           </div>
                           
                           <!-- Terms and Conditions -->
                           <div class="border border-gray-200 rounded-lg p-6 mb-6">
                               <h4 class="text-lg font-medium mb-4">Terms and Conditions</h4>
                               <div class="mb-4 h-40 overflow-y-auto bg-gray-50 p-4 rounded text-sm text-gray-700">
                                   <p class="mb-3">By registering to become an inTime driver, you agree to the following terms and conditions:</p>
                                   <p class="mb-2">1. You must maintain a valid driver's license and insurance at all times.</p>
                                   <p class="mb-2">2. Your vehicle must meet all safety and maintenance requirements.</p>
                                   <p class="mb-2">3. You agree to complete all necessary background checks.</p>
                                   <p class="mb-2">4. inTime takes a commission on each completed ride as specified in the payment terms.</p>
                                   <p class="mb-2">5. You are responsible for any taxes on your earnings.</p>
                                   <p class="mb-2">6. inTime reserves the right to deactivate your account for policy violations.</p>
                                   <p class="mb-2">7. You agree to maintain a minimum rating as specified in our driver guidelines.</p>
                                   <p class="mb-2">8. You understand that you are an independent contractor, not an employee of inTime.</p>
                                   <p>9. You must comply with all local transportation regulations and laws.</p>
                               </div>
                               <div class="flex items-start">
                                   <div class="flex items-center h-5">
                                       <input id="terms" name="terms" type="checkbox" class="focus:ring-black h-5 w-5 text-black border-gray-300 rounded custom-checkbox @error('terms') border-red-500 @enderror">
                                   </div>
                                   <div class="ml-3 text-sm">
                                       <label for="terms" class="font-medium text-gray-700">I have read and agree to the <a href="#" class="text-black underline">Terms and Conditions</a> and <a href="#" class="text-black underline">Privacy Policy</a></label>
                                   </div>
                               </div>
                               @error('terms')
                                   <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                               @enderror
                           </div>
                           
                           <!-- Driver Benefits -->
                           <div class="bg-black text-white rounded-lg p-6 mb-6">
                               <h4 class="text-lg font-medium mb-4">Why Drive with inTime?</h4>
                               <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                   <div class="flex items-start">
                                       <div class="mr-3 mt-1">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                           </svg>
                                       </div>
                                       <div>
                                           <h5 class="font-medium mb-1">Flexible Earnings</h5>
                                           <p class="text-sm text-gray-300">Get paid weekly with instant cashout options</p>
                                       </div>
                                   </div>
                                   <div class="flex items-start">
                                       <div class="mr-3 mt-1">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                           </svg>
                                       </div>
                                       <div>
                                           <h5 class="font-medium mb-1">Work Anytime</h5>
                                           <p class="text-sm text-gray-300">Set your own schedule, drive when you want</p>
                                       </div>
                                   </div>
                                   <div class="flex items-start">
                                       <div class="mr-3 mt-1">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                           </svg>
                                       </div>
                                       <div>
                                           <h5 class="font-medium mb-1">Insurance Coverage</h5>
                                           <p class="text-sm text-gray-300">Protection on every trip while the app is on</p>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           
                           <!-- Navigation Buttons and Submit -->
                           <div class="mt-8 flex justify-between">
                               <button type="button" class="prev-step px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                       <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                   </svg>
                                   Previous: Vehicle Information
                               </button>
                               <button type="submit" class="px-8 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black shadow-lg">
                                   <span class="mr-2">Register as Driver</span>
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                       <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                   </svg>
                               </button>
                           </div>
                       </div>
                   </form>
               </div>
           </div>
           
           <!-- FAQ Section -->
           <div class="mt-16">
               <h2 class="text-2xl font-bold mb-8 text-center">Frequently Asked Questions</h2>
               <div class="max-w-3xl mx-auto">
                   <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                       <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                           <span>How long does the approval process take?</span>
                           <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                               <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                           </svg>
                       </button>
                       <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                           <p class="text-gray-700">The approval process typically takes 24-48 hours after all your documents have been submitted. Our team reviews your application to ensure everything meets our requirements. You'll receive an email notification once your application has been approved.</p>
                       </div>
                   </div>
                   
                   <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                       <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                           <span>What documents do I need to provide?</span>
                           <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                               <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                           </svg>
                       </button>
                       <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                           <p class="text-gray-700">You'll need to provide a valid driver's license, vehicle registration, proof of insurance, and a certificate of good conduct. All documents must be current and not expired. Additionally, you'll need to upload clear photos of your vehicle.</p>
                       </div>
                   </div>
                   
                   <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                       <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                           <span>How do I get paid?</span>
                           <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                               <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                           </svg>
                       </button>
                       <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                           <p class="text-gray-700">Payments are processed weekly and deposited directly to your bank account. We also offer an instant cashout feature that allows you to access your earnings immediately for a small fee. You can track all your earnings in real-time through the driver app.</p>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </main>
   
   <!-- Footer -->
   <footer class="bg-white py-12 border-t border-gray-200">
       <div class="container mx-auto px-6">
           <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
               <div>
                   <h3 class="text-lg font-bold mb-4">inTime</h3>
                   <p class="text-gray-600 mb-4">Modern ride-sharing for drivers and passengers.</p>
                   <div class="flex space-x-4">
                       <a href="#" class="text-gray-400 hover:text-gray-500">
                           <span class="sr-only">Facebook</span>
                           <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                               <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                           </svg>
                       </a>
                       <a href="#" class="text-gray-400 hover:text-gray-500">
                           <span class="sr-only">Instagram</span>
                           <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                               <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0011.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                           </svg>
                       </a>
                       <a href="#" class="text-gray-400 hover:text-gray-500">
                           <span class="sr-only">Twitter</span>
                           <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                               <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                           </svg>
                       </a>
                   </div>
               </div>
               
               <div>
                   <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Company</h3>
                   <ul class="space-y-3">
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">About</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Careers</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Press</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Blog</a></li>
                   </ul>
               </div>
               
               <div>
                   <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Support</h3>
                   <ul class="space-y-3">
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Help Center</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Safety Center</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Community Guidelines</a></li>
                   </ul>
               </div>
               
               <div>
                   <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Legal</h3>
                   <ul class="space-y-3">
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Privacy Policy</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Terms of Service</a></li>
                       <li><a href="#" class="text-base text-gray-600 hover:text-gray-900">Cookie Policy</a></li>
                   </ul>
               </div>
           </div>
           <div class="border-t border-gray-200 pt-8">
               <p class="text-center text-gray-500 text-sm">&copy; 2025 inTime. All rights reserved.</p>
           </div>
       </div>
   </footer>
   
   <!-- JavaScript -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
   <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr("#license_expiry, #insurance_expiry, #registration_expiry", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });
    
    // File input preview functionality
    const fileInputs = document.querySelectorAll('.file-input');
    
    fileInputs.forEach(input => {
        // Find the related elements
        const uploadArea = input.closest('.upload-area');
        const uploadContent = uploadArea.querySelector('.upload-content');
        const previewContainer = uploadArea.querySelector('.preview-container');
        const previewImage = previewContainer.querySelector('.image-preview');
        const removeBtn = previewContainer.querySelector('.remove-preview');
        
        // Add change event to input
        input.addEventListener('change', function(event) {
            if (this.files && this.files[0]) {
                // Create file reader to display preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    uploadContent.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Add click event to remove button
        removeBtn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation(); // Prevent event bubbling
            input.value = '';
            uploadContent.classList.remove('hidden');
            previewContainer.classList.add('hidden');
        });
    });
    
    // Make the entire upload area clickable to trigger file input
    document.querySelectorAll('.upload-area').forEach(area => {
        area.addEventListener('click', function(event) {
            // Don't trigger if clicking on the remove button
            if (!event.target.closest('.remove-preview')) {
                const fileInput = this.querySelector('.file-input');
                fileInput.click();
            }
        });
    });
    
    // Multi-step form navigation
    const steps = document.querySelectorAll('.form-step');
    const progressSteps = document.querySelectorAll('.progress-step');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    
    // Next button click event
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = this.closest('.form-step');
            const currentStepIndex = Array.from(steps).indexOf(currentStep);
            const nextStepIndex = currentStepIndex + 1;
            
            if (nextStepIndex < steps.length) {
                // Hide current step
                steps.forEach(step => step.classList.add('hidden'));
                
                // Show next step
                steps[nextStepIndex].classList.remove('hidden');
                
                // Update progress indicators
                updateProgressSteps(nextStepIndex);
                
                // Scroll to top of the form
                window.scrollTo({
                    top: document.querySelector('.progress-step').offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Previous button click event
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = this.closest('.form-step');
            const currentStepIndex = Array.from(steps).indexOf(currentStep);
            const prevStepIndex = currentStepIndex - 1;
            
            if (prevStepIndex >= 0) {
                // Hide current step
                steps.forEach(step => step.classList.add('hidden'));
                
                // Show previous step
                steps[prevStepIndex].classList.remove('hidden');
                
                // Update progress indicators
                updateProgressSteps(prevStepIndex);
                
                // Scroll to top of the form
                window.scrollTo({
                    top: document.querySelector('.progress-step').offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Update progress step indicators
    function updateProgressSteps(activeIndex) {
        progressSteps.forEach((step, index) => {
            const line = step.nextElementSibling;
            
            if (index <= activeIndex) {
                step.classList.add('active');
                if (line && index < activeIndex) {
                    line.classList.add('bg-black');
                    line.classList.remove('bg-gray-200');
                }
            } else {
                step.classList.remove('active');
                if (line) {
                    line.classList.remove('bg-black');
                    line.classList.add('bg-gray-200');
                }
            }
        });
    }
    
    // Vehicle type selection
    const vehicleTypeRadios = document.querySelectorAll('.vehicle-type-radio');
    vehicleTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected style from all
            document.querySelectorAll('.vehicle-type-label').forEach(label => {
                label.classList.remove('border-black', 'bg-gray-50');
            });
            
            // Add selected style to checked
            if (this.checked) {
                this.closest('.vehicle-type-label').classList.add('border-black', 'bg-gray-50');
            }
        });
    });
    
    // FAQ toggles
    const faqToggles = document.querySelectorAll('.faq-toggle');
    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.faq-icon');
            
            // Toggle content visibility
            content.classList.toggle('hidden');
            
            // Update aria-expanded
            const isExpanded = content.classList.contains('hidden') ? 'false' : 'true';
            this.setAttribute('aria-expanded', isExpanded);
            
            // Update icon
            if (isExpanded === 'true') {
                icon.innerHTML = '<path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />';
            } else {
                icon.innerHTML = '<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />';
            }
        });
    });
    
    // Form validation before submission
    const driverForm = document.getElementById('driver-form');
    if (driverForm) {
        driverForm.addEventListener('submit', function(e) {
            let hasError = false;
            
            // Check required fields in the current visible step
            const currentStep = document.querySelector('.form-step:not(.hidden)');
            const requiredInputs = currentStep.querySelectorAll('input[required], select[required]');
            
            requiredInputs.forEach(input => {
                if (!input.value) {
                    input.classList.add('border-red-500');
                    hasError = true;
                    
                    // Add error message if not already present
                    const errorId = input.id + '-error';
                    if (!document.getElementById(errorId)) {
                        const errorMsg = document.createElement('div');
                        errorMsg.id = errorId;
                        errorMsg.className = 'text-red-500 mt-1 text-sm';
                        errorMsg.textContent = 'This field is required';
                        input.parentNode.appendChild(errorMsg);
                    }
                } else {
                    input.classList.remove('border-red-500');
                    const errorId = input.id + '-error';
                    const errorMsg = document.getElementById(errorId);
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Check if terms checkbox is checked in final step
            if (currentStep.id === 'step-3') {
                const termsCheckbox = document.getElementById('terms');
                if (termsCheckbox && !termsCheckbox.checked) {
                    const termsParent = termsCheckbox.closest('div.flex').parentNode;
                    
                    const errorId = 'terms-error';
                    if (!document.getElementById(errorId)) {
                        const errorMsg = document.createElement('div');
                        errorMsg.id = errorId;
                        errorMsg.className = 'text-red-500 mt-1 text-sm';
                        errorMsg.textContent = 'You must agree to the terms and conditions';
                        termsParent.appendChild(errorMsg);
                    }
                    
                    hasError = true;
                }
            }
            
            if (hasError) {
                e.preventDefault();
                
                // Scroll to first error
                const firstError = currentStep.querySelector('.border-red-500, .text-red-500');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
    }
    
    // Clear error state when input changes
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorId = this.id + '-error';
            const errorMsg = document.getElementById(errorId);
            if (errorMsg) {
                errorMsg.remove();
            }
        });
    });
    
    // Handle terms checkbox change
    const termsCheckbox = document.getElementById('terms');
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            const errorId = 'terms-error';
            const errorMsg = document.getElementById(errorId);
            if (errorMsg && this.checked) {
                errorMsg.remove();
            }
        });
    }
});
   </script>
</body>
</html>