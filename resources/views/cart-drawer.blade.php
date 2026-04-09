<!-- ── CART DRAWER HTML ── -->
<div id="cartDrawer" class="cart-drawer">
    <div class="cart-header">
        <h3>Your Cart (<span id="cartCount">0</span>)</h3>
        <button onclick="closeCart()"
            style="background:none;border:none;color:var(--muted);font-size:1.8rem;cursor:pointer;">×</button>
    </div>

    <div id="cartItems" class="cart-items"></div>

    <div id="cartFooter" style="padding:24px;border-top:1px solid var(--border);">
        <div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:1.1rem;">
            <span>Subtotal</span>
            <span id="cartSubtotal"
                style="font-family:'Syne',sans-serif;font-weight:800;color:var(--accent);">$0.00</span>
        </div>
        <button onclick="checkout()"
            style="width:100%;padding:18px 20px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;font-weight:700;font-size:1.05rem;border:none;cursor:pointer;">
            Proceed to Checkout
        </button>
        <button onclick="clearCart()"
            style="width:100%;margin-top:12px;padding:14px;border-radius:12px;background:transparent;border:1px solid var(--border);color:var(--muted);font-weight:500;cursor:pointer;">
            Clear Cart
        </button>
    </div>
</div>

<div id="cartOverlay" class="cart-overlay" onclick="closeCart()"></div>

