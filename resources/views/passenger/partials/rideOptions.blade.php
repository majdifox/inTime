<!-- passenger/partials/rideOptions.blade.php -->
<div class="space-y-3">
    @foreach($fareOptions as $option)
        @php 
            $isDisabled = $option->vehicle_type === 'women' && Auth::user()->gender !== 'female';
        @endphp
        
        <div class="border rounded-md p-4 {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-black transition' }} ride-option-card"
             data-vehicle-type="{{ $option->vehicle_type }}" {{ $isDisabled ? 'disabled' : '' }}>
            <div class="flex items-center">
                <div class="h-12 w-12 bg-gray-200 rounded-full mr-4 flex items-center justify-center">
                    @switch($option->vehicle_type)
                        @case('share')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            @break
                        @case('comfort')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            @break
                        @case('women')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            @break
                        @case('wav')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            @break
                        @case('black')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            @break
                        @default
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 4H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                    @endswitch
                </div>
                <div>
                    <h3 class="font-bold">{{ $option->getVehicleTypeDisplayAttribute() }}</h3>
                    <p class="text-sm text-gray-500">
                        @if(isset($estimate))
                            {{ $estimate['eta_minutes'] ?? '5-10' }} min wait
                        @else
                            5-10 min wait
                        @endif
                    </p>
                </div>
                <div class="ml-auto text-right">
                    <p class="font-bold">
                        @if(isset($distance) && isset($surgeMultiplier))
                            @php
                                $fare = $option->calculateFare($distance, $surgeMultiplier);
                            @endphp
                            MAD {{ number_format($fare['total_fare'], 2) }}
                        @else
                            MAD {{ number_format($option->base_fare, 2) }}+
                        @endif
                    </p>
                    @if(isset($surgeMultiplier) && $surgeMultiplier > 1)
                        <p class="text-xs text-red-600">Surge x{{ $surgeMultiplier }}</p>
                    @endif
                </div>
            </div>
            
            @if($option->isWomenOnly() && Auth::user()->gender !== 'female')
                <div class="mt-2 text-xs text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Available only for female passengers
                </div>
            @else
                <div class="mt-2 text-xs text-gray-500">
                    {{ $option->getDescriptionAttribute() }}
                </div>
            @endif
        </div>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rideOptions = document.querySelectorAll('.ride-option-card:not([disabled])');
        
        rideOptions.forEach(option => {
            option.addEventListener('click', function() {
                const vehicleType = this.getAttribute('data-vehicle-type');
                
                // Remove selection from all options
                rideOptions.forEach(opt => {
                    opt.classList.remove('border-black', 'bg-gray-50');
                });
                
                // Add selection to clicked option
                this.classList.add('border-black', 'bg-gray-50');
                
                // Set selected vehicle type in hidden input
                if (document.getElementById('selected_vehicle_type')) {
                    document.getElementById('selected_vehicle_type').value = vehicleType;
                }
                
                // Show request button if it exists
                if (document.getElementById('request-ride-btn')) {
                    document.getElementById('request-ride-btn').classList.remove('hidden');
                }
            });
        });
    });
</script>