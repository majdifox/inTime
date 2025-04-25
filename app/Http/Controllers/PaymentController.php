<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ride;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Driver;
use App\Models\Passenger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;
use Stripe\Exception\CardException;
use Stripe\Exception\ApiErrorException;
use Exception;

class PaymentController extends Controller
{
    /**
     * Constructor - set Stripe API key
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    
    /**
     * Show payment page for a completed ride
     */
    public function showPaymentPage(Ride $ride)
    {
        // Security check - ensure the ride belongs to this passenger
        $passenger = Passenger::where('user_id', Auth::id())->first();
        if ($ride->passenger_id !== $passenger->id) {
            return redirect()->route('passenger.dashboard')->with('error', 'You are not authorized to view this page.');
        }
        
        // Check if ride is completed and not already paid
        if ($ride->ride_status !== 'completed' || $ride->is_paid) {
            return redirect()->route('passenger.dashboard')->with('error', 'This ride cannot be paid for.');
        }
        
        // Get the passenger's saved payment methods
        $savedCards = PaymentMethod::where('user_id', Auth::id())->get();
        
        return view('passenger.ridePayment', compact('ride', 'savedCards'));
    }
    
    /**
     * Process payment for a ride
     */
    public function processPayment(Request $request, Ride $ride)
{
    // Security check - ensure the ride belongs to this passenger
    $passenger = Passenger::where('user_id', Auth::id())->first();
    if ($ride->passenger_id !== $passenger->id) {
        return redirect()->route('passenger.dashboard')->with('error', 'You are not authorized to make this payment.');
    }
    
    // Check if ride is completed and not already paid
    if ($ride->ride_status !== 'completed' || $ride->is_paid) {
        return redirect()->route('passenger.dashboard')->with('error', 'This ride cannot be paid for.');
    }
    
    // Validate request
    $validated = $request->validate([
        'payment_method_type' => 'required|in:card,cash',
        'payment_method_id' => 'nullable|string',
        'setup_intent_id' => 'nullable|string',
    ]);
    
    // Start database transaction
    DB::beginTransaction();
    try {
        // Create a payment record
        $payment = new Payment();
        $payment->ride_id = $ride->id;
        $payment->user_id = Auth::id();
        $payment->amount = $ride->price;
        $payment->payment_method = $validated['payment_method_type'];
        
        if ($validated['payment_method_type'] === 'card') {
            // Process card payment through Stripe
            if (!empty($validated['payment_method_id'])) {
                // Check if saving a new card
                if (!empty($validated['setup_intent_id'])) {
                    // Save the payment method for future use
                    $this->savePaymentMethod($validated['payment_method_id'], $validated['setup_intent_id']);
                }
                
                // Create a payment intent
                $intent = PaymentIntent::create([
                    'amount' => round($ride->price * 100), // Stripe requires amount in cents
                    'currency' => 'mad', // Moroccan Dirham
                    'payment_method' => $validated['payment_method_id'],
                    'confirm' => true,
                    'description' => 'Ride payment: #' . $ride->id,
                    'metadata' => [
                        'ride_id' => $ride->id,
                        'user_id' => Auth::id(),
                    ],
                ]);
                
                // Update payment record with Stripe data
                $payment->stripe_payment_id = $intent->id;
                $payment->status = 'completed';
                $payment->payment_details = json_encode([
                    'card_brand' => $intent->charges->data[0]->payment_method_details->card->brand,
                    'card_last4' => $intent->charges->data[0]->payment_method_details->card->last4,
                    'receipt_url' => $intent->charges->data[0]->receipt_url,
                ]);
            } else {
                throw new Exception('No payment method provided');
            }
        } else {
            // Cash payment - mark as pending until driver confirms
            $payment->status = 'pending';
            $payment->payment_details = json_encode(['method' => 'cash']);
        }
        
        // Save the payment record
        $payment->save();
        
        // Update ride payment status
        if ($validated['payment_method_type'] === 'card') {
            $ride->is_paid = true;
            $ride->payment_method = 'card';
            $ride->payment_status = 'completed';
        } else {
            $ride->is_paid = false;
            $ride->payment_method = 'cash';
            $ride->payment_status = 'pending';
        }
        $ride->save();
        
        // Update driver's balance for card payments
        if ($validated['payment_method_type'] === 'card') {
            $driver = $ride->driver;
            $driver->balance += $ride->price;
            $driver->save();
            
            // Notify the driver about completed payment
            // In a real app, you would use a notification system here
            // For now, we'll just log it
            \Log::info("Card payment completed for ride #{$ride->id}. Driver #{$driver->id} balance updated.");
        }
        
        DB::commit();
        
        // Redirect based on payment type
        if ($validated['payment_method_type'] === 'card') {
            // For card payments, redirect to rating page
            return redirect()->route('passenger.rate.ride', $ride->id)->with('success', 'Payment successful! Please rate your ride.');
        } else {
            // For cash payments, show cash payment instructions page
            return redirect()->route('passenger.cash.payment', $ride->id);
        }
        
    } catch (CardException $e) {
        DB::rollBack();
        // Handle card errors
        return redirect()->back()->with('error', 'Card error: ' . $e->getMessage());
        
    } catch (ApiErrorException $e) {
        DB::rollBack();
        // Handle Stripe API errors
        return redirect()->back()->with('error', 'Payment error: ' . $e->getMessage());
        
    } catch (Exception $e) {
        DB::rollBack();
        // Handle other errors
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
    
    /**
     * Show cash payment page for passenger
     */
    public function showCashPaymentPage(Ride $ride)
    {
        // Security check - ensure the ride belongs to this passenger
        $passenger = Passenger::where('user_id', Auth::id())->first();
        if ($ride->passenger_id !== $passenger->id) {
            return redirect()->route('passenger.dashboard')->with('error', 'You are not authorized to view this page.');
        }
        
        return view('passenger.cashPayment', compact('ride'));
    }
    
    /**
     * Show cash payment confirmation page for driver
     */
   
    /**
     * Create a Setup Intent for saving a card
     */
    public function createSetupIntent()
    {
        try {
            $setupIntent = SetupIntent::create([
                'customer' => $this->getOrCreateStripeCustomer(),
                'usage' => 'off_session',
            ]);
            
            return response()->json(['client_secret' => $setupIntent->client_secret]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get or create a Stripe customer for the current user
     */
    private function getOrCreateStripeCustomer()
    {
        $user = Auth::user();
        
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }
        
        // Create a new customer in Stripe
        $customer = \Stripe\Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);
        
        // Save the customer ID to the user
        $user->stripe_customer_id = $customer->id;
        $user->save();
        
        return $customer->id;
    }
    
    /**
     * Save a payment method for future use
     */
    private function savePaymentMethod($paymentMethodId, $setupIntentId)
    {
        $user = Auth::user();
        
        // Retrieve the Setup Intent to get payment method details
        $setupIntent = SetupIntent::retrieve($setupIntentId);
        $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
        
        // Save to our database
        $savedMethod = new PaymentMethod();
        $savedMethod->user_id = $user->id;
        $savedMethod->stripe_payment_method_id = $paymentMethodId;
        $savedMethod->brand = $paymentMethod->card->brand;
        $savedMethod->last4 = $paymentMethod->card->last4;
        $savedMethod->exp_month = $paymentMethod->card->exp_month;
        $savedMethod->exp_year = $paymentMethod->card->exp_year;
        $savedMethod->is_default = !PaymentMethod::where('user_id', $user->id)->exists(); // Make default if it's the first one
        $savedMethod->save();
        
        return $savedMethod;
    }
    
    /**
     * Delete a saved payment method
     */
    public function deletePaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        
        // Security check
        if ($paymentMethod->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this payment method.');
        }
        
        try {
            // Delete from Stripe
            $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethod->stripe_payment_method_id);
            $stripePaymentMethod->detach();
            
            // Delete from our database
            $paymentMethod->delete();
            
            return redirect()->back()->with('success', 'Payment method deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete payment method: ' . $e->getMessage());
        }
    }
    
    /**
     * Set a payment method as default
     */
    public function setDefaultPaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        
        // Security check
        if ($paymentMethod->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to update this payment method.');
        }
        
        // Update all payment methods to not default
        PaymentMethod::where('user_id', Auth::id())->update(['is_default' => false]);
        
        // Set this one as default
        $paymentMethod->is_default = true;
        $paymentMethod->save();
        
        return redirect()->back()->with('success', 'Default payment method updated.');
    }

