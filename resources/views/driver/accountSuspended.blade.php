<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inTime - Account Suspended</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Add custom fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .status-badge {
            transition: all 0.3s ease;
        }
        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 17px;
            top: 30px;
            height: calc(100% - 30px);
            width: 2px;
            background-color: #e5e7eb;
        }
        .timeline-step.completed:not(:last-child)::after {
            background-color: #10b981;
        }
        .timeline-step {
            position: relative;
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
                <a href="#" class="font-medium hover:text-black transition duration-150">Home</a>
                <a href="#" class="font-medium hover:text-black transition duration-150">My Profile</a>
            </nav>
        </div>
        
        <!-- User Profile -->
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 rounded-full bg-gray-300 overflow-hidden shadow-sm">
                <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover">
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Left Column (Status) -->
                    <div class="w-full md:w-1/3 bg-gray-50 p-8 flex flex-col items-center">
                        <div class="mb-6">
                            <div class="bg-red-100 p-5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        
                        <h2 class="text-xl font-bold mb-2 text-center">Account Status</h2>
                        
                        <div class="status-badge bg-red-50 text-red-800 px-4 py-2 rounded-full font-medium text-sm mb-6 flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Suspended
                        </div>
                        
                        <div class="text-center mb-6">
                            <p class="text-gray-600 text-sm">Last updated</p>
                            <p class="font-medium">{{ \Carbon\Carbon::now()->format('F j, Y') }}</p>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6 w-full">
                            <h3 class="font-medium text-sm text-gray-500 uppercase tracking-wider mb-4 text-center">To Resolve</h3>
                            <div class="flex justify-center">
                                <div class="text-lg font-medium text-center text-gray-800">Contact Support</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column (Content) -->
                    <div class="w-full md:w-2/3 p-8">
                        <h1 class="text-2xl font-bold mb-4">Your Account is Suspended</h1>
                        
                        <p class="text-gray-600 mb-6">
                            We regret to inform you that your account has been Suspended. This might be due to multiple ride cancellations, policy violations, or other issues with your account.
                        </p>
                        
                        <!-- Suspension Reasons -->
                        <div class="mb-8">
                            <h3 class="font-medium mb-4">Possible Reasons for Suspension</h3>
                            
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900">Multiple Ride Cancellations</h4>
                                            <p class="mt-1 text-sm text-gray-500">You've cancelled several rides recently, which may have triggered an automatic suspension.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900">Low Ratings</h4>
                                            <p class="mt-1 text-sm text-gray-500">Consistently low ratings from drivers or passengers may lead to account deactivation.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900">Platform Policy Violation</h4>
                                            <p class="mt-1 text-sm text-gray-500">Violating inTime's terms of service or community guidelines.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-6 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800">Need help reactivating your account?</h4>
                                    <p class="mt-1 text-sm text-blue-700">
                                        Please contact our support team to discuss your account status and the steps you can take to reactivate it.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-8">
                            <a href="" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-150 font-medium">
                                Back to Home
                            </a>
                            <a href="mailto:support@intime-app.com" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 font-medium">
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6 text-center">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                            <span>How can I get my account reactivated?</span>
                            <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                            <p class="text-gray-700">To reactivate your account, please contact our support team. Depending on the reason for deactivation, you may need to wait a specific period, complete certain actions, or agree to specific terms before reactivation.</p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                            <span>How long do temporary suspensions last?</span>
                            <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                            <p class="text-gray-700">Temporary suspensions for ride cancellations typically last from 1 minute to 24 hours, depending on the frequency and pattern of cancellations. Other violations may result in longer suspensions.</p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                            <span>Will I lose my data or history after reactivation?</span>
                            <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                            <p class="text-gray-700">No, your ride history, profile information, and account details will remain intact when your account is reactivated. Your rating may be affected by the events that led to deactivation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white py-6 mt-12 border-t border-gray-200">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-center md:text-left text-gray-500 text-sm">&copy; 2025 inTime. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150">
                        Privacy Policy
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150">
                        Terms of Service
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>