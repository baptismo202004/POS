@extends('layouts.app')
@section('title', 'Stock Transfer')

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
                <h2 class="mb-1">Stock Transfer</h2>
                <p class="text-muted mb-0">Transfer stock between branches</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cashier.stocktransfer.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Transfer
                </a>
            </div>
        </div>

        <!-- Stock Transfer List -->
        <div class="card">
            <div class="card-body">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                    <h4>Stock Transfer Management</h4>
                    <p>Transfer stock between branches efficiently.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
