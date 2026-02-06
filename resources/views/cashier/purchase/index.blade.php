@extends('layouts.app')
@section('title', 'Purchases')

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
                                <h2 class="m-0">Purchases</h2>
                                <a href="{{ route('cashier.purchases.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add New Purchase</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Reference Number</th>
                                            <th>Purchase Date</th>
                                            <th>Payment Status</th>
                                            <th>Items</th>
                                            <th>Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->reference_number ?: 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('cashier.purchases.show', $purchase) }}">
                                                        {{ optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ ucfirst($purchase->payment_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $purchase->items->count() }} item(s)</td>
                                                <td><strong>₱{{ number_format($purchase->total_cost, 2) }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No purchases found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $purchases->links() }}
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

    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif
});
</script>
@endpush