<script>
    /* ─────────────────────────────────────────
       OpenCart  –  localStorage + API sync
    ───────────────────────────────────────── */
    class OpenCart {
        constructor() {
            this.cart = JSON.parse(localStorage.getItem("cart")) || [];
        }

        saveCart() {
            localStorage.setItem("cart", JSON.stringify(this.cart));
        }

        addItem(item) {
            const existing = this.cart.find(i => i.id === item.id);
            if (existing) {
                existing.qty += 1;
            } else {
                this.cart.push({ ...item, qty: 1 });
            }
            this.saveCart();
            this.updateNavBadge("cartNavBadge");

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: item.id,
                    quantity: item.qty || 1
                })
            });
        }

        removeItem(id) {
            this.cart = this.cart.filter(i => i.id !== id);
            this.saveCart();
            this.updateNavBadge("cartNavBadge");

            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) console.error('DB remove failed!');
            })
            .catch(err => console.error('Remove error:', err));
        }

        updateQty(id, qty) {
            if (qty < 1) return this.removeItem(id);
            const item = this.cart.find(i => i.id === id);
            if (item) item.qty = qty;
            this.saveCart();
            this.updateNavBadge("cartNavBadge");
        }

        getTotal() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
        }

        clearCart() {
            this.cart = [];
            this.saveCart();
            this.updateNavBadge("cartNavBadge");
        }

        getItems() {
            return this.cart;
        }

        updateNavBadge(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const total = this.cart.reduce((sum, i) => sum + i.qty, 0);
            el.style.display = total > 0 ? "inline-block" : "none";
            el.innerText = total;
        }
    }

    /* ─────────────────────────────────────────
       CartDrawer  –  UI rendering
    ───────────────────────────────────────── */
    class CartDrawer {
        constructor(cart) {
            this.cart = cart;
            this.drawer = document.getElementById("cartDrawer");
            this.overlay = document.getElementById("cartOverlay");
            this.itemsContainer = document.getElementById("cartItems");
            this.countEl = document.getElementById("cartCount");
            this.subtotalEl = document.getElementById("cartSubtotal");
        }

        open() {
            this.renderItems();
            this.drawer.classList.add("open");
            this.overlay.style.display = "block";
        }

        close() {
            this.drawer.classList.remove("open");
            this.overlay.style.display = "none";
        }

        renderItems() {
            const items = this.cart.getItems();
            this.itemsContainer.innerHTML = "";

            if (!items.length) {
                this.itemsContainer.innerHTML = `
                    <div style="text-align:center;padding:80px 20px;color:var(--muted);">
                        <i class="bi bi-bag" style="font-size:4rem;opacity:0.2;margin-bottom:20px;display:block;"></i>
                        <p style="font-size:1.1rem;">Your cart is empty</p>
                        <p style="font-size:0.9rem;">Start adding some awesome products!</p>
                    </div>`;
                this.updateFooter(0);
                return;
            }

            items.forEach(item => {
                const div = document.createElement("div");
                div.className = "cart-item";
                div.innerHTML = `
                    <div class="cart-item-img">${item.image ? `<img src="${item.image}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">` : '🛍️'}</div>
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">$${item.price}</div>
                        <div class="cart-qty">
                            <button onclick="cartDrawer.updateItemQty('${item.id}', ${item.qty - 1})">–</button>
                            <span style="font-weight:700;min-width:28px;text-align:center;">${item.qty}</span>
                            <button onclick="cartDrawer.updateItemQty('${item.id}', ${item.qty + 1})">+</button>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <button onclick="cartDrawer.removeItem('${item.id}')"
                            style="background:none;border:none;color:var(--red);font-size:1.6rem;margin-top:12px;cursor:pointer;">×</button>
                    </div>`;
                this.itemsContainer.appendChild(div);
            });

            this.updateFooter(this.cart.getTotal());
        }

        updateFooter(total) {
            this.countEl.textContent = this.cart.getItems().reduce((sum, i) => sum + i.qty, 0);
            this.subtotalEl.textContent = `$${total.toFixed(2)}`;
        }

        removeItem(id) {
            this.cart.removeItem(id);
            this.renderItems();
        }

        updateItemQty(id, qty) {
            this.cart.updateQty(id, qty);
            this.renderItems();
        }
    }

    /* ─────────────────────────────────────────
       Global instances  –  immediately created
       (window.openCart etc. work right away,
        even before DOMContentLoaded)
    ───────────────────────────────────────── */
    const cart = new OpenCart();
    let cartDrawer = new CartDrawer(cart);

    // ✅ Global helpers – defined OUTSIDE load event so onclick="openCart()" always works
    window.openCart  = () => cartDrawer.open();
    window.closeCart = () => cartDrawer.close();
    window.checkout  = () => {
        if (cart.getItems().length === 0) return;
        window.location.href = '/checkout';
    };
    window.clearCart = () => {
        if (confirm("Empty the entire cart?")) {
            cart.clearCart();
            cartDrawer.renderItems();
        }
    };

    // Make cartDrawer globally accessible (needed by inline onclick in renderItems)
    window.cartDrawer = cartDrawer;

    /* ─────────────────────────────────────────
       On load: sync DB cart (auth users) +
       badge update + observers + add-to-cart btns
    ───────────────────────────────────────── */
    window.addEventListener("load", () => {

        @auth
        fetch('/cart/items')
            .then(res => res.json())
            .then(dbItems => {
                // Remove localStorage items not in DB
                const dbProductIds = dbItems.map(i => String(i.product_id));
                cart.cart = cart.cart.filter(i => dbProductIds.includes(String(i.id)));

                // Add DB items missing from localStorage
                dbItems.forEach(dbItem => {
                    const existing = cart.cart.find(i => i.id == dbItem.product_id);
                    if (!existing) {
                        cart.cart.push({
                            id: String(dbItem.product_id),
                            name: dbItem.product.name,
                            price: parseFloat(dbItem.product.price),
                            qty: dbItem.quantity
                        });
                    }
                });

                cart.saveCart();
                cart.updateNavBadge("cartNavBadge");
            });
        @endauth

        // Badge update for guests too
        cart.updateNavBadge("cartNavBadge");

        // ── Scroll reveal observer ──
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), i * 80);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // ── Add-to-cart buttons ──
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id    = this.dataset.id;
                const name  = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                const image = this.dataset.image;  

                cart.addItem({ id, name, price , image });
                cart.updateNavBadge("cartNavBadge");

                // Visual feedback
                const original = this.innerHTML;
                this.style.transition = "all 0.2s";
                this.innerHTML = `<i class="bi bi-check-circle"></i> Added!`;
                this.style.background = "rgba(52,211,153,0.3)";
                this.style.color = "var(--green)";

                setTimeout(() => {
                    this.innerHTML = original;
                    this.style = "";
                }, 1400);
            });
        });

    });
</script>