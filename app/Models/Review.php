<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ride_id',
        'reviewer_id',
        'reviewed_id',
        'rating',
        'comment',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
    ];
    
    /**
     * Get the ride associated with the review.
     */
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
    
    /**
     * Get the user who submitted the review.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    
    /**
     * Get the user who received the review.
     */
    public function reviewed()
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }

    public function passenger()
{
    // assuming reviewed_id is the passenger's user_id
    return $this->belongsTo(Passenger::class, 'reviewer_id', 'user_id');
}
}