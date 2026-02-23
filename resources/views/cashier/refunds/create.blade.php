@extends('layouts.app')
@section('title', 'Create Refund')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Use CashierSidebar */
        .main-content {
            margin-left: 280px !important;
        }
    </style>
@endpush

@section('content')
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Create Refund</h2>
                <p class="text-muted mb-0">Process a customer refund or return</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cashier.refunds.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Refunds
                </a>
            </div>
        </div>

        <!-- Refund Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('cashier.refunds.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_id" class="form-label">Sale ID</label>
                                <input type="text" class="form-control" id="sale_id" name="sale_id" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="refund_type" class="form-label">Refund Type</label>
                                <select class="form-select" id="refund_type" name="refund_type" required>
                                    <option value="">Select Type</option>
                                    <option value="full">Full Refund</option>
                                    <option value="partial">Partial Refund</option>
                                    <option value="return">Return</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Process Refund
                                </button>
                                <a href="{{ route('cashier.refunds.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
