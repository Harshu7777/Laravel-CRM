<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        #card-element {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
        }

        .StripeElement {
            padding: 10px;
        }

        .StripeElement--invalid {
            border-color: red;
        }
    </style>
</head>

<body>

    <h2>Checkout</h2>

    <div class="main-div">
        <h6>Order Smmary</h6>
        <div>
            <span>Subtotal</span><span>$190.00</span>
        </div>
        <div>
            <span>Shipping</span><span>$10.00</span>
        </div>
        <div>
            <span>Total</span><span>$200.00</span>
        </div>

        <form action="{{ route('stripe.payment') }}" method="POST" id="stripe-form"
            onsubmit="createToken(); return false;">
            @csrf

            <input type="hidden" name="price" id="200">

            <input type="hidden" name="StripeToken" id="stripe-token">

            <div id="card-element" class="form-control"></div>

            <button type="submit">Submit</button>
        </form>

    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        var stripe = Stripe("{{ env('STRIPE_KEY') }}");
        var elements = stripe.elements(); 
        var cardElement = elements.create('card'); 
        cardElement.mount('#card-element');

        function createToken() {
            stripe.createToken(cardElement).then(function(result) {
                console.log(result);
                if (result.token) {
                    document.getElementById('stripe-token').value = result.token.id; 
                    document.getElementById("stripe-form").submit();
                } else if (result.error) {
                    console.error(result.error.message);
                }
            });
        }
    </script>

</body>

</html>
