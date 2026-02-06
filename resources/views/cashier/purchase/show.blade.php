@extends('layouts.app')
@section('title', 'Purchase Details')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 0 !important;
        }
    </style>
@endpush

@section('content')
<div class="d-flex min-vh-100">
    <div class="container-fluid purchase-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2 class="m-0">Purchase Details</h2>
                                <a href="{{ route('cashier.purchases.index') }}" class="btn btn-outline-primary">Back to Purchases</a>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <p class="text-muted mb-1">Reference Number</p>
                                    <p class="fw-semibold">{{ $purchase->reference_number ?: 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted mb-1">Purchase Date</p>
                                    <p class="fw-semibold">{{ optional($purchase->purchase_date)->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted mb-1">Payment Status</p>
                                    <p><span class="badge fs-6 {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($purchase->payment_status) }}</span></p>
                                </div>
                            </div>

                            <h5 class="mt-4">Purchased Items</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Type</th>
                                            <th>Unit Cost</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchase->items as $item)
                                            <tr>
                                                <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->unitType->unit_name ?? 'N/A' }}</td>
                                                <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                                                <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">No items found for this purchase.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebarHTML = sessionStorage.getItem('cashierSidebarHTML') || localStorage.getItem('cashierSidebarHTML');
    if (sidebarHTML) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = sidebarHTML;
        const appendedSidebar = wrapper.firstElementChild;
        if (appendedSidebar) {
            document.body.appendChild(appendedSidebar);
        }

        const sidebar = appendedSidebar || document.querySelector('body > div[style*="position: fixed"][style*="left: 0"]');
        if (sidebar) {
            sidebar.style.transform = 'translateX(0)';
            sidebar.style.zIndex = '2000';
            const navItems = sidebar.querySelectorAll('.nav-card');
            navItems.forEach(item => {
                item.style.transform = 'translateX(0)';
                item.style.opacity = '1';
            });

            const logoImg = sidebar.querySelector('img[src*="BGH LOGO.png"]');
            if (logoImg) {
                logoImg.addEventListener('click', () => {
                    window.location.href = '{{ route('cashier.dashboard') }}';
                });
            }

            const expandedWidth = 220;
            sidebar.style.width = expandedWidth + 'px';
            sidebar.style.padding = '20px 10px';
            sidebar.style.overflowX = 'hidden';

            const page = document.querySelector('.purchase-page');
            if (page) {
                page.style.transition = 'margin-left 0.2s ease';
                page.style.marginLeft = expandedWidth + 'px';
            }

            navItems.forEach(item => {
                item.style.justifyContent = 'flex-start';
                item.style.gap = '16px';
                item.style.paddingLeft = '20px';
                item.style.paddingRight = '20px';

                const icon = item.querySelector('.nav-icon');
                if (icon) icon.style.margin = '0';

                const content = item.querySelector('.nav-content');
                if (content) {
                    content.style.opacity = '1';
                    content.style.pointerEvents = 'auto';
                }
            });

            const itemsArr = Array.from(navItems);
            const invItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Inventory');
            const stockInItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Stock In');
            if (invItem && stockInItem) {
                stockInItem.style.marginLeft = '18px';
                stockInItem.style.paddingLeft = '20px';
                invItem.insertAdjacentElement('afterend', stockInItem);
            }

            const productsItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Products');
            const categoryItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Product Category');
            if (productsItem && categoryItem) {
                categoryItem.style.marginLeft = '18px';
                categoryItem.style.paddingLeft = '20px';
                productsItem.insertAdjacentElement('afterend', categoryItem);
            }
        }
    } else {
        console.warn('Cashier sidebar not found in sessionStorage/localStorage (cashierSidebarHTML).');
    }
});
</script>
@endpush
