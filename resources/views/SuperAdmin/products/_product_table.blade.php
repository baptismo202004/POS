@forelse($products as $product)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input product-select" name="selected_ids[]" value="{{ $product->id }}">
        </td>
        <td>
            <span style="color: #7f8c8d; font-family: monospace; font-weight: 600;">#{{ $product->id }}</span>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}" class="product-image">
                @else
                    <div class="product-image-placeholder">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#95a5a6" stroke-width="2">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <div class="product-name">{{ $product->product_name }}</div>
                    <small style="color: #7f8c8d; font-size: 0.75rem;">{{ $product->barcode }}</small>
                </div>
            </div>
        </td>
        <td>
            <span class="badge-barcode">{{ $product->barcode }}</span>
        </td>
        <td style="color: #34495e;">
            {{ $product->brand->name ?? 'N/A' }}
        </td>
        <td style="color: #34495e;">
            {{ $product->category->name ?? 'N/A' }}
        </td>
        <td style="color: #34495e;">
            {{ $product->productType->name ?? 'N/A' }}
        </td>
        <td>
            @if($product->unitTypes->isNotEmpty())
                @foreach($product->unitTypes as $unitType)
                    <span class="badge-unit me-1">{{ $unitType->name }}</span>
                @endforeach
            @else
                <span style="color: #95a5a6;">N/A</span>
            @endif
        </td>
        <td>
            <span class="{{ $product->status === 'active' ? 'badge-status-active' : 'badge-status-inactive' }}">
                {{ ucfirst($product->status) }}
            </span>
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