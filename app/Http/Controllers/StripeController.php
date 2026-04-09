<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function index(Request $request)
    {
        return view('stripe');
    }

    public function store(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        
        $charge = $stripe->charges->create([
            'amount' => 200 * 100, 
            'currency' => 'usd',
            'source' => $request->input('StripeToken'),
            'description' => 'Test Charge from Laravel',
        ]);

        // dd($charge);
        return response()->with('success', 'Payment successful!');
    }
}
