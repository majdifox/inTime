<!-- passenger/partials/fareDetails.blade.php -->
<div class="bg-gray-50 rounded-md p-4">
    <h3 class="font-medium mb-3">Fare Breakdown</h3>
    
    <div class="space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Base Fare</span>
            <span>MAD {{ number_format($ride->base_fare, 2) }}</span>
        </div>
        
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Distance ({{ number_format($ride->distance_in_km, 1) }} km × MAD {{ number_format($ride->per_km_price, 2) }})</span>
            <span>MAD {{ number_format($ride->distance_in_km * $ride->per_km_price, 2) }}</span>
        </div>
        
        @if($ride->wait_time_minutes > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Wait Time ({{ $ride->wait_time_minutes }} min)</span>
                <span>MAD {{ number_format($ride->wait_time_minutes * 0.5, 2) }}</span>
            </div>
        @endif
        
        @if($ride->surge_multiplier > 1)
            <div class="flex justify-between text-sm font-medium pt-2 border-t">
                <span class="text-gray-600">Subtotal</span>
                <span>MAD {{ number_format($ride->price / $ride->surge_multiplier, 2) }}</span>
            </div>
            
            <div class="flex justify-between text-sm text-red-600">
                <span>Surge pricing (×{{ $ride->surge_multiplier }})</span>
                <span>MAD {{ number_format($ride->price - ($ride->price / $ride->surge_multiplier), 2) }}</span>
            </div>
        @endif
        
        <div class="flex justify-between font-bold text-sm pt-2 border-t">
            <span>Total</span>
            <span>MAD {{ number_format($ride->price, 2) }}</span>
        </div>
    </div>
    
    <div class="mt-4 pt-3 border-t text-xs text-gray-500">
        <p>Payment Method: <span class="font-medium">Automatic ({{ $ride->payment_method ?? 'Credit Card' }})</span></p>
    </div>
</div>