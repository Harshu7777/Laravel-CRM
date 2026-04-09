<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Mail\InvoicePaid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function index()
    {
        // ✅ Auth check — guest redirect
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();

        // ✅ Empty cart toh checkout page pe jaane ki zaroorat nahi
        if ($cartItems->isEmpty()) {
            return redirect()->route('index')->with('error', 'Your cart is empty.');
        }

        return view('checkout', compact('cartItems'));
    }

    // ── Called from JavaScript (Place Order button) ──
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'required|string',
            'city'     => 'required|string',
            'zip_code' => 'required|string',
        ]);

        $cartItems = Cart::where('user_id', auth()->id())
                         ->with('product')
                         ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        // ✅ Check karo koi product delete toh nahi hua
        $hasInvalidProduct = $cartItems->contains(fn($item) => is_null($item->product));
        if ($hasInvalidProduct) {
            return response()->json(['error' => 'Some products are no longer available.'], 400);
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        // ✅ Order pehle banao, Stripe session baad mein
        $order = Order::create([
            'user_id'          => auth()->id(),
            'full_name'        => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'address'          => $request->address,
            'city'             => $request->city,
            'zip_code'         => $request->zip_code,
            'total_amount'     => $total,
            'payment_status'   => 'pending_payment',
            'shipping_status'  => 'pending',
        ]);

        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $cartItem->product_id,
                'product_name' => $cartItem->product->name ?? 'Unknown Product',
                'price'        => $cartItem->product->price,
                'quantity'     => $cartItem->quantity,
            ]);
        }

        try {
            $lineItems = $cartItems->map(function ($item) {
                return [
                    'price_data' => [
                        'currency'     => 'inr',
                        'product_data' => [
                            'name' => $item->product->name ?? 'Product',
                        ],
                        // ✅ round() for float safety
                        'unit_amount'  => (int) round($item->product->price * 100),
                    ],
                    'quantity' => $item->quantity,
                ];
            })->toArray();

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => $request->email,
                'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
                'cancel_url'           => route('checkout.cancel') . '?order_id=' . $order->id,
                'metadata'             => ['order_id' => $order->id],
            ]);

            $order->update(['stripe_session_id' => $session->id]);

            return response()->json(['url' => $session->url]);

        } catch (\Exception $e) {
            // ✅ Order aur items dono delete karo agar Stripe fail ho
            $order->items()->delete();
            $order->delete();
            Log::error('Stripe Session Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment gateway error. Please try again.'], 500);
        }
    }

    // ── Stripe Success Redirect ──
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $orderId   = $request->query('order_id');

        if (!$sessionId || !$orderId) {
            return redirect()->route('checkout')->with('error', 'Invalid payment session.');
        }

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {

                $order = Order::findOrFail($orderId);

                if ($order->payment_status === 'paid') {
                    return view('payment.success', compact('order'));
                }

                $order->update([
                    'payment_status'    => 'paid',
                    'paid_at'           => now(),
                    'stripe_session_id' => $session->id,
                ]);

                Cart::where('user_id', auth()->id())->delete();

                // ✅ Invoice email send karo
                try {
                    Mail::to($order->email)->send(new InvoicePaid($order));
                } catch (\Exception $e) {
                    Log::error('Invoice email failed: ' . $e->getMessage());
                }

                return view('payment.success', compact('order'));
            }

            // Payment pending ya failed
            return redirect()->route('checkout')
                             ->with('error', 'Payment not completed. Please try again.');

        } catch (\Exception $e) {
            Log::error('Stripe Success Error: ' . $e->getMessage());
            return redirect()->route('checkout')
                             ->with('error', 'Payment confirmation failed. Please contact support.');
        }
    }

    // ── Stripe Cancel Redirect ──
    public function cancel(Request $request)
    {
        $orderId = $request->query('order_id');

        // ✅ Cancel pe pending order delete kar do
        if ($orderId) {
            $order = Order::where('id', $orderId)
                          ->where('user_id', auth()->id())
                          ->where('payment_status', 'pending_payment')
                          ->first();

            if ($order) {
                $order->items()->delete();
                $order->delete();
            }
        }

        return redirect()->route('checkout')
                         ->with('error', 'Payment was cancelled.');
    }
}