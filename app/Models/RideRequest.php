<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ride_id',
        'driver_id',
        'status',
        'requested_at',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the ride associated with this request.
     */
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    /**
     * Get the driver associated with this request.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}