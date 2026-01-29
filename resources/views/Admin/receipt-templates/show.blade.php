@extends('layouts.app')
@section('title', 'Receipt Template Details')

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
        title: 'Success!',
        text: '{{ session()->get('success') }}',
        showConfirmButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Got it!',
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
        title: 'Error!',
        text: '{{ session()->get('error') }}',
        showConfirmButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'OK',
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="m-0">Template Details: {{ $receiptTemplate->name }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('superadmin.receipt-templates.preview', $receiptTemplate) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="{{ route('superadmin.receipt-templates.edit', $receiptTemplate) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('superadmin.receipt-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Template Name:</strong></td>
                                    <td>{{ $receiptTemplate->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($receiptTemplate->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Paper Size:</strong></td>
                                    <td>{{ $receiptTemplate->paper_size }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Orientation:</strong></td>
                                    <td>{{ ucfirst($receiptTemplate->orientation) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $receiptTemplate->is_active ? 'success' : 'secondary' }}">
                                            {{ $receiptTemplate->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Default:</strong></td>
                                    <td>
                                        @if($receiptTemplate->is_default)
                                            <span class="badge bg-warning">Default</span>
                                        @else
                                            <form action="{{ route('superadmin.receipt-templates.set-default', $receiptTemplate) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-star"></i> Set as Default
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $receiptTemplate->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $receiptTemplate->updated_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($receiptTemplate->settings)
                        <div class="mt-4">
                            <h5>Template Settings</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <pre>{{ json_encode($receiptTemplate->settings, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>Template Content Preview</h5>
                        <div class="row">
                            @if($receiptTemplate->header_content)
                                <div class="col-md-4">
                                    <h6>Header Content</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <code>{{ Str::limit($receiptTemplate->header_content, 200) }}</code>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($receiptTemplate->body_content)
                                <div class="col-md-4">
                                    <h6>Body Content</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <code>{{ Str::limit($receiptTemplate->body_content, 200) }}</code>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($receiptTemplate->footer_content)
                                <div class="col-md-4">
                                    <h6>Footer Content</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <code>{{ Str::limit($receiptTemplate->footer_content, 200) }}</code>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($receiptTemplate->css_styles)
                        <div class="mt-4">
                            <h5>CSS Styles</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <pre><code>{{ $receiptTemplate->css_styles }}</code></pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
