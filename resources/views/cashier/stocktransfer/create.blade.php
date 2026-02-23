@extends('layouts.app')
@section('title', 'Create Stock Transfer')

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
                <h2 class="mb-1">Create Stock Transfer</h2>
                <p class="text-muted mb-0">Transfer stock to another branch</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cashier.stocktransfer.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Transfers
                </a>
            </div>
        </div>

        <!-- Stock Transfer Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('cashier.stocktransfer.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="from_branch" class="form-label">From Branch</label>
                                <input type="text" class="form-control" id="from_branch" value="{{ $branchId }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="to_branch" class="form-label">To Branch</label>
                                <select class="form-select" id="to_branch" name="to_branch" required>
                                    <option value="">Select Branch</option>
                                    <!-- Branches will be populated here -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Transfer
                                </button>
                                <a href="{{ route('cashier.stocktransfer.index') }}" class="btn btn-outline-secondary">
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
