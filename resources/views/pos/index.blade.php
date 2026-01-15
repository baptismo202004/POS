<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Cashier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #1a202c; /* slate-900 */
            color: #e2e8f0; /* slate-200 */
        }
        .font-mono-receipt {
            font-family: 'Consolas', 'Menlo', 'Courier New', monospace;
        }
        .pos-grid {
            display: grid;
            grid-template-rows: auto 1fr auto;
            grid-template-columns: 1fr 450px;
            height: 100vh;
            gap: 1rem;
            padding: 1rem;
        }
        .header { grid-area: 1 / 1 / 2 / 3; }
        .product-input-zone { grid-area: 2 / 1 / 3 / 2; }
        .cart-view { grid-area: 2 / 2 / 4 / 3; }
        .action-panel { grid-area: 3 / 1 / 4 / 2; }

        .card {
            background-color: #2d3748; /* slate-800 */
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .input-main {
            background-color: #1f2937; /* slate-800 */
            border: 2px solid #4b5563; /* slate-600 */
            color: #f9fafb; /* slate-50 */
            font-size: 2rem;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .input-main:focus {
            outline: none;
            border-color: #60a5fa; /* blue-400 */
        }
        .cart-item.active {
            background-color: #3b82f6; /* blue-600 */
            color: #ffffff;
        }
        .action-button {
            background-color: #4b5563; /* slate-600 */
            border: 1px solid #6b7280; /* slate-500 */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }
        .action-button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .payment-tile {
            background-color: #4b5563; /* slate-600 */
            border: 2px solid transparent;
        }
        .payment-tile.active {
            border-color: #60a5fa; /* blue-400 */
        }
    </style>
</head>
<body class="h-full">

    <div class="pos-grid">
        <!-- Header -->
        <header class="header card flex justify-between items-center text-sm p-2">
            <div class="font-bold text-lg">GROCERY POS</div>
            <div class="flex items-center gap-4">
                <span><i class="fas fa-user mr-1"></i>Cashier: Jane Doe</span>
                <span><i class="fas fa-desktop mr-1"></i>Terminal: T1</span>
                <span><i class="fas fa-sun mr-1"></i>Shift: Morning</span>
            </div>
            <div id="current-time" class="font-mono-receipt text-lg"></div>
        </header>

        <!-- Product Input Zone -->
        <div class="product-input-zone card flex flex-col">
            <input type="text" id="product-input" class="input-main w-full" placeholder="Scan or Enter SKU...">
            <div id="error-message" class="text-red-400 h-6 mt-2"></div>
        </div>

        <!-- Cart & Totals View -->
        <div class="cart-view card flex flex-col">
            <div class="grow overflow-y-auto">
                <table class="w-full font-mono-receipt text-lg">
                    <tbody id="cart-items">
                        <!-- Example Item -->
                        <tr class="cart-item active">
                            <td class="p-2">Whole Milk 1L</td>
                            <td class="p-2 text-center">1</td>
                            <td class="p-2 text-right">₱85.00</td>
                            <td class="p-2 text-right font-bold">₱85.00</td>
                        </tr>
                        <tr class="cart-item">
                            <td class="p-2">Loaf Bread</td>
                            <td class="p-2 text-center">2</td>
                            <td class="p-2 text-right">₱70.00</td>
                            <td class="p-2 text-right font-bold">₱140.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-auto pt-4 border-t-2 border-dashed border-slate-600">
                <div class="space-y-2 text-xl font-mono-receipt">
                    <div class="flex justify-between"><span>Subtotal</span><span id="subtotal">₱225.00</span></div>
                    <div class="flex justify-between"><span>Tax (12%)</span><span id="tax">₱27.00</span></div>
                    <div class="flex justify-between text-red-400"><span>Discount</span><span id="discount">-₱0.00</span></div>
                    <div class="flex justify-between text-3xl font-bold mt-2 pt-2 border-t border-slate-600"><span>TOTAL</span><span id="total">₱252.00</span></div>
                </div>
                <div class="mt-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="payment-tile active p-4 rounded-md text-center">
                            <i class="fas fa-money-bill-wave text-2xl mb-1"></i>
                            <div>Cash</div>
                        </div>
                        <div class="payment-tile p-4 rounded-md text-center">
                            <i class="fas fa-credit-card text-2xl mb-1"></i>
                            <div>Card</div>
                        </div>
                        <div class="payment-tile p-4 rounded-md text-center">
                            <i class="fas fa-qrcode text-2xl mb-1"></i>
                            <div>eWallet</div>
                        </div>
                    </div>
                    <input type="text" id="cash-input" class="input-main w-full mt-2 text-center" placeholder="Enter cash amount...">
                    <div class="text-center mt-2 text-2xl">Change Due: <span class="font-bold">₱0.00</span></div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="action-panel card grid grid-cols-4 grid-rows-2 gap-2">
            <button class="action-button"><i class="fas fa-plus-circle text-2xl mb-1"></i> F1 New</button>
            <button class="action-button"><i class="fas fa-edit text-2xl mb-1"></i> F2 Qty</button>
            <button class="action-button"><i class="fas fa-tag text-2xl mb-1"></i> F3 Disc</button>
            <button class="action-button"><i class="fas fa-trash-alt text-2xl mb-1"></i> F4 Remove</button>
            <button class="action-button"><i class="fas fa-pause-circle text-2xl mb-1"></i> F5 Hold</button>
            <button class="action-button"><i class="fas fa-play-circle text-2xl mb-1"></i> F6 Resume</button>
            <button class="action-button col-span-2 bg-red-800 border-red-700"><i class="fas fa-times-circle text-2xl mb-1"></i> ESC Cancel</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- STATE MANAGEMENT ---
            const state = {
                cart: [
                    { id: 1, name: 'Whole Milk 1L', qty: 1, price: 85.00 },
                    { id: 2, name: 'Loaf Bread', qty: 2, price: 70.00 },
                ],
                activeCartItemIndex: 0,
                paymentMethod: 'cash', // 'cash', 'card', 'ewallet'
                uiMode: 'scan', // 'scan', 'payment', 'end'
            };

            // --- DOM ELEMENTS ---
            const productInput = document.getElementById('product-input');
            const cartItemsContainer = document.getElementById('cart-items');
            const currentTimeEl = document.getElementById('current-time');
            const errorMessageEl = document.getElementById('error-message');
            const cashInput = document.getElementById('cash-input');
            const paymentTiles = document.querySelectorAll('.payment-tile');
            const totalsContainer = document.querySelector('.mt-auto.pt-4');

            // --- MOCK DATA ---
            const products = {
                '12345': { name: 'Canned Tuna', price: 45.50 },
                '67890': { name: 'Instant Noodles', price: 15.00 },
                '11223': { name: 'Soda 1.5L', price: 60.00 },
            };

            // --- RENDER FUNCTIONS ---
            function renderCart() {
                cartItemsContainer.innerHTML = '';
                if (state.cart.length === 0) {
                    cartItemsContainer.innerHTML = `<tr><td colspan="4" class="text-center p-4 text-slate-400">Cart is empty</td></tr>`;
                } else {
                    state.cart.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.classList.add('cart-item');
                        if (index === state.activeCartItemIndex) {
                            tr.classList.add('active');
                        }
                        tr.dataset.index = index;
                        const lineTotal = (item.qty * item.price).toFixed(2);
                        tr.innerHTML = `
                            <td class="p-2">${item.name}</td>
                            <td class="p-2 text-center">${item.qty}</td>
                            <td class="p-2 text-right">₱${item.price.toFixed(2)}</td>
                            <td class="p-2 text-right font-bold">₱${lineTotal}</td>
                        `;
                        cartItemsContainer.appendChild(tr);
                    });
                }
                renderTotals();
            }

            function renderTotals() {
                const subtotal = state.cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
                const tax = subtotal * 0.12;
                const discount = 0; // Placeholder
                const total = subtotal + tax - discount;

                totalsContainer.querySelector('#subtotal').innerText = `₱${subtotal.toFixed(2)}`;
                totalsContainer.querySelector('#tax').innerText = `₱${tax.toFixed(2)}`;
                totalsContainer.querySelector('#discount').innerText = `-₱${discount.toFixed(2)}`;
                totalsContainer.querySelector('#total').innerText = `₱${total.toFixed(2)}`;
            }

            // --- EVENT HANDLERS & LOGIC ---
            function handleProductInput(e) {
                if (e.key === 'Enter') {
                    const code = productInput.value.trim();
                    if (!code) return;

                    const product = products[code];
                    if (product) {
                        const existingItem = state.cart.find(item => item.name === product.name);
                        if (existingItem) {
                            existingItem.qty++;
                        } else {
                            state.cart.push({ ...product, qty: 1 });
                        }
                        state.activeCartItemIndex = state.cart.length - 1;
                        renderCart();
                        productInput.value = '';
                        errorMessageEl.innerText = '';
                    } else {
                        errorMessageEl.innerText = `Product not found: ${code}`;
                        productInput.select();
                    }
                }
            }

            function navigateCart(direction) {
                const newIndex = state.activeCartItemIndex + direction;
                if (newIndex >= 0 && newIndex < state.cart.length) {
                    state.activeCartItemIndex = newIndex;
                    renderCart();
                }
            }

            function startPayment() {
                if (state.cart.length === 0) return;
                state.uiMode = 'payment';
                cashInput.focus();
                cashInput.placeholder = totalsContainer.querySelector('#total').innerText;
            }

            function finishSale() {
                console.log('Sale Confirmed', state);
                state.uiMode = 'end';
                // Simulate showing change and waiting for next sale
                setTimeout(() => {
                    resetForNewSale();
                }, 2000); // Wait 2 seconds before resetting
            }

            function resetForNewSale() {
                state.cart = [];
                state.activeCartItemIndex = 0;
                state.uiMode = 'scan';
                cashInput.value = '';
                cashInput.placeholder = 'Enter cash amount...';
                renderCart();
                productInput.focus();
            }

            // --- GLOBAL KEYBOARD LISTENER ---
            document.addEventListener('keydown', function (e) {
                if (state.uiMode === 'end') {
                    e.preventDefault();
                    return; // Block input while sale is finalizing
                }

                // Global shortcuts
                if (e.key === 'F1') { e.preventDefault(); resetForNewSale(); }
                if (e.key === 'Escape') {
                    e.preventDefault();
                    if (confirm('Are you sure you want to cancel this sale?')) {
                        resetForNewSale();
                    }
                }

                // Context-aware shortcuts
                if (state.uiMode === 'scan') {
                    if (document.activeElement !== productInput) {
                        // Cart Navigation
                        switch (e.key) {
                            case 'ArrowUp': e.preventDefault(); navigateCart(-1); break;
                            case 'ArrowDown': e.preventDefault(); navigateCart(1); break;
                            case 'Delete':
                                e.preventDefault();
                                if (state.cart.length > 0) {
                                    state.cart.splice(state.activeCartItemIndex, 1);
                                    if (state.activeCartItemIndex >= state.cart.length) {
                                        state.activeCartItemIndex = Math.max(0, state.cart.length - 1);
                                    }
                                    renderCart();
                                }
                                break;
                            case 'Enter': e.preventDefault(); startPayment(); break;
                            case 'F2':
                                e.preventDefault();
                                const newQty = prompt(`Enter new quantity for ${state.cart[state.activeCartItemIndex].name}:`, state.cart[state.activeCartItemIndex].qty);
                                if (newQty && !isNaN(newQty) && newQty > 0) {
                                    state.cart[state.activeCartItemIndex].qty = parseInt(newQty, 10);
                                    renderCart();
                                }
                                break;
                        }
                    } else if (e.key === 'ArrowDown' && state.cart.length > 0) {
                        e.preventDefault();
                        productInput.blur();
                        renderCart(); // Re-render to ensure active class is set
                    }
                } else if (state.uiMode === 'payment') {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        finishSale();
                    }
                }
            });

            // --- INITIALIZATION ---
            function init() {
                setInterval(() => {
                    currentTimeEl.innerText = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }, 1000);

                productInput.addEventListener('keydown', handleProductInput);
                renderCart();
                productInput.focus();
            }

            init();
        });
    </script>
</body>
</html>
