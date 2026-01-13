@forelse($products as $product)
    <tr>
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
        <td>
            <a href="{{ route('superadmin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            <form action="{{ route('superadmin.products.destroy', $product) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center">No products found.</td>
    </tr>
@endforelse
