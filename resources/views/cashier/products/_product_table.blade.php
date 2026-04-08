@forelse($products as $product)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input product-select" name="selected_ids[]" value="{{ $product->id }}">
        </td>
        <td>
            <span style="color: #6b84aa; font-family: monospace; font-weight: 700;">#{{ $product->id }}</span>
        </td>
        <td>
            <div class="product-name">{{ $product->product_name }}</div>
        </td>
        <td>
            <div style="width:36px;height:36px;border-radius:6px;border:1px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f8faff;">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="fas fa-image" style="color:var(--muted);font-size:13px;"></i>
                @endif
            </div>
        </td>
        <td>
            <small style="color: #6b84aa; font-family: monospace;">{{ $product->barcode ?? 'N/A' }}</small>
        </td>
        <td style="color: #34495e;">
            {{ $product->brand->name ?? 'N/A' }}
        </td>
        <td style="color: #34495e;">
            {{ $product->category->name ?? 'N/A' }}
        </td>
        <td>
            @if($product->status === 'active')
                <span class="badge-status-active">
                    <span class="status-dot active-dot"></span>
                    Active
                </span>
            @else
                <span class="badge-status-inactive">
                    <span class="status-dot inactive-dot"></span>
                    {{ ucfirst($product->status) }}
                </span>
            @endif
        </td>
        <td>
            <a href="{{ route('cashier.products.show', $product->id) }}" class="tbl-action tbl-view" title="View">
                <i class="fas fa-eye"></i>
            </a>
            @php
                $productStock = \App\Models\StockIn::where('product_id', $product->id)
                    ->selectRaw('COALESCE(SUM(quantity - sold), 0) as total')
                    ->value('total');
            @endphp
            @if((float) $productStock == 0)
                <button type="button"
                    class="tbl-action tbl-delete delete-product-btn"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->product_name }}"
                    title="Delete (no stock)"
                    style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px 6px;">
                    <i class="fas fa-trash"></i>
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-5">
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#95a5a6" stroke-width="1.5">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <div class="fw-semibold" style="font-size: 1.125rem; margin-top: 1rem; color: #34495e;">No products found</div>
                <small style="color: #7f8c8d;">Start by adding your first product</small>
            </div>
        </td>
    </tr>
@endforelse
