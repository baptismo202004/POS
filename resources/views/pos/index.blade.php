<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <div class="pos-grid relative">
        <div id="loading-overlay" class="absolute inset-0 bg-slate-900 bg-opacity-75 flex items-center justify-center hidden z-50">
            <i class="fas fa-spinner fa-spin text-white text-6xl"></i>
        </div>
    <!-- Cashier Login Modal -->
    <div id="cashier-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-lg w-96 p-4">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold">Cashier Login</h2>
                <button id="cashier-close" class="px-2 py-1 bg-slate-700 rounded">Esc</button>
            </div>
            <label class="block text-sm mb-1">Employee ID</label>
            <input id="employee-id-input" type="text" class="w-full input-main text-base text-center" placeholder="Enter Employee ID" />
            <div class="flex justify-end gap-2 mt-3">
                <button id="cashier-cancel" class="px-3 py-1 bg-slate-700 rounded">Cancel</button>
                <button id="cashier-apply" class="px-3 py-1 bg-blue-700 rounded">Apply (Enter)</button>
            </div>
        </div>
    </div>

    <!-- Discount Modal -->
    <div id="discount-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-lg w-96 p-4">
            <div class="flex justify-between items-center mb-3">
                <h2 id="discount-title" class="text-lg font-bold">Set Discount</h2>
                <button id="discount-close" class="px-2 py-1 bg-slate-700 rounded">Esc</button>
            </div>
            <label class="block text-sm mb-1" id="discount-label">Discount % (0-100)</label>
            <input id="discount-input" type="number" min="0" max="100" step="0.01" class="w-full input-main text-base" />
            <div class="flex justify-end gap-2 mt-3">
                <button id="discount-cancel" class="px-3 py-1 bg-slate-700 rounded">Cancel</button>
                <button id="discount-apply" class="px-3 py-1 bg-blue-700 rounded">Apply</button>
            </div>
        </div>
    </div>
    <!-- Cash Payment Modal -->
    <div id="cash-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-lg w-96 p-4">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold">Cash Payment</h2>
                <button id="cash-close" class="px-2 py-1 bg-slate-700 rounded">Esc</button>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-xl font-mono-receipt">
                    <span>Total Due</span>
                    <span id="cash-total">₱0.00</span>
                </div>
                <label class="block text-sm mb-1">Cash Tendered</label>
                <input id="cash-tendered" type="number" min="0" step="0.01" class="w-full input-main text-base text-center" />
                <div class="text-center mt-2 text-2xl">Change: <span id="cash-change" class="font-bold">₱0.00</span></div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
                <button id="cash-cancel" class="px-3 py-1 bg-slate-700 rounded">Cancel</button>
                <button id="cash-pay" class="px-3 py-1 bg-green-700 rounded">Pay (Enter)</button>
            </div>
        </div>
    </div>
        <!-- Header -->
        <header class="header card flex justify-between items-center text-sm p-2">
            <div class="font-bold text-lg">GROCERY POS</div>
            <div class="flex items-center gap-4">
                <button id="cashier-display" class="hover:underline"><i class="fas fa-user mr-1"></i>Cashier: <span id="cashier-name">Unassigned</span></button>
                <span><i class="fas fa-desktop mr-1"></i>Terminal: <span id="terminal-name">T1</span></span>
                <span><i class="fas fa-sun mr-1"></i>Shift: <span id="shift-name">—</span></span>
                <button id="btn-shortcuts" class="px-3 py-1 bg-blue-700 rounded hover:bg-blue-600">Shortcuts</button>
            </div>
            <div id="current-time" class="font-mono-receipt text-lg"></div>
        </header>

        <!-- Product Input Zone -->
        <div class="product-input-zone card flex flex-col">
            <input type="text" id="product-input" class="input-main w-full" placeholder="Scan or Enter SKU...">
            <div id="error-message" class="text-red-400 h-6 mt-2"></div>
            <div id="suggestions" class="mt-2 bg-slate-800 border border-slate-700 rounded hidden max-h-64 overflow-y-auto"></div>
        </div>

        <!-- Cart & Totals View -->
        <div class="cart-view card flex flex-col">
            <div class="grow overflow-y-auto">
                <table class="w-full font-mono-receipt text-lg">
                    <tbody id="cart-items">
                        <!-- Cart items are dynamically inserted here -->
                    </tbody>
                </table>
            </div>
            <div class="mt-auto pt-4 border-t-2 border-dashed border-slate-600">
                <div class="space-y-2 text-xl font-mono-receipt">
                    <div class="flex justify-between"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
                    <div class="flex justify-between"><span>Tax (12%)</span><span id="tax">₱0.00</span></div>
                    <div class="flex justify-between text-red-400"><span>Discount</span><span id="discount">-₱0.00</span></div>
                    <div class="flex justify-between text-3xl font-bold mt-2 pt-2 border-t border-slate-600"><span>TOTAL</span><span id="total">₱0.00</span></div>
                </div>
                <div class="mt-4">
                    <div class="grid grid-cols-1 gap-2">
                        <div class="payment-tile active p-4 rounded-md text-center">
                            <i class="fas fa-money-bill-wave text-2xl mb-1"></i>
                            <div>Cash</div>
                            <div class="text-xs mt-1">Open Cash (F9 or Ctrl+Enter)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="action-panel card grid grid-cols-4 grid-rows-2 gap-2">
            <button id="btn-f1" class="action-button"><i class="fas fa-plus-circle text-2xl mb-1"></i> F1 New</button>
            <button id="btn-f2" class="action-button"><i class="fas fa-edit text-2xl mb-1"></i> F2 Qty</button>
            <button id="btn-f3" class="action-button"><i class="fas fa-tag text-2xl mb-1"></i> F3 Disc</button>
            <button id="btn-f4" class="action-button"><i class="fas fa-trash-alt text-2xl mb-1"></i> F4 Remove</button>
            <button id="btn-f5" class="action-button"><i class="fas fa-pause-circle text-2xl mb-1"></i> F5 Hold</button>
            <button id="btn-f6" class="action-button"><i class="fas fa-play-circle text-2xl mb-1"></i> F6 Resume</button>
            <button id="btn-esc" class="action-button col-span-2 bg-red-800 border-red-700"><i class="fas fa-times-circle text-2xl mb-1"></i> ESC Cancel</button>
        </div>
    </div>

    <div id="shortcuts-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-lg w-11/12 max-w-3xl max-h-[80vh] overflow-y-auto p-4">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-xl font-bold">POS Keyboard Shortcuts</h2>
                <button id="close-shortcuts" class="px-3 py-1 bg-slate-700 rounded">Close (Esc)</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="font-bold mb-1">Global / Navigation</div>
                    <div>F1 – Help / shortcut guide</div>
                    <div>F2 – Focus product search</div>
                    <div>F4 – New transaction</div>
                    <div>F3 – Open payment screen</div>
                    <div>Esc – Cancel / close modal</div>
                </div>
                <div>
                    <div class="font-bold mb-1">Product Search & Entry</div>
                    <div>Enter – Confirm search / add selected</div>
                    <div>↑/↓ – Navigate results or cart</div>
                    <div>Ctrl+Backspace – Clear search</div>
                    <div>Ctrl+Enter – Force add selected suggestion</div>
                </div>
                <div>
                    <div class="font-bold mb-1">Cart / Item</div>
                    <div>+ / - – Increase / decrease qty</div>
                    <div>Delete – Remove item</div>
                    <div>Ctrl+R – Refresh totals</div>
                </div>
                <div>
                    <div class="font-bold mb-1">Payment</div>
                    <div>Enter – Confirm</div>
                    <div>Esc – Cancel</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('[POS_SCRIPT] Loaded version 2026-01-19T11:30');
            // --- STATE MANAGEMENT ---
            const state = {
                cart: [],
                activeCartItemIndex: 0,
                paymentMethod: 'cash', // 'cash', 'card', 'ewallet'
                uiMode: 'scan', // 'scan', 'payment', 'end'
                cartDiscountPerc: 0,
                heldCart: null,
            };

            // --- DOM ELEMENTS ---
            const productInput = document.getElementById('product-input');
            const cartItemsContainer = document.getElementById('cart-items');
            const currentTimeEl = document.getElementById('current-time');
            const errorMessageEl = document.getElementById('error-message');
            // Cashier elements
            const cashierModal = document.getElementById('cashier-modal');
            const cashierDisplayBtn = document.getElementById('cashier-display');
            const cashierNameEl = document.getElementById('cashier-name');
            const terminalNameEl = document.getElementById('terminal-name');
            const shiftNameEl = document.getElementById('shift-name');
            const cashierCloseBtn = document.getElementById('cashier-close');
            const cashierCancelBtn = document.getElementById('cashier-cancel');
            const cashierApplyBtn = document.getElementById('cashier-apply');
            const employeeIdInput = document.getElementById('employee-id-input');
            // Cash modal elements
            const cashModal = document.getElementById('cash-modal');
            const cashTenderedInput = document.getElementById('cash-tendered');
            const cashPayBtn = document.getElementById('cash-pay');
            const cashCancelBtn = document.getElementById('cash-cancel');
            const cashCloseBtn = document.getElementById('cash-close');
            const cashTotalEl = document.getElementById('cash-total');
            const cashChangeEl = document.getElementById('cash-change');
            const loadingOverlay = document.getElementById('loading-overlay');
            const totalsContainer = document.querySelector('.mt-auto.pt-4');
            const MIN_QUERY_LEN = 3; // minimum characters for name/SKU queries
            const suggestionsEl = document.getElementById('suggestions');
            const shortcutsModal = document.getElementById('shortcuts-modal');
            const btnShortcuts = document.getElementById('btn-shortcuts');
            const btnShortcutsClose = document.getElementById('close-shortcuts');
            const discountModal = document.getElementById('discount-modal');
            const discountInput = document.getElementById('discount-input');
            const discountApply = document.getElementById('discount-apply');
            const discountCancel = document.getElementById('discount-cancel');
            const discountClose = document.getElementById('discount-close');
            const discountTitle = document.getElementById('discount-title');
            const discountLabel = document.getElementById('discount-label');
            let suggestionsData = [];
            let suggestionsIndex = -1; // -1 means none selected
            let discountContext = { type: null };
            // Persisted settings
            let persistedCashierId = localStorage.getItem('pos_cashier_id') || '';
            let persistedCashierName = localStorage.getItem('pos_cashier_name') || '';
            let persistedTerminal = localStorage.getItem('pos_terminal') || 'T1';

            // --- LOADER ---
            function showLoader() { loadingOverlay.classList.remove('hidden'); }
            function hideLoader() { loadingOverlay.classList.add('hidden'); }

            // --- SHORTCUTS MODAL HELPERS ---
            function openShortcuts() {
                if (!shortcutsModal) return;
                shortcutsModal.classList.remove('hidden');
                shortcutsModal.classList.add('flex');
            }

            function closeShortcuts() {
                if (!shortcutsModal) return;
                shortcutsModal.classList.add('hidden');
                shortcutsModal.classList.remove('flex');
            }

            function openDiscountModal(type, currentValue) {
                discountContext.type = type; // 'item' or 'cart'
                if (type === 'item') {
                    discountTitle.innerText = 'Item Discount';
                    discountLabel.innerText = 'Item discount % (0-100)';
                } else {
                    discountTitle.innerText = 'Cart Discount';
                    discountLabel.innerText = 'Cart discount % (0-100)';
                }
                discountInput.value = Number(currentValue ?? 0);
                discountModal.classList.remove('hidden');
                discountModal.classList.add('flex');
                setTimeout(() => discountInput.focus(), 0);
            }

            function closeDiscountModal() {
                discountModal.classList.add('hidden');
                discountModal.classList.remove('flex');
                discountContext.type = null;
            }

            // --- RENDER FUNCTIONS ---
            function highlightElement(el) {
                if (!el) return;
                el.classList.add('ring', 'ring-blue-400');
                setTimeout(() => el.classList.remove('ring', 'ring-blue-400'), 300);
            }
            function computeShiftFromDate(d) {
                const h = d.getHours();
                if (h >= 6 && h < 13) return 'Morning';
                if (h >= 13 && h < 21) return 'Afternoon';
                return 'Night';
            }
            function updateHeaderClockAndShift() {
                const now = new Date();
                currentTimeEl.innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                shiftNameEl.innerText = computeShiftFromDate(now);
            }
            function openCashierModal() {
                cashierModal.classList.remove('hidden');
                cashierModal.classList.add('flex');
                employeeIdInput.value = persistedCashierId || '';
                setTimeout(() => employeeIdInput.select(), 0);
            }
            function closeCashierModal() {
                cashierModal.classList.add('hidden');
                cashierModal.classList.remove('flex');
            }
            async function applyCashier() {
                const id = (employeeIdInput.value || '').trim();
                if (!id) { alert('Please enter Employee ID.'); return; }
                try {
                    const res = await fetch(`{{ route('pos.cashier.validate') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ employee_id: id }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.error || 'Validation failed');
                    }
                    persistedCashierId = data.employee_id || id;
                    persistedCashierName = data.name || id;
                    localStorage.setItem('pos_cashier_id', persistedCashierId);
                    localStorage.setItem('pos_cashier_name', persistedCashierName);
                    cashierNameEl.innerText = persistedCashierName;
                    closeCashierModal();
                } catch (err) {
                    alert(err.message || 'Unable to validate cashier.');
                }
            }
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
                        const qty = Number(item.quantity ?? 0);
                        const unitPriceNum = Number(item.unit_price ?? 0);
                        const itemDiscPerc = Number(item.discount_perc ?? 0);
                        const lineTotalNum = qty * unitPriceNum * (1 - Math.min(Math.max(itemDiscPerc, 0), 100) / 100);
                        const lineTotal = lineTotalNum.toFixed(2);
                        const discountTag = itemDiscPerc ? ' <span class="text-xs text-green-400">(-' + itemDiscPerc + '% )</span>' : '';
                        tr.innerHTML = `
                            <td class="p-2">${item.name}</td>
                            <td class="p-2 text-center">${item.quantity}</td>
                            <td class="p-2 text-right">₱${unitPriceNum.toFixed(2)}</td>
                            <td class="p-2 text-right font-bold">₱${lineTotal}${discountTag}</td>
                        `;
                        cartItemsContainer.appendChild(tr);
                    });
                }
                renderTotals();
            }

            function renderTotals() {
                const subtotalBeforeDisc = state.cart.reduce((sum, item) => {
                    const qty = Number(item.quantity ?? 0);
                    const price = Number(item.unit_price ?? 0);
                    return sum + (qty * price);
                }, 0);

                const itemDiscountTotal = state.cart.reduce((sum, item) => {
                    const qty = Number(item.quantity ?? 0);
                    const price = Number(item.unit_price ?? 0);
                    const discPerc = Math.min(Math.max(Number(item.discount_perc ?? 0), 0), 100);
                    return sum + (qty * price * discPerc / 100);
                }, 0);

                const afterItemDisc = subtotalBeforeDisc - itemDiscountTotal;
                const cartDiscPerc = Math.min(Math.max(Number(state.cartDiscountPerc ?? 0), 0), 100);
                const cartDiscount = afterItemDisc * cartDiscPerc / 100;
                const subtotal = afterItemDisc - cartDiscount;
                const tax = subtotal * 0.12;
                const total = subtotal + tax;

                totalsContainer.querySelector('#subtotal').innerText = `₱${subtotal.toFixed(2)}`;
                totalsContainer.querySelector('#tax').innerText = `₱${tax.toFixed(2)}`;
                totalsContainer.querySelector('#discount').innerText = `-₱${(itemDiscountTotal + cartDiscount).toFixed(2)}`;
                totalsContainer.querySelector('#total').innerText = `₱${total.toFixed(2)}`;
            }

            // --- EVENT HANDLERS & LOGIC ---
            function debounce(func, delay) {
                let timeout;
                const debounced = function(...args) {
                    const context = this;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
                debounced.cancel = function() {
                    clearTimeout(timeout);
                };
                return debounced;
            }

            // --- SUGGESTIONS (PARTIAL SEARCH) ---
            function hideSuggestions() {
                suggestionsEl.classList.add('hidden');
                suggestionsEl.innerHTML = '';
                suggestionsData = [];
                suggestionsIndex = -1;
            }

            function renderSuggestionsList() {
                suggestionsEl.innerHTML = '';
                suggestionsData.forEach((item, idx) => {
                    const row = document.createElement('div');
                    row.className = 'px-3 py-2 hover:bg-slate-700 cursor-pointer flex justify-between items-center ' + (idx === suggestionsIndex ? 'bg-slate-700' : '');
                    const left = document.createElement('div');
                    left.innerHTML = `<div class="font-semibold">${item.name}</div><div class="text-xs text-slate-300">${item.barcode || ''}</div>`;
                    const right = document.createElement('div');
                    right.innerHTML = `<span class="mr-3">₱${Number(item.price ?? 0).toFixed(2)}</span><span class="text-xs ${item.stock > 0 ? 'text-green-400' : 'text-red-400'}">Stock: ${item.stock}</span>`;
                    row.appendChild(left);
                    row.appendChild(right);
                    row.addEventListener('mouseenter', () => { suggestionsIndex = idx; renderSuggestionsList(); });
                    row.addEventListener('click', () => {
                        hideSuggestions();
                        productInput.value = '';
                        lookupProduct(item.barcode || item.name);
                    });
                    suggestionsEl.appendChild(row);
                });
            }

            function showSuggestions(items) {
                if (!items || items.length === 0) {
                    hideSuggestions();
                    return;
                }
                suggestionsData = items;
                suggestionsIndex = -1;
                renderSuggestionsList();
                suggestionsEl.classList.remove('hidden');
            }

            async function fetchSuggestions(keyword) {
                try {
                    const url = `{{ route('pos.lookup') }}?barcode=${encodeURIComponent(keyword)}&mode=list`;
                    console.log('[fetchSuggestions] URL:', url);
                    const res = await fetch(url);
                    const text = await res.text();
                    console.log('[fetchSuggestions] Raw text:', text);
                    if (!res.ok) throw new Error('Suggestion fetch failed');
                    const data = JSON.parse(text);
                    showSuggestions(data.items || []);
                } catch (e) {
                    console.error('[fetchSuggestions] Error:', e);
                    hideSuggestions();
                }
            }

            async function lookupProduct(barcode) {
                console.log(`[lookupProduct] Started for barcode: "${barcode}"`);
                if (!barcode) {
                    errorMessageEl.innerText = '';
                    console.log('[lookupProduct] Halted: Barcode is empty.');
                    return;
                }

                try {
                    const url = `{{ route('pos.lookup') }}?barcode=${barcode}`;
                    console.log(`[lookupProduct] Fetching URL: ${url}`);
                    const response = await fetch(url);
                    console.log('[lookupProduct] Received response from server:', response);

                    const responseText = await response.text();
                    console.log('[lookupProduct] Raw response text:', responseText);

                    if (!response.ok) {
                        let errorMsg = 'Product lookup failed';
                        try {
                            const errorData = JSON.parse(responseText);
                            errorMsg = errorData.error || errorMsg;
                        } catch (e) {
                            // Response was not JSON, likely an HTML error page.
                            console.error('[lookupProduct] Failed to parse error response as JSON.');
                        }
                        throw new Error(errorMsg);
                    }

                    const data = JSON.parse(responseText);
                    console.log('[lookupProduct] Parsed JSON data:', data);

                    const existingItem = state.cart.find(item => item.product_id === data.product_id);
                    if (existingItem) {
                        console.log(`[lookupProduct] Product already in cart. Incrementing quantity for: ${existingItem.name}`);
                        existingItem.quantity++;
                    } else {
                        console.log(`[lookupProduct] Product not in cart. Adding new item: ${data.name}`);
                        state.cart.push({ 
                            product_id: data.product_id, 
                            name: data.name, 
                            quantity: 1, 
                            unit_price: Number(data.price ?? 0) 
                        });
                    }
                    console.log('[lookupProduct] Successfully processed product. Cart state:', state.cart);
                    state.activeCartItemIndex = state.cart.length - 1;
                    renderCart();
                    productInput.value = '';
                    errorMessageEl.innerText = '';
                    hideSuggestions();
                } catch (error) {
                    console.error('[lookupProduct] An error occurred:', error);
                    errorMessageEl.innerText = error.message;
                }
            }

            const debouncedLookup = debounce(lookupProduct, 300);

            function isLikelyBarcode(str) {
                // Treat purely numeric strings of length 8-14 as barcode scans
                return /^\d{8,14}$/.test(str);
            }

            function handleProductInput(e) {
                const barcode = productInput.value.trim();
                console.log(`[handleProductInput] Event: ${e.type}, Key: ${e.key || 'N/A'}, Value: "${barcode}"`);

                // On Enter, cancel any pending lookup and trigger one immediately.
                if (e.key === 'Enter') {
                    console.log('[handleProductInput] Enter key pressed. Triggering immediate lookup.');
                    e.preventDefault();
                    debouncedLookup.cancel();
                    lookupProduct(barcode);
                    return;
                }

                // Ctrl+Backspace clears entire search field
                if (e.ctrlKey && e.key === 'Backspace') {
                    e.preventDefault();
                    productInput.value = '';
                    hideSuggestions();
                    return;
                }

                // Ctrl+Enter: add selected suggestion if present
                if (e.ctrlKey && e.key === 'Enter') {
                    if (!suggestionsEl.classList.contains('hidden') && suggestionsIndex >= 0 && suggestionsIndex < suggestionsData.length) {
                        e.preventDefault();
                        const sel = suggestionsData[suggestionsIndex];
                        hideSuggestions();
                        productInput.value = '';
                        lookupProduct(sel.barcode || sel.name);
                        return;
                    }
                }
                
                // On input: only trigger lookup when likely barcode; otherwise fetch suggestions when length >= MIN_QUERY_LEN
                if (e.type === 'input') {
                    if (isLikelyBarcode(barcode)) {
                        console.log('[handleProductInput] Likely barcode. Triggering debounced lookup.');
                        hideSuggestions();
                        debouncedLookup(barcode);
                    } else if (barcode.length >= MIN_QUERY_LEN) {
                        console.log('[handleProductInput] Manual typing detected. Fetching suggestions.');
                        debouncedLookup.cancel();
                        fetchSuggestions(barcode);
                    } else {
                        console.log('[handleProductInput] Too short. Suppressing suggestions.');
                        hideSuggestions();
                        debouncedLookup.cancel();
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
                openCashModal();
            }

            async function finishSale() {
                if (state.cart.length === 0) return;

                const subtotal = state.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                const tax = subtotal * 0.12;
                const total = subtotal + tax;

                const saleData = {
                    items: state.cart,
                    total_amount: total,
                    tax: tax,
                    payment_method: state.paymentMethod,
                    employee_id: persistedCashierId || '',
                    customer_id: null,
                };

                showLoader();
                try {
                    const response = await fetch('{{ route('pos.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify(saleData),
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.error || 'Sale processing failed');
                    }

                    state.uiMode = 'end';
                    errorMessageEl.innerText = `Sale #${result.sale_id} completed successfully.`;
                    printReceipt();
                    setTimeout(() => {
                        resetForNewSale();
                    }, 2000);
                } catch (error) {
                    errorMessageEl.innerText = error.message;
                    state.uiMode = 'scan';
                    productInput.focus();
                } finally {
                    hideLoader();
                }
            }

            function resetForNewSale() {
                state.cart = [];
                state.activeCartItemIndex = 0;
                state.uiMode = 'scan';
                errorMessageEl.innerText = '';
                renderCart();
                productInput.focus();
                // Initialize header values
                cashierNameEl.innerText = (persistedCashierName || persistedCashierId) || 'Unassigned';
                terminalNameEl.innerText = persistedTerminal || 'T1';
                updateHeaderClockAndShift();
            }

            function handleF2() {
                if (state.cart.length === 0) return;
                const item = state.cart[state.activeCartItemIndex];
                const newQty = prompt(`Enter new quantity for ${item.name}:`, item.quantity);
                if (newQty && !isNaN(newQty) && newQty > 0) {
                    item.quantity = parseInt(newQty, 10);
                    renderCart();
                }
            }

            function handleF4() {
                if (state.cart.length === 0) return;
                state.cart.splice(state.activeCartItemIndex, 1);
                if (state.activeCartItemIndex >= state.cart.length) {
                    state.activeCartItemIndex = Math.max(0, state.cart.length - 1);
                }
                renderCart();
            }

            function handleEsc() {
                if (confirm('Are you sure you want to cancel this sale?')) {
                    resetForNewSale();
                }
            }

            // --- GLOBAL KEYBOARD LISTENER ---
            document.addEventListener('keydown', function (e) {
                if (state.uiMode === 'end') {
                    e.preventDefault();
                    return; // Block input while sale is finalizing
                }

                // Prevent default for all F-keys
                if (e.key.startsWith('F') && (parseInt(e.key.substring(1)) >= 1 && parseInt(e.key.substring(1)) <= 12)) {
                    e.preventDefault();
                }

                // Global shortcuts and modifiers
                if (e.ctrlKey && e.key === 'F1') { e.preventDefault(); resetForNewSale(); return; }
                if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) { e.preventDefault(); printReceipt(); return; }
                if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); openCashModal(); return; }
                if (e.ctrlKey && e.key === 'F6') { e.preventDefault(); resumeCart(); return; }
                if (e.ctrlKey && (e.key === 'l' || e.key === 'L')) { e.preventDefault(); openCashierModal(); return; }
                if (e.ctrlKey && e.key === 'F3') { e.preventDefault(); applyCartDiscount(); return; }

                switch (e.key) {
                    case 'F1': e.preventDefault(); openShortcuts(); return;
                    case 'Escape':
                        if (shortcutsModal && !shortcutsModal.classList.contains('hidden')) { e.preventDefault(); closeShortcuts(); }
                        else { handleEsc(); }
                        return;
                    case 'F2': e.preventDefault(); handleF2(); return;
                    case 'F3': e.preventDefault(); applyItemDiscount(); return;
                    case 'F4': e.preventDefault(); resetForNewSale(); return;
                    case 'F5': e.preventDefault(); holdCart(); return;
                    case 'F6': e.preventDefault(); holdCart(); return;
                    case 'F7': e.preventDefault(); startPayment(); return;
                    case 'F8': e.preventDefault(); startPayment(); return;
                    case 'F9': e.preventDefault(); openCashModal(); return;
                    case 'F10': e.preventDefault(); openCashierModal(); return;
                }

                // Payment method hotkeys removed (cash only)

                // Context-aware shortcuts
                if (state.uiMode === 'scan') {
                    if (document.activeElement !== productInput) {
                        // Cart Navigation
                        switch (e.key) {
                            case 'ArrowUp': e.preventDefault(); navigateCart(-1); break;
                            case 'ArrowDown': e.preventDefault(); navigateCart(1); break;
                            case 'Delete': e.preventDefault(); handleF4(); break;
                            case 'Enter': e.preventDefault(); startPayment(); break;
                            case '+': e.preventDefault(); if (state.cart.length) { state.cart[state.activeCartItemIndex].quantity++; renderCart(); } break;
                            case '-': e.preventDefault(); if (state.cart.length) { const it = state.cart[state.activeCartItemIndex]; it.quantity=Math.max(1,(it.quantity-1)); renderCart(); } break;
                        }
                        if (e.ctrlKey && (e.key === 'r' || e.key === 'R')) { e.preventDefault(); renderCart(); }
                        if (e.ctrlKey && e.key === 'ArrowUp') { e.preventDefault(); if (state.cart.length) { state.activeCartItemIndex = 0; renderCart(); } }
                        if (e.ctrlKey && e.key === 'ArrowDown') { e.preventDefault(); if (state.cart.length) { state.activeCartItemIndex = state.cart.length - 1; renderCart(); } }
                        if (e.ctrlKey && (e.key === 'd' || e.key === 'D')) { e.preventDefault(); handleF4(); }
                        if (e.ctrlKey && (e.key === 'q' || e.key === 'Q')) {
                            e.preventDefault();
                            if (state.cart.length) {
                                const it = state.cart[state.activeCartItemIndex];
                                const newQty = prompt(`Quick quantity for ${it.name}:`, it.quantity);
                                if (newQty && !isNaN(newQty) && Number(newQty) > 0) { it.quantity = parseInt(newQty, 10); renderCart(); }
                            }
                        }
                    } else if (e.key === 'ArrowDown' && state.cart.length > 0 && suggestionsEl.classList.contains('hidden')) {
                        e.preventDefault();
                        productInput.blur();
                        renderCart(); // Re-render to ensure active class is set
                    } else if (!suggestionsEl.classList.contains('hidden')) {
                        // Keyboard navigation for suggestions when typing
                        if (e.key === 'ArrowDown') { e.preventDefault(); suggestionsIndex = Math.min(suggestionsData.length - 1, suggestionsIndex + 1); renderSuggestionsList(); }
                        if (e.key === 'ArrowUp') { e.preventDefault(); suggestionsIndex = Math.max(-1, suggestionsIndex - 1); renderSuggestionsList(); }
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            if (suggestionsIndex >= 0 && suggestionsIndex < suggestionsData.length) {
                                const sel = suggestionsData[suggestionsIndex];
                                hideSuggestions();
                                productInput.value = '';
                                lookupProduct(sel.barcode || sel.name);
                            }
                        }
                        if (e.key === 'Escape') { e.preventDefault(); hideSuggestions(); }
                    }
                } else if (state.uiMode === 'payment') {
                    // Handled via cash modal
                }

                if (shortcutsModal && !shortcutsModal.classList.contains('hidden')) {
                    if (e.key === 'Escape') { e.preventDefault(); closeShortcuts(); }
                }
                if (discountModal && !discountModal.classList.contains('hidden')) {
                    if (e.key === 'Escape') { e.preventDefault(); closeDiscountModal(); }
                }
            });

            // --- INITIALIZATION ---
            function openCashModal() {
                const totalStr = totalsContainer.querySelector('#total').innerText.replace('₱', '').trim();
                cashTotalEl.innerText = `₱${parseFloat(totalStr || '0').toFixed(2)}`;
                cashTenderedInput.value = totalStr;
                updateCashChange();
                updatePayButtonState();
                cashModal.classList.remove('hidden');
                cashModal.classList.add('flex');
                setTimeout(() => cashTenderedInput.select(), 0);
            }
            function closeCashModal() {
                cashModal.classList.add('hidden');
                cashModal.classList.remove('flex');
                if (state.uiMode === 'payment') {
                    // stay in payment mode until sale finishes or canceled
                }
            }
            function updateCashChange() {
                const total = parseFloat(totalsContainer.querySelector('#total').innerText.replace('₱', '')) || 0;
                const tendered = parseFloat(cashTenderedInput.value) || 0;
                const change = Math.max(0, tendered - total);
                cashChangeEl.innerText = `₱${change.toFixed(2)}`;
                updatePayButtonState();
            }
            function updatePayButtonState() {
                const total = parseFloat(totalsContainer.querySelector('#total').innerText.replace('₱', '')) || 0;
                const tendered = parseFloat(cashTenderedInput.value) || 0;
                const canPay = tendered >= total && total > 0;
                cashPayBtn.disabled = !canPay;
                cashPayBtn.classList.toggle('opacity-50', !canPay);
                cashPayBtn.classList.toggle('cursor-not-allowed', !canPay);
            }
            function confirmCashPayment() {
                const total = parseFloat(totalsContainer.querySelector('#total').innerText.replace('₱', '')) || 0;
                const tendered = parseFloat(cashTenderedInput.value) || 0;
                if (tendered < total) { alert('Insufficient cash tendered.'); return; }
                state.paymentMethod = 'cash';
                closeCashModal();
                finishSale();
            }

            // --- STUB HANDLERS FOR SHORTCUT ACTIONS ---
            function holdCart() {
                state.heldCart = JSON.parse(JSON.stringify({ cart: state.cart, active: state.activeCartItemIndex, cartDiscountPerc: state.cartDiscountPerc }));
                errorMessageEl.innerText = 'Cart held.';
            }
            function resumeCart() {
                if (state.heldCart) {
                    state.cart = state.heldCart.cart || [];
                    state.activeCartItemIndex = state.heldCart.active || 0;
                    state.cartDiscountPerc = state.heldCart.cartDiscountPerc || 0;
                    state.heldCart = null;
                    renderCart();
                    errorMessageEl.innerText = 'Cart resumed.';
                } else {
                    errorMessageEl.innerText = 'No held cart.';
                }
            }
            function applyItemDiscount() {
                if (state.cart.length === 0) return;
                const it = state.cart[state.activeCartItemIndex];
                openDiscountModal('item', it.discount_perc ?? 0);
            }
            function applyCartDiscount() {
                openDiscountModal('cart', state.cartDiscountPerc ?? 0);
            }
            function printReceipt() {
                window.print();
            }

            function init() {
                hideLoader();
                setInterval(() => {
                    updateHeaderClockAndShift();
                }, 1000);

                // Event Listeners
                productInput.addEventListener('input', handleProductInput);
                productInput.addEventListener('keydown', handleProductInput);
                // Cash modal events
                cashTenderedInput.addEventListener('input', updateCashChange);
                cashPayBtn.addEventListener('click', confirmCashPayment);
                cashCancelBtn.addEventListener('click', closeCashModal);
                cashCloseBtn.addEventListener('click', closeCashModal);
                btnShortcuts.addEventListener('click', openShortcuts);
                btnShortcutsClose.addEventListener('click', closeShortcuts);
                discountCancel.addEventListener('click', closeDiscountModal);
                discountClose.addEventListener('click', closeDiscountModal);
                discountApply.addEventListener('click', () => {
                    const num = Number(discountInput.value);
                    if (isNaN(num) || num < 0 || num > 100) { alert('Enter a value from 0 to 100.'); return; }
                    if (discountContext.type === 'item') {
                        const it = state.cart[state.activeCartItemIndex];
                        if (it) it.discount_perc = num;
                    } else if (discountContext.type === 'cart') {
                        state.cartDiscountPerc = num;
                    }
                    closeDiscountModal();
                    renderCart();
                });

                // Cashier modal listeners
                if (cashierDisplayBtn) cashierDisplayBtn.addEventListener('click', openCashierModal);
                if (cashierCloseBtn) cashierCloseBtn.addEventListener('click', closeCashierModal);
                if (cashierCancelBtn) cashierCancelBtn.addEventListener('click', closeCashierModal);
                if (cashierApplyBtn) cashierApplyBtn.addEventListener('click', applyCashier);
                const handleCashierKey = (ev) => {
                    if (cashierModal.classList.contains('hidden')) return;
                    if (ev.key === 'Enter') { ev.preventDefault(); applyCashier(); }
                    if (ev.key === 'Escape') { ev.preventDefault(); closeCashierModal(); }
                };
                cashierModal.addEventListener('keydown', handleCashierKey);
                employeeIdInput.addEventListener('keydown', handleCashierKey);

                // Action Buttons
                document.getElementById('btn-f1').addEventListener('click', resetForNewSale);
                document.getElementById('btn-f2').addEventListener('click', handleF2);
                document.getElementById('btn-f3').addEventListener('click', applyItemDiscount);
                document.getElementById('btn-f4').addEventListener('click', handleF4);
                document.getElementById('btn-f5').addEventListener('click', holdCart);
                document.getElementById('btn-f6').addEventListener('click', resumeCart);
                document.getElementById('btn-esc').addEventListener('click', handleEsc);

                // Keyboard within Cash Modal (scoped listeners)
                const handleCashKey = (ev) => {
                    if (cashModal.classList.contains('hidden')) return;
                    if (ev.key === 'Enter') { ev.preventDefault(); confirmCashPayment(); }
                    if (ev.key === 'Escape') { ev.preventDefault(); closeCashModal(); }
                };
                cashModal.addEventListener('keydown', handleCashKey);
                cashTenderedInput.addEventListener('keydown', handleCashKey);

                renderCart();
                productInput.focus();
            }

            init();
        });
    </script>
</body>
</html>
