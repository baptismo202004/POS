@extends('layouts.app')
@section('title', 'Receipt Templates')

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
        confirmButtonText: 'Great!',
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
                    <h4 class="m-0">Receipt Templates</h4>
                    <a href="{{ route('superadmin.receipt-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Template
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Paper Size</th>
                                    <th>Orientation</th>
                                    <th>Status</th>
                                    <th>Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr>
                                        <td>{{ $template->name }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($template->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $template->paper_size }}</td>
                                        <td>{{ ucfirst($template->orientation) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($template->is_default)
                                                <span class="badge bg-warning">Default</span>
                                            @else
                                                <form action="{{ route('superadmin.receipt-templates.set-default', $template) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Set as Default">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('superadmin.receipt-templates.preview', $template) }}" class="btn btn-outline-info" title="Preview">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.receipt-templates.show', $template) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                                <a href="{{ route('superadmin.receipt-templates.edit', $template) }}" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.receipt-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this template?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-receipt fa-2x mb-2"></i>
                                                <p>No receipt templates found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($templates->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $templates->firstItem() }} to {{ $templates->lastItem() }} of {{ $templates->total() }} entries
                            </div>
                            {{ $templates->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
