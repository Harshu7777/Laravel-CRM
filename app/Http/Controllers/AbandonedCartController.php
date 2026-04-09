<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Mail\AbandonedCartMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AbandonedCartController extends Controller
{
    // List all abandoned carts
    public function index()
    {
        $leads = AbandonedCart::latest()->get();
        return view('lead', compact('leads'));
    }

    // Send recovery email
    public function sendMail($id)
    {
        $cart = AbandonedCart::with(['user', 'product'])->findOrFail($id);

        // Send the email
        Mail::to($cart->user->email)
            ->send(new AbandonedCartMail($cart));

        // Update email_sent = 1
        $cart->update(['email_sent' => 1]);

        return back()->with('success', 'Recovery email sent to ' . $cart->user->name);
    }
}
