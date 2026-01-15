@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-gray-800">Expense Details</h1>
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Expense Details</h5>
                            @if($expense->purchase_id)
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-link me-1"></i>
                                    This expense is linked to Purchase #{{ $expense->purchase_id }}.
                                </div>
                            @endif
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Category:</strong></div>
                                <div class="col-sm-8"><span class="badge bg-secondary">{{ $expense->category->name }}</span></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Date:</strong></div>
                                <div class="col-sm-8">{{ $expense->expense_date->format('F j, Y') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Amount:</strong></div>
                                <div class="col-sm-8">₱{{ number_format($expense->amount, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Payment Method:</strong></div>
                                <div class="col-sm-8">{{ $expense->payment_method }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Description:</strong></div>
                                <div class="col-sm-8">{{ $expense->description ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Supplier:</strong></div>
                                <div class="col-sm-8">{{ $expense->supplier->name ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Reference:</strong></div>
                                <div class="col-sm-8">{{ $expense->reference_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Receipt</h5>
                            @if($expense->receipt_path)
                                @php
                                    $extension = pathinfo($expense->receipt_path, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $expense->receipt_path) }}" alt="Receipt Preview" class="img-fluid rounded border" style="max-height: 400px;">
                                    </a>
                                @elseif($extension == 'pdf')
                                    <div class="alert alert-secondary">
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="btn btn-primary"><i class="fas fa-file-pdf me-2"></i>View PDF Receipt</a>
                                    </div>
                                @else
                                    <div class="alert alert-secondary">
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank">Download Receipt</a>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light text-center" role="alert">
                                    No receipt attached.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
