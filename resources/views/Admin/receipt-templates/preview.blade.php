@extends('layouts.app')
@section('title', 'Preview Receipt Template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-rounded shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="m-0">BGH IT SOLUTIONS</h4>
                    <div class="btn-group">
                        <a href="{{ route('superadmin.receipt-templates.edit', $receiptTemplate) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('superadmin.receipt-templates.show', $receiptTemplate) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="border p-3 bg-white" style="min-height: 400px;">
                                <style>
                                    {{ $receiptTemplate->css_styles ?? 'body { font-family: Arial, sans-serif; padding: 20px; }' }}
                                </style>
                                
                                @php
                                    // Sample data for preview
                                    $sampleData = [
                                        'company_name' => 'BGH Pharmacy',
                                        'company_address' => '123 Main St, City',
                                        'company_phone' => '123-456-7890',
                                        'receipt_number' => '0001',
                                        'date' => now()->format('M d, Y H:i'),
                                        'cashier' => 'Admin',
                                        'items' => [
                                            (object) ['name' => 'Medicine A', 'quantity' => 2, 'price' => 150.00, 'subtotal' => 300.00],
                                            (object) ['name' => 'Medicine B', 'quantity' => 1, 'price' => 250.00, 'subtotal' => 250.00],
                                        ],
                                        'subtotal' => 550.00,
                                        'tax' => 66.00,
                                        'total' => 616.00,
                                        'payment_method' => 'Cash',
                                        'thank_you_message' => 'Thank you for your purchase!',
                                    ];
                                    
                                    if ($receiptTemplate->type === 'refund') {
                                        $sampleData['refund_number'] = 'R0001';
                                        $sampleData['original_receipt'] = '0001';
                                        $sampleData['refund_reason'] = 'Customer Request';
                                        $sampleData['total_refund'] = 100.00;
                                        $sampleData['items'] = [
                                            (object) ['name' => 'Medicine A', 'quantity' => 1, 'price' => 150.00, 'refund_amount' => 100.00],
                                        ];
                                    } elseif ($receiptTemplate->type === 'purchase') {
                                        $sampleData['po_number'] = 'PO0001';
                                        $sampleData['supplier_name'] = 'Supplier Name';
                                        $sampleData['delivery_date'] = 'TBD';
                                        $sampleData['payment_terms'] = 'Net 30';
                                        $sampleData['prepared_by'] = 'Admin';
                                        $sampleData['items'] = [
                                            (object) ['name' => 'Medicine A', 'quantity' => 10, 'unit_price' => 120.00, 'total' => 1200.00],
                                        ];
                                    }
                                @endphp

                                <div style="max-width: {{ $receiptTemplate->paper_size === '80mm' ? '300px' : '100%' }}; margin: 0 auto;">
                                    {!! $receiptTemplate->header_content !!}
                                    
                                    @if($receiptTemplate->body_content)
                                        @php
                                            // Sample data for preview
                                            $sampleData = [
                                                'company_name' => 'BGH Pharmacy',
                                                'company_address' => '123 Main St, City',
                                                'company_phone' => '123-456-7890',
                                                'receipt_number' => '0001',
                                                'date' => now()->format('M d, Y H:i'),
                                                'cashier' => 'Admin',
                                                'items' => [
                                                    (object) ['name' => 'Medicine A', 'quantity' => 2, 'price' => 150.00, 'subtotal' => 300.00],
                                                    (object) ['name' => 'Medicine B', 'quantity' => 1, 'price' => 250.00, 'subtotal' => 250.00],
                                                ],
                                                'subtotal' => 550.00,
                                                'tax' => 66.00,
                                                'total' => 616.00,
                                                'payment_method' => 'Cash',
                                                'thank_you_message' => 'Thank you for your purchase!',
                                            ];
                                            
                                            if ($receiptTemplate->type === 'refund') {
                                                $sampleData['refund_number'] = 'R0001';
                                                $sampleData['original_receipt'] = '0001';
                                                $sampleData['refund_reason'] = 'Customer Request';
                                                $sampleData['total_refund'] = 100.00;
                                                $sampleData['items'] = [
                                                    (object) ['name' => 'Medicine A', 'quantity' => 1, 'price' => 150.00, 'refund_amount' => 100.00],
                                                ];
                                            } elseif ($receiptTemplate->type === 'purchase') {
                                                $sampleData['po_number'] = 'PO0001';
                                                $sampleData['supplier_name'] = 'Supplier Name';
                                                $sampleData['delivery_date'] = 'TBD';
                                                $sampleData['payment_terms'] = 'Net 30';
                                                $sampleData['prepared_by'] = 'Admin';
                                                $sampleData['items'] = [
                                                    (object) ['name' => 'Medicine A', 'quantity' => 10, 'unit_price' => 120.00, 'total' => 1200.00],
                                                ];
                                            }
                                            
                                            // Render the body content using Blade::render()
                                            echo Blade::render($receiptTemplate->body_content, $sampleData);
                                        @endphp
                                    @endif
                                    
                                    {!! $receiptTemplate->footer_content !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0">Template Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $receiptTemplate->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>{{ ucfirst($receiptTemplate->type) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paper:</strong></td>
                                            <td>{{ $receiptTemplate->paper_size }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Orientation:</strong></td>
                                            <td>{{ ucfirst($receiptTemplate->orientation) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $receiptTemplate->is_active ? 'success' : 'secondary' }}">
                                                    {{ $receiptTemplate->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($receiptTemplate->is_default)
                                            <tr>
                                                <td><strong>Default:</strong></td>
                                                <td><span class="badge bg-warning">Default</span></td>
                                            </tr>
                                        @endif
                                    </table>
                                    
                                    <div class="mt-3">
                                        <h6>Sample Data Used:</h6>
                                        <small class="text-muted">
                                            This preview uses sample data to demonstrate how the template will look when printed.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
