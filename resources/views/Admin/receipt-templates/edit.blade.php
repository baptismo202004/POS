@extends('layouts.app')
@section('title', 'Edit Receipt Template')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success messages with SweetAlert
    @if(session()->has('success'))
    Swal.fire({
        icon: 'success',
        title: 'Template Updated!',
        text: '{{ session()->get('success') }}',
        showConfirmButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Perfect!',
        timer: 3000,
        timerProgressBar: true,
        backdrop: `
            rgba(16, 185, 129, 0.1)
            left top
            no-repeat
        `
    });
    @endif
    
    // Show error messages with SweetAlert
    @if(session()->has('error'))
    Swal.fire({
        icon: 'error',
        title: 'Update Failed!',
        text: '{{ session()->get('error') }}',
        showConfirmButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Try Again',
        timer: 5000,
        timerProgressBar: true,
        backdrop: `
            rgba(239, 68, 68, 0.1)
            left top
            no-repeat
        `
    });
    @endif
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-rounded shadow-sm">
                <div class="card-header">
                    <h4 class="m-0">Edit Receipt Template: {{ $receiptTemplate->name }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.receipt-templates.update', $receiptTemplate) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Template Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $receiptTemplate->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Template Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        @foreach($templateTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $receiptTemplate->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="paper_size" class="form-label">Paper Size *</label>
                                    <select class="form-select" id="paper_size" name="paper_size" required>
                                        @foreach($paperSizes as $key => $label)
                                            <option value="{{ $key }}" {{ old('paper_size', $receiptTemplate->paper_size) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('paper_size')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="orientation" class="form-label">Orientation *</label>
                                    <select class="form-select" id="orientation" name="orientation" required>
                                        @foreach($orientations as $key => $label)
                                            <option value="{{ $key }}" {{ old('orientation', $receiptTemplate->orientation) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('orientation')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" 
                                               value="1" {{ old('is_default', $receiptTemplate->is_default) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_default">
                                            Set as Default
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $receiptTemplate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <!-- Template Content Tabs -->
                        <div class="mb-3">
                            <ul class="nav nav-tabs" id="templateTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="header-tab" data-bs-toggle="tab" data-bs-target="#header" type="button" role="tab">
                                        Header Content
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="body-tab" data-bs-toggle="tab" data-bs-target="#body" type="button" role="tab">
                                        Body Content
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab">
                                        Footer Content
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="css-tab" data-bs-toggle="tab" data-bs-target="#css" type="button" role="tab">
                                        CSS Styles
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content border border-top-0 p-3 bg-light" id="templateTabContent">
                                <div class="tab-pane fade show active" id="header" role="tabpanel">
                                    <textarea class="form-control" id="header_content" name="header_content" rows="8" 
                                              placeholder="HTML/Blade template for receipt header">{{ old('header_content', $receiptTemplate->header_content) }}</textarea>
                                    <small class="text-muted">Available variables: {!! '{' !!} $company_name {!! '}' !!}, {!! '{' !!} $company_address {!! '}' !!}, {!! '{' !!} receipt_number {!! '}' !!}, {!! '{' !!} date {!! '}' !!}</small>
                                </div>
                                <div class="tab-pane fade" id="body" role="tabpanel">
                                    <textarea class="form-control" id="body_content" name="body_content" rows="8" 
                                              placeholder="HTML/Blade template for receipt body">{{ old('body_content', $receiptTemplate->body_content) }}</textarea>
                                    <small class="text-muted">Available variables: {!! '{' !!} $items {!! '}' !!}, {!! '{' !!} $subtotal {!! '}' !!}, {!! '{' !!} $tax {!! '}' !!}, {!! '{' !!} $total {!! '}' !!}</small>
                                </div>
                                <div class="tab-pane fade" id="footer" role="tabpanel">
                                    <textarea class="form-control" id="footer_content" name="footer_content" rows="8" 
                                              placeholder="HTML/Blade template for receipt footer">{{ old('footer_content', $receiptTemplate->footer_content) }}</textarea>
                                    <small class="text-muted">Available variables: {!! '{' !!} $payment_method {!! '}' !!}, {!! '{' !!} $cashier {!! '}' !!}, {!! '{' !!} thank_you_message {!! '}' !!}</small>
                                </div>
                                <div class="tab-pane fade" id="css" role="tabpanel">
                                    <textarea class="form-control" id="css_styles" name="css_styles" rows="8" 
                                              placeholder="Custom CSS styles">{{ old('css_styles', $receiptTemplate->css_styles) }}</textarea>
                                    <small class="text-muted">CSS for styling the receipt template</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Template
                            </button>
                            <a href="{{ route('superadmin.receipt-templates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
