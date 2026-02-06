@extends('layouts.app')
@section('title', 'Stock In')

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
    <div class="container-fluid stockin-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2 class="m-0">Stock In</h2>
                                <a href="{{ route('cashier.stockin.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add Stock In</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><a href="{{ route('cashier.stockin.index', ['sort' => 'product', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Product</a></th>
                                            <th>Purchase Ref</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stockIns as $stock)
                                            <tr>
                                                <td>{{ $stock->product->product_name ?? 'N/A' }}</td>
                                                <td>{{ $stock->purchase->reference_number ?? 'N/A' }}</td>
                                                <td>{{ $stock->quantity }}</td>
                                                <td>{{ number_format($stock->price, 2) }}</td>
                                                <td>{{ optional($stock->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No stock records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $stockIns->links() }}
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

            const page = document.querySelector('.stockin-page');
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
            const inventoryCard = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Inventory');
            const stockInCard = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Stock In');
            if (inventoryCard && stockInCard) {
                stockInCard.style.marginLeft = '18px';
                stockInCard.style.paddingLeft = '20px';
                stockInCard.style.opacity = '1';
                inventoryCard.insertAdjacentElement('afterend', stockInCard);
            }

            const productsCard = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Products');
            const categoryCard = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Product Category');
            if (productsCard && categoryCard) {
                categoryCard.style.marginLeft = '18px';
                categoryCard.style.paddingLeft = '20px';
                productsCard.insertAdjacentElement('afterend', categoryCard);
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

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Stock-in Error',
            html: '{!! session('error') !!}',
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif
});
</script>
@endpush
