@forelse($products as $product)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input product-select" name="selected_ids[]" value="{{ $product->id }}">
        </td>
        <td>
            <span style="font-weight:700;color:var(--navy);">{{ $product->id }}</span>
        </td>
        <td>
            <div class="image-wrapper" style="width:30px;height:30px;border-radius:6px;border:1px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;margin:0 auto;background:#f8faff;">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->product_name }}" 
                         class="product-image"
                         style="width: 100% !important; height: 100% !important; max-width: none !important; max-height: none !important; object-fit: cover !important;">
                @else
                    <div class="product-image-placeholder" style="width:30px;height:30px;border-radius:6px;background:rgba(13,71,161,0.07);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto;color:var(--muted);font-size:11px;">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
            </div>
        </td>
        <td>
            <div>
                <div class="product-name" style="font-weight:600;">{{ $product->product_name }}</div>
                <small style="color: var(--muted); font-size: 0.75rem;">{{ $product->barcode }}</small>
            </div>
        </td>
        <td>
            {{ $product->brand->name ?? 'N/A' }}
        </td>
        <td>
            {{ $product->category->name ?? 'N/A' }}
        </td>
        <td>
            @php
                $ptype = strtolower((string) ($product->product_type_id ?? ''));
            @endphp
            @if($ptype === 'electronic')
                <span class="sp-badge sp-badge-blue">Electronic</span>
            @elseif($ptype === 'non-electronic' || $ptype === 'nonelectronic' || $ptype === 'non_electronic')
                <span class="sp-badge sp-badge-amber">Non-Electronic</span>
            @else
                <span class="sp-badge sp-badge-blue">{{ $product->product_type_id ?? 'N/A' }}</span>
            @endif
        </td>
        <td>
            @if($product->unitTypes->isNotEmpty())
                @foreach($product->unitTypes as $unitType)
                    <span class="sp-badge sp-badge-blue" style="margin-right:4px;">{{ $unitType->name }}</span>
                @endforeach
            @else
                <span style="color: var(--muted);">N/A</span>
            @endif
        </td>
        <td>
            @if($product->status === 'active')
                <span class="sp-badge sp-badge-green">Active</span>
            @else
                <span class="sp-badge sp-badge-red">Inactive</span>
            @endif
        </td>
        <td>
            <a href="{{ route('superadmin.products.show', $product->id) }}" class="sp-tbl-action sp-tbl-edit" title="View">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center py-5">
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