<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body         { margin:0; padding:0; background:#f1f3f6; font-family:'Segoe UI',Arial,sans-serif; color:#212121; }
    .wrap        { max-width:580px; margin:32px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .header      { background:#2874f0; padding:26px 32px; text-align:center; }
    .header h1   { margin:0; font-size:22px; font-weight:800; color:#fff; }
    .header h1 span { color:#ffd200; }
    .body        { padding:32px; }
    .greeting    { font-size:17px; font-weight:700; margin-bottom:6px; }
    .subtext     { color:#878787; font-size:14px; margin-bottom:24px; line-height:1.6; }
    .item        { display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid #f1f3f6; }
    .item:last-child { border-bottom:none; }
    .item-img    { width:58px; height:58px; background:#f8f9fa; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0; overflow:hidden; }
    .item-img img{ width:100%; height:100%; object-fit:contain; }
    .item-name   { font-size:13px; font-weight:600; margin-bottom:3px; }
    .item-meta   { font-size:12px; color:#878787; }
    .item-price  { margin-left:auto; font-size:15px; font-weight:800; white-space:nowrap; }
    .cta         { text-align:center; margin:28px 0 8px; }
    .cta a       { display:inline-block; background:#fb641b; color:#fff; font-size:15px; font-weight:800; padding:14px 40px; border-radius:4px; text-decoration:none; }
    .offer       { background:#fff9e6; border:1.5px dashed #ffd200; border-radius:6px; padding:14px 20px; margin:20px 0; text-align:center; }
    .offer-code  { font-size:20px; font-weight:800; letter-spacing:3px; color:#212121; }
    .offer p     { margin:4px 0 0; font-size:12px; color:#878787; }
    .footer      { background:#f8f9fa; padding:18px 32px; text-align:center; border-top:1px solid #e0e0e0; }
    .footer p    { margin:0; font-size:11px; color:#aaa; line-height:1.8; }
    .footer a    { color:#2874f0; text-decoration:none; }
</style>
</head>
<body>
<div class="wrap">

    <div class="header">
        <h1>New<span>Bazzar</span></h1>
    </div>

    <div class="body">

        <p class="greeting">Hey {{ $user->name }}, your cart misses you! 🛒</p>
        <p class="subtext">
            You added {{ $cartItems->count() }} item{{ $cartItems->count() > 1 ? 's' : '' }}
            to your cart but didn't complete your order. They're still saved for you!
        </p>

        @foreach($cartItems as $item)
        <div class="item">
            <div class="item-img">
                @if($item->product->image)
                    <img src="{{ asset('storage/' . $item->product->image) }}"
                         alt="{{ $item->product->name }}">
                @else
                    🛍️
                @endif
            </div>
            <div>
                <div class="item-name">{{ $item->product->name }}</div>
                <div class="item-meta">Qty: {{ $item->quantity }}</div>
            </div>
            <div class="item-price">
                ₹{{ number_format($item->product->price * $item->quantity, 0) }}
            </div>
        </div>
        @endforeach

        <div class="offer">
            <div class="offer-code">COMEBACK10</div>
            <p>Use this code for 10% off — valid for 48 hours only</p>
        </div>

        <div class="cta">
            <a href="{{ url('/cart') }}">Complete My Purchase →</a>
        </div>

    </div>

    <div class="footer">
        <p>
            You got this email because you have items in your cart at
            <a href="{{ url('/') }}">NewBazzar</a>.
        </p>
    </div>

</div>
</body>
</html>