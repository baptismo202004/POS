@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col">
            <h1 class="h4 mb-0">POS Admin</h1>
            <small class="text-muted">Inventory overview, product/barcode search, and order summary</small>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary">Back to Sales</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="input-group">
                        <input id="search-input" type="text" class="form-control" placeholder="Search by product name / barcode / model number" />
                        <button id="search-btn" class="btn btn-primary">Search</button>
                    </div>
                    <small class="text-muted">Press Enter to search. Use barcode scanner as well.</small>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>On-stock Items</span>
                    <span class="text-muted" id="results-count">0 results</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" id="results-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Barcode</th>
                                    <th class="text-end">Total Stock</th>
                                    <th>Branches Offered</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-muted">
                                    <td colspan="6" class="text-center py-4">Start searching to see results…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Order Summary</div>
                <div class="card-body">
                    <div id="order-items" class="mb-3">
                        <div class="text-muted">No items yet</div>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <strong>Total</strong>
                        <strong id="order-total">₱0.00</strong>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button id="clear-order" class="btn btn-outline-secondary w-50">Clear</button>
                        <button id="checkout" class="btn btn-success w-50" disabled>Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const input = document.getElementById('search-input');
    const btn = document.getElementById('search-btn');
    const tableBody = document.querySelector('#results-table tbody');
    const resultsCount = document.getElementById('results-count');
    const orderItems = document.getElementById('order-items');
    const orderTotal = document.getElementById('order-total');
    const checkoutBtn = document.getElementById('checkout');
    const clearBtn = document.getElementById('clear-order');

    const order = [];

    function formatCurrency(n){
        const num = Number(n||0);
        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(num);
    }

    function renderOrder(){
        if(order.length === 0){
            orderItems.innerHTML = '<div class="text-muted">No items yet</div>';
            orderTotal.textContent = formatCurrency(0);
            checkoutBtn.disabled = true;
            return;
        }
        let html = '';
        let total = 0;
        order.forEach((it, idx) => {
            const sub = it.qty * it.price;
            total += sub;
            html += `
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <div>
                    <div class="fw-semibold">${it.name}</div>
                    <small class="text-muted">Qty: ${it.qty} × ${formatCurrency(it.price)}</small>
                </div>
                <div class="text-end">
                    <div>${formatCurrency(sub)}</div>
                    <button class="btn btn-sm btn-link text-danger p-0" data-remove="${idx}">Remove</button>
                </div>
            </div>`;
        });
        orderItems.innerHTML = html;
        orderTotal.textContent = formatCurrency(total);
        checkoutBtn.disabled = false;
        orderItems.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', () => {
                const i = Number(btn.getAttribute('data-remove'));
                order.splice(i,1);
                renderOrder();
            });
        });
    }

    function addToOrder(item){
        const existing = order.find(o => o.product_id === item.product_id);
        if(existing){ existing.qty += 1; }
        else { order.push({ product_id:item.product_id, name:item.name, price:item.price, qty:1 }); }
        renderOrder();
    }

    async function search(mode='list'){
        const keyword = input.value.trim();
        if(!keyword){ return; }
        const url = new URL("{{ route('admin.pos.admin.lookup') }}", window.location.origin);
        url.searchParams.set('mode', mode);
        const form = new FormData();
        form.set('barcode', keyword);
        const res = await fetch(url.toString(), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: form
        });
        const data = await res.json();

        const items = data.items || (data.error ? [] : [data]);
        resultsCount.textContent = `${items.length} results`;
        if(items.length === 0){
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No results</td></tr>';
            return;
        }

        tableBody.innerHTML = items.map(it => {
            const branches = (it.branches || []).map(b => `${b.branch_name||'Branch #'+b.branch_id} <span class=\"badge bg-secondary ms-1\">${b.stock}</span>`).join('<br>');
            return `
            <tr>
                <td>${it.name}</td>
                <td>${it.barcode||''}</td>
                <td class="text-end">${it.total_stock ?? it.stock ?? 0}</td>
                <td>${branches||'<span class=\'text-muted\'>—</span>'}</td>
                <td class="text-end">${formatCurrency(it.price||0)}</td>
                <td class="text-end"><button class="btn btn-sm btn-primary" data-add='${JSON.stringify({product_id:it.product_id,name:it.name,price:it.price||0}).replace(/"/g, '&quot;')}'>Add</button></td>
            </tr>`;
        }).join('');

        tableBody.querySelectorAll('[data-add]').forEach(btn => {
            btn.addEventListener('click', () => {
                const payload = JSON.parse(btn.getAttribute('data-add').replace(/&quot;/g,'"'));
                addToOrder(payload);
            });
        });
    }

    input.addEventListener('keydown', (e) => {
        if(e.key === 'Enter'){
            e.preventDefault();
            search('list');
        }
    });
    btn.addEventListener('click', () => search('list'));
    clearBtn.addEventListener('click', () => { order.length = 0; renderOrder(); });
})();
</script>
@endpush
@endsection
