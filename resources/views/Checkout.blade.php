<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — NewBazzar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg: #07080f;
            --surface: #0f1119;
            --surface2: #161924;
            --border: rgba(255, 255, 255, 0.07);
            --text: #eef0f8;
            --muted: #6b7280;
            --accent: #4f8ef7;
            --accent2: #a78bfa;
            --green: #34d399;
            --red: #f87171;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Noise overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        h1,
        h2,
        h3,
        .brand {
            font-family: 'Syne', sans-serif;
        }

        /* ── Navbar ── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5vw;
            height: 68px;
            background: rgba(7, 8, 15, 0.85);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-img img {
            overflow: hidden;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .brand span {
            color: var(--accent);
        }

        .nav-back {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            padding: 8px 16px;
            border-radius: 9px;
            transition: all 0.2s;
            border: 1px solid var(--border);
        }

        .nav-back:hover {
            color: var(--text);
            border-color: var(--accent);
        }

        /* ── Steps ── */
        .steps-bar {
            position: fixed;
            top: 68px;
            left: 0;
            right: 0;
            z-index: 999;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 5vw;
            display: flex;
            align-items: center;
            gap: 0;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--muted);
        }

        .step.active {
            color: var(--accent);
        }

        .step.done {
            color: var(--green);
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .step.active .step-num {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .step.done .step-num {
            background: var(--green);
            border-color: var(--green);
            color: #07080f;
        }

        .step-divider {
            width: 40px;
            height: 1px;
            background: var(--border);
            margin: 0 8px;
        }

        /* ── Main Layout ── */
        .page {
            position: relative;
            z-index: 1;
            padding: 160px 5vw 60px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 28px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
        }

        .card h2 i {
            color: var(--accent);
            font-size: 1rem;
        }

        /* ── Form ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 500;
            letter-spacing: 0.03em;
        }

        .form-group input,
        .form-group select {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 142, 247, 0.1);
        }

        .form-group input::placeholder {
            color: var(--muted);
        }

        .form-group select option {
            background: var(--surface2);
        }

        /* ── Payment Options ── */
        .pay-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .pay-opt {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.82rem;
            color: var(--muted);
        }

        .pay-opt i {
            display: block;
            font-size: 1.4rem;
            margin-bottom: 6px;
        }

        .pay-opt:hover {
            border-color: var(--accent);
            color: var(--text);
        }

        .pay-opt.active {
            border-color: var(--accent);
            background: rgba(79, 142, 247, 0.1);
            color: var(--accent);
        }

        /* ── Order Summary ── */
        .order-summary {
            position: sticky;
            top: 140px;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .cart-item-row:last-of-type {
            border-bottom: none;
        }

        .item-img {
            width: 52px;
            height: 52px;
            background: var(--surface2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            border: 1px solid var(--border);
        }

        .item-details {
            flex: 1;
        }

        .item-details .name {
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--text);
        }

        .item-details .qty {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .item-price {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--accent);
        }

        /* ── Summary Rows ── */
        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .summary-line.total {
            font-family: 'Syne', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text);
            border-top: 1px solid var(--border);
            padding-top: 14px;
            margin-top: 6px;
        }

        .summary-line .free {
            color: var(--green);
            font-weight: 600;
        }

        /* ── Place Order Button ── */
        .btn-place {
            width: 100%;
            padding: 18px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: white;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            margin-top: 18px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-place:hover {
            opacity: 0.88;
            transform: translateY(-1px);
        }

        .btn-place:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Trust Badges ── */
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 16px;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .trust-item i {
            color: var(--green);
            font-size: 0.85rem;
        }

        /* ── Success Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            max-width: 420px;
            width: 90%;
        }

        .modal .success-icon {
            width: 72px;
            height: 72px;
            background: rgba(52, 211, 153, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--green);
        }

        .modal h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .modal p {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 28px;
        }

        .btn-continue {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: white;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            font-family: 'Syne', sans-serif;
        }

        /* ── Empty Cart Warning ── */
        .empty-warning {
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            color: var(--red);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .page {
                grid-template-columns: 1fr;
                padding-top: 140px;
            }

            .order-summary {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .pay-options {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar">
        <a href="{{ url('index') }}" class="brand">
            <div class="brand-icon">🛍️</div>
            New<span>Bazzar</span>
        </a>
        <a href="{{ url('index') }}" class="nav-back">
            <i class="bi bi-arrow-left"></i> Back to Shop
        </a>
    </nav>

    {{-- Steps Bar --}}
    <div class="steps-bar">
        <div class="step done">
            <div class="step-num"><i class="bi bi-check"></i></div>
            Cart
        </div>
        <div class="step-divider"></div>
        <div class="step active">
            <div class="step-num">2</div>
            Checkout
        </div>
        <div class="step-divider"></div>
        <div class="step">
            <div class="step-num">3</div>
            Confirmation
        </div>
    </div>

    {{-- Main Page --}}
    <div class="page">

        {{-- LEFT SIDE — Forms --}}
        <div>

            {{-- Session Success --}}
            @if (session('success'))
                <div
                    style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;color:var(--green);font-size:0.88rem;display:flex;align-items:center;gap:10px;">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Contact Information --}}
            <div class="card">
                <h2><i class="bi bi-person-circle"></i> Contact Information</h2>

                <div class="form-group">
                    <label>Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="full_name" placeholder="Rahul Sharma"
                        value="{{ auth()->user()->name ?? '' }}" required />
                </div>

                <div class="form-group">
                    <label>Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="email" placeholder="rahul@example.com"
                        value="{{ auth()->user()->email ?? '' }}" required />
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="phone" placeholder="+91 98765 43210" />
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="card">
                <h2><i class="bi bi-geo-alt"></i> Shipping Address</h2>

                <div class="form-group">
                    <label>Street Address <span class="text-danger">*</span></label>
                    <textarea id="address" rows="2" placeholder="123, MG Road, Sector 17" required>{{ old('address', auth()->user()->address ?? '') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>City <span class="text-danger">*</span></label>
                        <input type="text" id="city" placeholder="Jalandhar / Chandigarh" required />
                    </div>
                    <div class="form-group">
                        <label>PIN Code <span class="text-danger">*</span></label>
                        <input type="text" id="zip_code" placeholder="144001" maxlength="6" required />
                    </div>
                </div>

                <div class="form-group">
                    <label>State</label>
                    <select id="state">
                        <option value="">Select State</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Haryana">Haryana</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Himachal Pradesh">Himachal Pradesh</option>
                        <option value="Uttar Pradesh">Uttar Pradesh</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Gujarat">Gujarat</option>
                        <option value="Rajasthan">Rajasthan</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Tamil Nadu">Tamil Nadu</option>
                    </select>
                </div>
            </div>

            {{-- Payment --}}
            <div class="card">
                <h2><i class="bi bi-credit-card"></i> Payment Method</h2>

                <div class="pay-options">
                    <div class="pay-opt active" onclick="selectPayment(this, 'card')">
                        <i class="bi bi-credit-card-2-front"></i>
                        Pay with Stripe
                    </div>
                    <div class="pay-opt" onclick="selectPayment(this, 'upi')">
                        <i class="bi bi-phone"></i>
                        UPI
                    </div>
                    <div class="pay-opt" onclick="selectPayment(this, 'cod')">
                        <i class="bi bi-cash-stack"></i>
                        COD
                    </div>
                </div>

                {{-- Card Fields --}}
                <div id="fields-card">

                </div>

                {{-- UPI Fields --}}
                <div id="fields-upi" style="display:none;">
                    <div class="form-group">
                        <label>UPI ID</label>
                        <input type="text" placeholder="rahul@upi" />
                    </div>
                </div>

                {{-- COD --}}
                <div id="fields-cod" style="display:none;">
                    <div
                        style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.2);border-radius:10px;padding:14px 16px;font-size:0.85rem;color:#fbbf24;">
                        <i class="bi bi-info-circle"></i>
                        Cash on Delivery — Pay when your order arrives. Extra ₹50 COD charge applicable.
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT SIDE — Order Summary --}}
        <div class="order-summary">
            <div class="card">
                <h2><i class="bi bi-bag-check"></i> Order Summary</h2>

                {{-- Cart Items — JS se populate hoga --}}
                <div id="checkoutItems">
                    <div style="text-align:center;padding:30px;color:var(--muted);font-size:0.88rem;">
                        <i class="bi bi-bag" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        Cart load ho raha hai...
                    </div>
                </div>

                {{-- Totals --}}
                <div style="margin-top:16px;">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span id="checkoutSubtotal">₹0.00</span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping</span>
                        <span class="free">FREE</span>
                    </div>
                    <div class="summary-line">
                        <span>GST (18%)</span>
                        <span id="checkoutGST">₹0.00</span>
                    </div>
                    <div class="summary-line total">
                        <span>Total</span>
                        <span id="checkoutTotal">₹0.00</span>
                    </div>
                </div>

                <button class="btn-place" id="placeOrderBtn" onclick="placeOrder()">
                    <i class="bi bi-lock-fill"></i>
                    Place Order Securely
                </button>

                <div class="trust-badges">
                    <div class="trust-item">
                        <i class="bi bi-shield-check"></i> Secure
                    </div>
                    <div class="trust-item">
                        <i class="bi bi-arrow-counterclockwise"></i> Easy Returns
                    </div>
                    <div class="trust-item">
                        <i class="bi bi-truck"></i> Fast Delivery
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Success Modal --}}
    <div class="modal-overlay" id="successModal">
        <div class="modal">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2>Order Placed! 🎉</h2>
            <p>Aapka order successfully place ho gaya hai.<br>
                Confirmation email aapke inbox mein aayega.</p>
            <a href="{{ url('/') }}" class="btn-continue">
                Continue Shopping
            </a>
        </div>
    </div>

    <script>
        // ── Load cart from localStorage ──
        let cartData = JSON.parse(localStorage.getItem('cart')) || [];

        document.addEventListener('DOMContentLoaded', () => {
            renderCheckoutItems();
        });

        function renderCheckoutItems() {
            const container = document.getElementById('checkoutItems');
            const placeOrderBtn = document.getElementById('placeOrderBtn');

            if (!cartData || cartData.length === 0) {
                container.innerHTML = `
                <div style="text-align:center;padding:40px 20px;color:var(--muted);font-size:0.9rem;">
                    <i class="bi bi-bag" style="font-size:2.5rem;opacity:0.25;display:block;margin-bottom:12px;"></i>
                    Cart khaali hai!<br>
                    Pehle kuch add karo.
                </div>`;
                if (placeOrderBtn) placeOrderBtn.disabled = true;
                return;
            }

            container.innerHTML = '';

            cartData.forEach((item, index) => {
                const div = document.createElement('div');
                div.className = 'cart-item-row';
                div.innerHTML = `
                <div class="item-img">${item.image ? `<img src="${item.image}" alt="${item.name}">` : '🛍️'}</div>
                <div class="item-details">
                    <div class="name">${item.name}</div>
                    <div class="qty">Qty: ${item.qty || item.quantity}</div>
                </div>
                <div class="item-price">₹${(item.price * (item.qty || item.quantity)).toFixed(2)}</div>
            `;
                container.appendChild(div);
            });

            updateTotals();
            if (placeOrderBtn) placeOrderBtn.disabled = false;
        }

        function updateTotals() {
            const subtotal = cartData.reduce((sum, item) => {
                return sum + (item.price * (item.qty || item.quantity));
            }, 0);

            const gst = subtotal * 0.18;
            const total = subtotal + gst;

            // Update DOM elements
            const subtotalEl = document.getElementById('checkoutSubtotal');
            const gstEl = document.getElementById('checkoutGST');
            const totalEl = document.getElementById('checkoutTotal');

            if (subtotalEl) subtotalEl.textContent = `₹${subtotal.toFixed(2)}`;
            if (gstEl) gstEl.textContent = `₹${gst.toFixed(2)}`;
            if (totalEl) totalEl.textContent = `₹${total.toFixed(2)}`;
        }

        // ── Payment Method Selection ──
        function selectPayment(el, type) {
            // Remove active class from all
            document.querySelectorAll('.pay-opt').forEach(e => e.classList.remove('active'));

            // Add active to clicked
            el.classList.add('active');

            // Show/hide payment fields
            document.getElementById('fields-card').style.display = (type === 'card') ? 'block' : 'none';
            document.getElementById('fields-upi').style.display = (type === 'upi') ? 'block' : 'none';
            document.getElementById('fields-cod').style.display = (type === 'cod') ? 'block' : 'none';
        }

        // ── Card Number Formatting (XXXX XXXX XXXX XXXX) ──
        function formatCard(el) {
            let value = el.value.replace(/\D/g, '').substring(0, 16);
            el.value = value.replace(/(.{4})/g, '$1 ').trim();
        }

        // ── Expiry Date Formatting (MM / YY) ──
        function formatExpiry(el) {
            let value = el.value.replace(/\D/g, '').substring(0, 4);
            if (value.length >= 2) {
                value = value.substring(0, 2) + ' / ' + value.substring(2);
            }
            el.value = value;
        }

        async function placeOrder() {
            const btn = document.getElementById('placeOrderBtn');

            // Disable button
            btn.disabled = true;
            btn.innerHTML = `<i class="bi bi-hourglass-split"></i> Processing...`;

            // Collect form data
            const payload = {
                name: document.getElementById('full_name').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                address: document.getElementById('address').value.trim(),
                city: document.getElementById('city').value.trim(),
                zip_code: document.getElementById('zip_code').value.trim(),
            };

            // Basic validation
            if (!payload.name || !payload.email || !payload.address || !payload.city || !payload.zip_code) {
                alert("Please fill all required fields (*)");
                btn.disabled = false;
                btn.innerHTML = `Place Order`;
                return;
            }

            try {
                const response = await fetch('/create-checkout-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error("Server responded with:", errorText);
                    throw new Error(`HTTP Error: ${response.status}`);
                }

                const data = await response.json();

                if (data.url) {
                    window.location.href = data.url;
                } else if (data.error) {
                    throw new Error(data.error);
                } else {
                    throw new Error("No payment URL received");
                }

            } catch (error) {
                console.error("Order placement error:", error);
                alert("Error: " + error.message + "\n\nPlease try again.");
            } finally {
                // Reset button in case of error
                btn.disabled = false;
                btn.innerHTML = `Place Order`;
            }
        }
    </script>

</body>

</html>
