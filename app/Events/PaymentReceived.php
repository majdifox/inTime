<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    public $ride;

    /**
     * Create a new event instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->ride = $payment->ride;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ride.'.$this->ride->id),
            new PrivateChannel('driver.'.$this->ride->driver_id),
            new PrivateChannel('passenger.'.$this->ride->passenger_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'payment_id' => $this->payment->id,
            'ride_id' => $this->ride->id,
            'amount' => $this->payment->amount,
            'status' => $this->payment->status,
            'method' => $this->payment->payment_method,
            'timestamp' => now()->toIso8601String(),
            'driver_redirect' => $this->payment->status === 'completed' 
                ? route('driver.rate.ride', $this->ride->id) 
                : null,
            'passenger_redirect' => $this->payment->status === 'completed' 
                ? route('passenger.rate.ride', $this->ride->id) 
                : null,
        ];
    }
}
