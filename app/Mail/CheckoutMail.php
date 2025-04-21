<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cart;
    public $total;

    public function __construct($cart, $total)
    {
        $this->cart = $cart;
        $this->total = $total;
    }

    public function build()
    {
        return $this->view('checkout.mail')
                    ->subject('Order Confirmation')
                    ->with([
                        'cart' => $this->cart,
                        'total' => $this->total,
                    ]);
    }
}
