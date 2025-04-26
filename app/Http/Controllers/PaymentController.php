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
        
        // Check if ride is completed
        if ($ride->ride_status !== 'completed') {
            return redirect()->route('passenger.dashboard')->with('error', 'This ride cannot be paid for yet.');
        }
        
        if ($ride->is_paid) {
            // If already paid, redirect to review page
            return redirect()->route('passenger.rate.ride', $ride->id)
                ->with('info', 'This ride has already been paid for. Please leave a review for your driver.');
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
    \Log::info('Process payment called for ride ID: ' . $ride->id, [
        'request_data' => $request->except(['token', 'password', 'card'])
    ]);
    
    // Security check - ensure the ride belongs to this passenger
    $passenger = Passenger::where('user_id', Auth::id())->first();
    if ($ride->passenger_id !== $passenger->id) {
        \Log::warning('Unauthorized payment attempt', [
            'ride_id' => $ride->id,
            'attempting_user' => Auth::id(),
            'ride_passenger_id' => $ride->passenger_id
        ]);
        return redirect()->route('passenger.dashboard')->with('error', 'You are not authorized to make this payment.');
    }
    
    // Check if ride is completed and not already paid
    if ($ride->ride_status !== 'completed') {
        \Log::warning('Payment attempt for incomplete ride', [
            'ride_id' => $ride->id,
            'ride_status' => $ride->ride_status
        ]);
        return redirect()->route('passenger.dashboard')->with('error', 'This ride cannot be paid for yet.');
    }
    
    if ($ride->is_paid) {
        \Log::warning('Payment attempt for already paid ride', [
            'ride_id' => $ride->id
        ]);
        return redirect()->route('passenger.rate.ride', $ride->id)
            ->with('info', 'This ride has already been paid for. Please leave a review for your driver.');
    }
    
    // Validate request
    $validated = $request->validate([
        'payment_method_id' => 'required|string',
        'setup_intent_id' => 'nullable|string',
    ]);
    
    \Log::info('Payment validation passed', [
        'ride_id' => $ride->id
    ]);
    
    // Start database transaction
    DB::beginTransaction();
    try {
        // Check if a payment record already exists for this ride
        $payment = Payment::where('ride_id', $ride->id)->first();
        
        if (!$payment) {
            // Create a new payment record
            $payment = new Payment();
            $payment->ride_id = $ride->id;
            $payment->user_id = Auth::id();
            $payment->amount = $ride->price;
            $payment->payment_method = 'card';
            \Log::info('Creating new payment record', [
                'ride_id' => $ride->id,
                'payment_method' => 'card'
            ]);
        } else {
            // Update existing payment record
            $payment->payment_method = 'card';
            \Log::info('Updating existing payment record', [
                'ride_id' => $ride->id,
                'payment_id' => $payment->id,
                'payment_method' => 'card'
            ]);
        }
        
        // Process card payment through Stripe
        if (!empty($validated['payment_method_id'])) {
            // Check if saving a new card
            if (!empty($validated['setup_intent_id'])) {
                // Save the payment method for future use
                $this->savePaymentMethod($validated['payment_method_id'], $validated['setup_intent_id']);
            }
            
            // Create a payment intent with automatic payment methods disabled
            // to prevent redirect-based payment methods
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
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'return_url' => route('passenger.dashboard') // Fallback return URL
            ]);
            
            // Update payment record with Stripe data
            $payment->stripe_payment_id = $intent->id;
            $payment->status = 'completed';
            $payment->payment_details = json_encode([
                'card_brand' => $intent->charges->data[0]->payment_method_details->card->brand,
                'card_last4' => $intent->charges->data[0]->payment_method_details->card->last4,
                'receipt_url' => $intent->charges->data[0]->receipt_url,
            ]);
            
            \Log::info('Card payment processed successfully', [
                'ride_id' => $ride->id,
                'stripe_payment_id' => $intent->id
            ]);
        } else {
            throw new Exception('No payment method provided');
        }
        
        // Save the payment record
        $payment->save();
        
        // Update ride payment status
        $ride->is_paid = true;
        $ride->payment_method = 'card';
        $ride->payment_status = 'completed';
        $ride->save();
        
        \Log::info('Ride payment status updated', [
            'ride_id' => $ride->id,
            'is_paid' => $ride->is_paid,
            'payment_method' => $ride->payment_method,
            'payment_status' => $ride->payment_status
        ]);
        
        // Update driver's balance
        $driver = $ride->driver;
        $driver->balance += $ride->price;
        $driver->save();
        
        // Notify the driver about completed payment
        \Log::info("Card payment completed for ride #{$ride->id}. Driver #{$driver->id} balance updated.");
        
        DB::commit();
        
        // Redirect to rating page
        \Log::info('Redirecting to rating page after card payment', [
            'ride_id' => $ride->id
        ]);
        return redirect()->route('passenger.rate.ride', $ride->id)
            ->with('success', 'Payment successful! Please rate your ride.');
        
    } catch (CardException $e) {
        DB::rollBack();
        // Handle card errors
        \Log::error('Card error in payment processing', [
            'ride_id' => $ride->id,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()->with('error', 'Card error: ' . $e->getMessage());
        
    } catch (ApiErrorException $e) {
        DB::rollBack();
        // Handle Stripe API errors
        \Log::error('Stripe API error in payment processing', [
            'ride_id' => $ride->id,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()->with('error', 'Payment error: ' . $e->getMessage());
        
    } catch (Exception $e) {
        DB::rollBack();
        // Handle other errors
        \Log::error('General error in payment processing', [
            'ride_id' => $ride->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

/**
 * Create a Setup Intent for saving a card
 */
public function createSetupIntent()
{
    try {
        $setupIntent = SetupIntent::create([
            'customer' => $this->getOrCreateStripeCustomer(),
            'usage' => 'off_session',
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never'
            ]
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
}