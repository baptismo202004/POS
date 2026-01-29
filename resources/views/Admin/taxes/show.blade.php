@extends('layouts.app')
@section('title', 'Tax Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-rounded shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="m-0">Tax Details: {{ $tax->name }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('superadmin.taxes.edit', $tax) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('superadmin.taxes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tax Name:</strong></td>
                                    <td>{{ $tax->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tax Code:</strong></td>
                                    <td><code>{{ $tax->code }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Tax Rate:</strong></td>
                                    <td>{{ $tax->formatted_rate }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tax Type:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $tax->type === 'percentage' ? 'info' : 'warning' }}">
                                            {{ ucfirst($tax->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $tax->is_active ? 'success' : 'secondary' }}">
                                            {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $tax->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($tax->description)
                        <div class="mt-4">
                            <h5>Description</h5>
                            <p>{{ $tax->description }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>Tax Calculation Example</h5>
                        <div class="alert alert-info">
                            @if($tax->type === 'percentage')
                                For an amount of ₱1,000.00, the tax would be: ₱{{ number_format(1000 * ($tax->rate / 100), 2) }}
                            @else
                                Fixed tax amount: ₱{{ number_format($tax->rate, 2) }} per transaction
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
