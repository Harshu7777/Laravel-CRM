<?php

// app/Console/Commands/MoveAbandonedCarts.php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\AbandonedCart;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MoveAbandonedCarts extends Command
{
    protected $signature   = 'cart:move-abandoned';
    protected $description = '1 ghante se purani carts ko abandoned_carts mein move karo aur carts se delete karo';

    public function handle()
    {
        $oneHourAgo = Carbon::now()->subHour();

        // 1 ghante se purani carts le aao
        $oldCarts = Cart::where('updated_at', '<', $oneHourAgo)->get();

        $movedCount = 0;

        foreach ($oldCarts as $cartItem) {

            // Abandoned table mein insert/update (Lead banane ke liye)
            AbandonedCart::updateOrCreate(
                [
                    'user_id'    => $cartItem->user_id,
                    'product_id' => $cartItem->product_id,
                ],
                [
                    'quantity'      => $cartItem->quantity,
                    'price'         => $cartItem->price ?? 0,        // agar price column hai
                    'total'         => ($cartItem->quantity * ($cartItem->price ?? 0)),
                    'abandoned_at'  => now(),
                    'email_sent'    => false,     // ya 0
                    'status'        => 'pending', // optional
                ]
            );

            // Purani cart delete kar do (important - warna baar-baar move hota rahega)
            $cartItem->delete();

            $movedCount++;
        }

        $this->info("✅ {$movedCount} carts moved to abandoned_carts successfully!");
        
        // Agar zero bhi ho toh bata do
        if ($movedCount === 0) {
            $this->info("ℹ️ No abandoned carts found older than 1 hour.");
        }
    }
}     