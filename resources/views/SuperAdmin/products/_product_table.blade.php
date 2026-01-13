@forelse($products as $product)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input product-select" name="selected_ids[]" value="{{ $product->id }}">
        </td>
        <td>{{ $product->id }}</td>
        <td>{{ $product->product_name }}</td>
        <td>{{ $product->barcode }}</td>
        <td>{{ $product->brand->name ?? 'N/A' }}</td>
        <td>{{ $product->category->name ?? 'N/A' }}</td>
        <td>{{ $product->productType->name ?? 'N/A' }}</td>
        <td>{{ $product->unitType->name ?? 'N/A' }}</td>
        <td>
            <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                {{ ucfirst($product->status) }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center">No products found.</td>
    </tr>
@endforelse
