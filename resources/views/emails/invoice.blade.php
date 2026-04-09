@component('mail::message')
# Payment Successful - Order Invoice

Hello **{{ $order->full_name }}**,

Thank you for your purchase! Your payment has been successfully received.

**Order ID:** #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}  
**Total Amount:** ₹{{ number_format($order->total_amount, 2) }}

Click the button below to download your official invoice:

@component('mail::button', ['url' => route('orders.downloadInvoice', $order->id)])
📥 Download Invoice PDF
@endcomponent


Thank you for shopping with us!

Regards,  
**NewBazzar** Team
@endcomponent