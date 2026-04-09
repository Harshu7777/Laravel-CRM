<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\AbandonedCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Cart dikhao
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())
                         ->with('product')
                         ->get();

        return view('cart', compact('cartItems'));
    }

    public function getItems()
    {
        $items = Cart::where('user_id', auth()->id())
                    ->with('product')
                    ->get();

        return response()->json($items);
    }

    // Product add karo cart mein
    public function addToCart(Request $request)
    {
        Cart::updateOrCreate(
            [
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $request->quantity ?? 1,
            ]
        );

        // Abandoned cart se DELETE 
        AbandonedCart::where('user_id', auth()->id())
                     ->where('product_id', $request->product_id)
                     ->delete();

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    // Product remove karo cart se
    public function removeFromCart(Request $request)
    {
        $cartItem = Cart::where('user_id', auth()->id())
                        ->where('product_id', $request->product_id)
                        ->first();

        if ($cartItem) {
            AbandonedCart::updateOrCreate(
                [
                    'user_id'    => auth()->id(),
                    'product_id' => $request->product_id,
                ],
                [
                    'quantity'     => $cartItem->quantity,
                    'email_sent'   => 0,
                    'abandoned_at' => now(),
                ]
            );

            $cartItem->delete();
        }

        return redirect()->back()->with('success', 'Product removed!');
    }

    // Cart clear karo checkout pe
    public function clearCart()
    {
        Cart::where('user_id', auth()->id())->delete();

        // Abandoned cart bhi saaf 
        AbandonedCart::where('user_id', auth()->id())->delete();

        return redirect()->route('checkout');
    }
}