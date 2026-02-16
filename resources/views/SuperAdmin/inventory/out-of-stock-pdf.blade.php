<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Out of Stock Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .filters {
            margin-bottom: 10px;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .critical {
            background-color: #ffebee;
        }
        .low-stock {
            background-color: #fff3cd;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Out of Stock Report</h1>
        @if($branchId)
            <div class="filters">
                <strong>Branch:</strong> {{ $branches->where('id', $branchId)->first()->branch_name ?? 'Unknown' }}
            </div>
        @endif
        @if($search)
            <div class="filters">
                <strong>Search:</strong> "{{ $search }}"
            </div>
        @endif
        <div class="filters">
            <strong>Generated:</strong> {{ $generatedDate }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Branch</th>
                <th>Current Stock</th>
                <th>Total Sold</th>
                <th>Total Revenue</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr class="{{ $product->current_stock <= 5 ? 'critical' : 'low-stock' }}">
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->brand_name ?? 'N/A' }}</td>
                    <td>{{ $product->category_name ?? 'N/A' }}</td>
                    <td>{{ $product->branch_name ?? 'N/A' }}</td>
                    <td>{{ $product->current_stock }}</td>
                    <td>{{ $product->total_sold ?? 0 }}</td>
                    <td>{{ number_format($product->total_revenue ?? 0, 2) }}</td>
                    <td>
                        @if($product->current_stock == 0)
                            Out of Stock
                        @elseif($product->current_stock <= 5)
                            Critical
                        @else
                            Low Stock
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No out-of-stock products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="text-center" style="margin-top: 30px;">
        <small>Report generated on {{ $generatedDate }}</small>
    </div>
</body>
</html>