    public function showCashConfirmationPage(Ride $ride)
{
    // Security check - ensure the ride belongs to this driver
    $driver = Driver::where('user_id', Auth::id())->first();
    if ($ride->driver_id !== $driver->id) {
        return redirect()->route('driver.dashboard')->with('error', 'You are not authorized to view this page.');
    }
    
    return view('driver.confirmCashPayment', compact('ride'));
}

public function confirmCashPayment(Request $request, Ride $ride)
{
    // Security check - ensure the ride belongs to this driver
    $driver = Driver::where('user_id', Auth::id())->first();
    if ($ride->driver_id !== $driver->id) {
        return redirect()->route('driver.dashboard')->with('error', 'You are not authorized to perform this action.');
    }
    
    // Check if payment is pending
    if ($ride->payment_status !== 'pending' || $ride->payment_method !== 'cash') {
        return redirect()->route('driver.dashboard')->with('error', 'This ride payment cannot be confirmed.');
    }
    
    // Start database transaction
    DB::beginTransaction();
    try {
        // Update the payment record
        $payment = Payment::where('ride_id', $ride->id)->first();
        if ($payment) {
            $payment->status = 'completed';
            $payment->save();
        } else {
            // Create a payment record if it doesn't exist
            $payment = new Payment();
            $payment->ride_id = $ride->id;
            $payment->user_id = $ride->passenger->user_id;
            $payment->amount = $ride->price;
            $payment->payment_method = 'cash';
            $payment->status = 'completed';
            $payment->payment_details = json_encode(['method' => 'cash']);
            $payment->save();
        }
        
        // Update ride payment status
        $ride->is_paid = true;
        $ride->payment_status = 'completed';
        $ride->save();
        
        // Update driver's balance
        $driver->balance += $ride->price;
        $driver->save();
        
        // Log the successful cash payment
        \Log::info("Cash payment confirmed for ride #{$ride->id}. Driver #{$driver->id} balance updated.");
        
        DB::commit();
        
        // Redirect to rate passenger page
        return redirect()->route('driver.rate.ride', $ride->id)->with('success', 'Cash payment confirmed! Please rate your passenger.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Cash payment confirmation failed: {$e->getMessage()}", [
            'ride_id' => $ride->id,
            'driver_id' => $driver->id,
            'exception' => $e->getMessage()
        ]);
        
        // Handle errors
        return redirect()->back()->with('error', 'An error occurred while confirming payment: ' . $e->getMessage());
    }
}
}