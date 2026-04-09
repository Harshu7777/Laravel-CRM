<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center py-5">
                    
                    <!-- Green Success Icon -->
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem;"></i>
                    </div>

                    <h1 class="display-4 fw-bold text-success mb-3">Payment Successful!</h1>
                    <h4 class="text-muted mb-4">Thank you for your purchase</h4>

                    <div class="alert alert-success mb-4">
                        <strong>Order Placed Successfully</strong><br>
                        Your payment has been processed securely via Stripe.
                    </div>

                    @if(isset($session_id))
                    <p class="text-muted small mb-4">
                        Transaction Reference: <strong>{{ $session_id }}</strong>
                    </p>
                    @endif

                    <div class="d-grid gap-3 d-md-flex justify-content-center">
                        <!-- View Orders Button -->
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-list-check"></i> View My Orders
                        </a>

                        <!-- Continue Shopping Button -->
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg px-5">
                            <i class="bi bi-shop"></i> Continue Shopping
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>