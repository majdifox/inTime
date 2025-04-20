<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'last_updated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the driver that owns the location.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    
    /**
     * Check if this location data is recent enough (within last 5 minutes)
     *
     * @return bool Whether location data is considered recent
     */
    public function isRecent()
    {
        return $this->last_updated && $this->last_updated->gt(now()->subMinutes(5));
    }
    
    /**
     * Calculate distance to another location in kilometers
     *
     * @param float $lat Latitude of target location
     * @param float $lng Longitude of target location
     * @return float Distance in kilometers
     */
    public function distanceTo($lat, $lng)
    {
        // Haversine formula to calculate distance between two coordinates
        $earthRadius = 6371; // in kilometers
        
        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + 
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
        return $angle * $earthRadius;
    }
}