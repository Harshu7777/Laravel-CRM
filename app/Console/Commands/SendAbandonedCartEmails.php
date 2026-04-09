<?php
// app/Console/Commands/SendAbandonedCartEmails.php

namespace App\Console\Commands;

use App\Mail\AbandonedCartMail;
use App\Models\AbandonedCart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartEmails extends Command
{
    protected $signature   = 'cart:send-abandoned-emails';
    protected $description = 'please send Abandoned cart users recovery email ';

    public function handle()
    {
        // User ke hisaab se group karo, saari items ek saath bhejo
        $pendingCarts = AbandonedCart::where('email_sent', 0)
                            ->with(['user', 'product'])  // eager load
                            ->get()
                            ->groupBy('user_id');         // user wise group

        if ($pendingCarts->isEmpty()) {
            $this->info("ℹ️ No pending Email.");
            return;
        }

        $sentCount = 0;

        foreach ($pendingCarts as $userId => $cartItems) {
            try {
                $user = $cartItems->first()->user;

                if (!$user || !$user->email) {
                    $this->warn("⚠️ User {$userId} email not found, skip.");
                    continue;
                }

                // Ek email mein saari items bhejo
                Mail::to($user->email)->send(new AbandonedCartMail($user, $cartItems));

                // Saari items mark karo email_sent = 1
                AbandonedCart::where('user_id', $userId)
                             ->where('email_sent', 0)
                             ->update(['email_sent' => 1]);

                $sentCount++;
                $this->info("📧 Email sent to: {$user->email} ({$cartItems->count()} items)");

            } catch (\Exception $e) {
                $this->error("❌ Failed for user {$userId}: " . $e->getMessage());
            }
        }

        $this->info("✅ Total {$sentCount} users sent recovery emails successfully!");
    }
}