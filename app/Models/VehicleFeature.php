<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleFeature extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'feature',
    ];
    
    /**
     * Available feature types
     */
    const FEATURE_AC = 'ac';
    const FEATURE_WIFI = 'wifi';
    const FEATURE_CHILD_SEAT = 'child_seat';
    const FEATURE_USB_CHARGER = 'usb_charger';
    const FEATURE_PET_FRIENDLY = 'pet_friendly';
    const FEATURE_LUGGAGE_CARRIER = 'luggage_carrier';
    
    /**
     * All available features
     *
     * @var array
     */
    const AVAILABLE_FEATURES = [
        self::FEATURE_AC => 'Air Conditioning',
        self::FEATURE_WIFI => 'WiFi',
        self::FEATURE_CHILD_SEAT => 'Child Seat',
        self::FEATURE_USB_CHARGER => 'USB Charger',
        self::FEATURE_PET_FRIENDLY => 'Pet Friendly',
        self::FEATURE_LUGGAGE_CARRIER => 'Luggage Carrier',
    ];

    /**
     * Get the vehicle that owns the feature.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the display name for this feature
     */
    public function getDisplayNameAttribute()
    {
        return self::AVAILABLE_FEATURES[$this->feature] ?? ucfirst(str_replace('_', ' ', $this->feature));
    }
}