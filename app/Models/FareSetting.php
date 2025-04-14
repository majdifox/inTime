<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FareSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_type',
        'base_fare',
        'per_km_price',
        'per_minute_price',
        'minimum_fare',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_fare' => 'decimal:2',
        'per_km_price' => 'decimal:2',
        'per_minute_price' => 'decimal:2',
        'minimum_fare' => 'decimal:2',
    ];

    /**
     * Get fare settings for a specific vehicle type
     * 
     * @param string $vehicleType The vehicle type to search for
     * @return FareSetting|null The fare settings or null if not found
     */
    public static function getForVehicleType(string $vehicleType)
    {
        return self::where('vehicle_type', $vehicleType)->first();
    }
    
    /**
     * Calculate fare for a ride
     * 
     * @param float $distanceInKm Distance in kilometers
     * @param float $surgeMultiplier Surge pricing multiplier
     * @return array Fare details
     */
    public function calculateFare(float $distanceInKm, float $surgeMultiplier = 1.0)
    {
        // Calculate distance based component
        $distanceFare = $distanceInKm * $this->per_km_price;
        
        // Calculate subtotal
        $subtotal = $this->base_fare + $distanceFare;
        
        // Apply surge pricing
        $fareWithSurge = $subtotal * $surgeMultiplier;
        
        // Ensure minimum fare is applied
        $finalFare = max($fareWithSurge, $this->minimum_fare);
        
        return [
            'vehicle_type' => $this->vehicle_type,
            'base_fare' => $this->base_fare,
            'per_km_price' => $this->per_km_price,
            'distance_km' => $distanceInKm,
            'distance_fare' => $distanceFare,
            'subtotal' => $subtotal,
            'surge_multiplier' => $surgeMultiplier,
            'surge_amount' => $fareWithSurge - $subtotal,
            'total_fare' => round($finalFare, 2)
        ];
    }
    
    /**
     * Get formatted display name for vehicle type
     */
    public function getVehicleTypeDisplayAttribute()
    {
        $displayNames = [
            'share' => 'Share',
            'comfort' => 'Comfort',
            'women' => 'Women',
            'wav' => 'Wheelchair Accessible',
            'black' => 'Premium Black'
        ];
        
        return $displayNames[$this->vehicle_type] ?? ucfirst($this->vehicle_type);
    }
    
    /**
     * Get description for a vehicle type
     */
    public function getDescriptionAttribute()
    {
        $descriptions = [
            'share' => 'Economic shared rides for daily commuters',
            'comfort' => 'Standard private rides with comfortable vehicles',
            'women' => 'Women-only service with female drivers for enhanced security',
            'wav' => 'Wheelchair accessible vehicles with trained drivers',
            'black' => 'Premium service with high-end vehicles and professional drivers'
        ];
        
        return $descriptions[$this->vehicle_type] ?? '';
    }
    
    /**
     * Check if this fare setting is for women-only service
     */
    public function isWomenOnly()
    {
        return $this->vehicle_type === 'women';
    }
}