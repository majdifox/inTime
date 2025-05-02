@extends('driver.layouts.driver')
@section('content')
<style>
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
@section('title', 'Account Under Review')

@section('content')
<main class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Left Column (Status) -->
                <div class="w-full md:w-1/3 bg-gray-50 p-8 flex flex-col items-center">
                    <div class="mb-6">
                        <div class="bg-yellow-100 p-5 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="text-xl font-bold mb-2 text-center">Account Status</h2>
                    
                    <div class="status-badge bg-yellow-50 text-yellow-800 px-4 py-2 rounded-full font-medium text-sm mb-6 flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        {{ ucfirst($status ?? 'Under Review') }}
                    </div>
                    
                    <div class="text-center mb-6">
                        <p class="text-gray-600 text-sm">Your application was submitted</p>
                        <p class="font-medium">{{ \Carbon\Carbon::now()->format('F j, Y') }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6 w-full">
                        <h3 class="font-medium text-sm text-gray-500 uppercase tracking-wider mb-4 text-center">Est. Time Remaining</h3>
                        <div class="flex justify-center">
                            <div class="text-2xl font-bold">24-48<span class="text-sm font-normal text-gray-500 ml-1">hours</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column (Content) -->
                <div class="w-full md:w-2/3 p-8">
                    <h1 class="text-2xl font-bold mb-4">{{ $title ?? 'Your Application is Under Review' }}</h1>
                    
                    <p class="text-gray-600 mb-6">
                        {{ $message ?? 'Thank you for registering as a driver with inTime. Our team is currently reviewing your documents and vehicle information to ensure compliance with our standards and local regulations.' }}
                    </p>
                    
                    <!-- Application Timeline -->
                    <div class="mb-8">
                        <h3 class="font-medium mb-4">Application Progress</h3>
                        
                        <div class="space-y-6">
                            <!-- Step 1: Submitted -->
                            <div class="timeline-step completed pl-10">
                                <div class="absolute left-0 top-0 bg-green-500 rounded-full w-7 h-7 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="font-medium">Application Submitted</h4>
                                <p class="text-sm text-gray-500">Your driver registration has been received.</p>
                            </div>
                            
                            <!-- Step 2: Document Review -->
                            <div class="timeline-step pl-10">
                                <div class="absolute left-0 top-0 bg-yellow-500 rounded-full w-7 h-7 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="font-medium">Document Verification</h4>
                                <p class="text-sm text-gray-500">We're reviewing your license, insurance, and other documents.</p>
                            </div>
                            
                            <!-- Step 3: Background Check -->
                            <div class="timeline-step pl-10">
                                <div class="absolute left-0 top-0 bg-gray-300 rounded-full w-7 h-7 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="font-medium">Background Check</h4>
                                <p class="text-sm text-gray-500">We'll verify your driving record and background information.</p>
                            </div>
                            
                            <!-- Step 4: Approved -->
                            <div class="timeline-step pl-10">
                                <div class="absolute left-0 top-0 bg-gray-300 rounded-full w-7 h-7 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="font-medium">Application Approved</h4>
                                <p class="text-sm text-gray-500">Once approved, you can start accepting ride requests.</p>
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
                                <h4 class="text-sm font-medium text-blue-800">Need to update your information?</h4>
                                <p class="mt-1 text-sm text-blue-700">
                                    If you need to make changes to your submitted information, please contact our support team.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <a href="" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-150 font-medium">
                            Back to Home
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 font-medium">
                                Log Out
                            </button>
                        </form>
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
                        <span>How long does the review process take?</span>
                        <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                        <p class="text-gray-700">The review process typically takes 1-3 business days. During busy periods, it may take up to 5 business days. You'll receive an email notification as soon as your application is approved.</p>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                        <span>What if my application gets rejected?</span>
                        <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                        <p class="text-gray-700">If your application is rejected, you'll receive an email with the specific reasons. In most cases, you can address the issues and reapply. Common reasons include expired documents, poor quality photos, or incomplete information.</p>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="flex justify-between items-center w-full px-6 py-4 text-left font-medium text-gray-900 bg-white hover:bg-gray-50 transition duration-150 faq-toggle" aria-expanded="false">
                        <span>Can I drive while my application is being reviewed?</span>
                        <svg class="h-5 w-5 text-gray-500 faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 faq-content">
                        <p class="text-gray-700">No, you cannot accept ride requests until your application has been fully approved. This is to ensure the safety of our passengers and compliance with local regulations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

    
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