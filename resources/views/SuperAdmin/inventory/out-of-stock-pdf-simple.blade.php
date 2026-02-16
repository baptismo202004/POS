<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Out of Stock Report</title>
    <style>
        body { font-family: Arial; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Out of Stock Report</h1>
    <p>Generated: {{ $generatedDate }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Stock</th>
                <th>Branch</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->current_stock }}</td>
                    <td>{{ $product->branch_name ?? 'All Branches' }}</td>
                    <td>{{ $product->current_stock <= 5 ? 'Critical' : 'Low Stock' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No products found that need purchasing.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <p><small>Total products needing purchase: {{ $products->count() }}</small></p>
</body>
</html>
