@extends('layouts.app')

@section('content')
    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">Credit Details</h2>
                                <a href="{{ route('superadmin.admin.credits.index') }}" class="btn btn-outline-primary">Back to Credits</a>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">Credit Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Credit ID:</strong></td>
                                            <td>#{{ $credit->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer:</strong></td>
                                            <td>{{ $credit->customer->name ?? $credit->customer_name ?? 'Walk-in Customer' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Credit Amount:</strong></td>
                                            <td>₱{{ number_format($credit->credit_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paid Amount:</strong></td>
                                            <td>₱{{ number_format($credit->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining Balance:</strong></td>
                                            <td class="fw-bold text-{{ $credit->remaining_balance > 0 ? 'danger' : 'success' }}">
                                                ₱{{ number_format($credit->remaining_balance, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $credit->date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $credit->status == 'active' ? 'primary' : ($credit->status == 'paid' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($credit->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($credit->notes)
                                        <tr>
                                            <td><strong>Notes:</strong></td>
                                            <td>{{ $credit->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">Payment History</h5>
                                    @if($credit->payments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Method</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($credit->payments as $payment)
                                                        <tr>
                                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No payments recorded yet.</p>
                                    @endif
                                </div>
                            </div>

                            @if($credit->remaining_balance > 0)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <div>
                                                <strong>Outstanding Balance:</strong> ₱{{ number_format($credit->remaining_balance, 2) }}
                                                <br>
                                                <small>Return to the credits list to make a payment.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-success d-flex align-items-center">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <div>
                                                <strong>Fully Paid:</strong> This credit has been completely settled.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
