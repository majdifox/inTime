<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        // If account_status is not set already, set based on role
        if (!$user->account_status) {
            if ($user->role === 'passenger') {
                $user->account_status = 'activated';
            } else if ($user->role === 'driver') {
                $user->account_status = 'deactivated';
            }
        }
    }
}