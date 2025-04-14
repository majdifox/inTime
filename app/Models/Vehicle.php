<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'make',
        'model',
        'year',
        'color',
        'plate_number',
        'type',
        'capacity',
        'vehicle_photo',
        'insurance_expiry',
        'registration_expiry',
        'is_active',
    ];

    protected $casts = [
        'insurance_expiry' => 'date',
        'registration_expiry' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Available vehicle types
     * 
     * @var array
     */
    const VEHICLE_TYPES = [
        'share' => 'Share',
        'comfort' => 'Comfort',
        'women' => 'Women',
        'wav' => 'WAV',
        'black' => 'Black'
    ];

    /**
     * Get the driver that owns the vehicle.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the features for the vehicle.
     */
    public function features()
    {
        return $this->hasMany(VehicleFeature::class);
    }
    
    /**
     * Check if the vehicle has a specific feature.
     */
    public function hasFeature($feature)
    {
        return $this->features()->where('feature', $feature)->exists();
    }
    
    /**
     * Get array of all features this vehicle has
     */
    public function getFeatureListAttribute()
    {
        return $this->features()->pluck('feature')->toArray();
    }
    
    /**
     * Check if this is a women-only vehicle
     */
    public function isWomenOnly()
    {
        return $this->type === 'women';
    }
    
    /**
     * Get fare setting for this vehicle type
     */
    public function getFareSetting()
    {
        return FareSetting::getForVehicleType($this->type);
    }
    
    /**
     * Get a formatted display name for the vehicle
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->make} {$this->model} ({$this->year})";
    }
    
    /**
     * Get a display name for the vehicle type
     */
    public function getTypeDisplayAttribute()
    {
        return self::VEHICLE_TYPES[$this->type] ?? ucfirst($this->type);
    }
    
    /**
     * Check if vehicle documents are valid (not expired)
     */
    public function documentsValid()
    {
        $now = now();
        return $this->insurance_expiry->gt($now) && $this->registration_expiry->gt($now);
    }
    
    /**
     * Scope query to only include vehicles of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope query to only include active vehicles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope query to include only women vehicles
     */
    public function scopeWomenOnly($query)
    {
        return $query->where('type', 'women');
    }
}