<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'feature',
    ];

    /**
     * Get the vehicle that owns the feature.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}