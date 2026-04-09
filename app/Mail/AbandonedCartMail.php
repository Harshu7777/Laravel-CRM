<?php
// app/Mail/AbandonedCartMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $cartItems;

    public function __construct($user, $cartItems)
    {
        $this->user      = $user;
        $this->cartItems = $cartItems;
    }

    public function build()
    {
        return $this->subject('Your cart misses you! 🛒')
                    ->view('emails.abandoned_cart');
    }
}