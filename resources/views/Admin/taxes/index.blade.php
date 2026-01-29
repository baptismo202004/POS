@extends('layouts.app')
@section('title', 'Tax Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-rounded shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="m-0">Tax Management</h4>
                    <a href="{{ route('superadmin.taxes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Tax
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Rate</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->name }}</td>
                                        <td><code>{{ $tax->code }}</code></td>
                                        <td>{{ $tax->formatted_rate }}</td>
                                        <td>
                                            <span class="badge bg-{{ $tax->type === 'percentage' ? 'info' : 'warning' }}">
                                                {{ ucfirst($tax->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $tax->is_active ? 'success' : 'secondary' }}">
                                                {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $tax->description ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('superadmin.taxes.show', $tax) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.taxes.edit', $tax) }}" class="btn btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.taxes.destroy', $tax) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
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
                                                <p>No taxes found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($taxes->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $taxes->firstItem() }} to {{ $taxes->lastItem() }} of {{ $taxes->total() }} entries
                            </div>
                            {{ $taxes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
