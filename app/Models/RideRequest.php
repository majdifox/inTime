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
     * Valid status values
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

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
    
    /**
     * Accept the ride request.
     *
     * @return bool Success status
     */
    public function accept()
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        $this->status = self::STATUS_ACCEPTED;
        $this->responded_at = now();
        
        return $this->save();
    }
    
    /**
     * Reject the ride request.
     *
     * @return bool Success status
     */
    public function reject()
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        $this->status = self::STATUS_REJECTED;
        $this->responded_at = now();
        
        return $this->save();
    }
    
    /**
     * Mark the ride request as expired.
     *
     * @return bool Success status
     */
    public function expire()
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        $this->status = self::STATUS_EXPIRED;
        $this->responded_at = now();
        
        return $this->save();
    }
    
    /**
     * Check if the request is still pending.
     *
     * @return bool Whether request is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    /**
     * Check if the request has expired based on time threshold.
     * 
     * @param int $expiryMinutes Minutes after which request is considered expired
     * @return bool Whether request has expired by time
     */
    public function hasExpiredByTime($expiryMinutes = 2)
    {
        return $this->isPending() && 
               $this->requested_at && 
               $this->requested_at->addMinutes($expiryMinutes)->lt(now());
    }
}