@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white mb-4">
        <h2 class="m-0">Create Stock Transfer</h2>
        <form action="{{ route('superadmin.stocktransfer.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="from_branch_id" class="form-label">From Branch</label>
                        <select name="from_branch_id" id="from_branch_id" class="form-control" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="to_branch_id" class="form-label">To Branch</label>
                        <select name="to_branch_id" id="to_branch_id" class="form-control" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="p-4 card-rounded shadow-sm bg-white">
        <h2 class="m-0">Transfer Requests</h2>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>From Branch</th>
                        <th>To Branch</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->product->product_name }}</td>
                            <td>{{ $transfer->fromBranch->name }}</td>
                            <td>{{ $transfer->toBranch->name }}</td>
                            <td>{{ $transfer->quantity }}</td>
                            <td><span class="badge bg-{{ $transfer->status == 'pending' ? 'warning' : ($transfer->status == 'approved' ? 'success' : 'danger') }}">{{ ucfirst($transfer->status) }}</span></td>
                            <td>{{ $transfer->notes }}</td>
                            <td>
                                @if($transfer->status == 'pending')
                                    <form action="{{ route('superadmin.stocktransfer.update', $transfer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('superadmin.stocktransfer.update', $transfer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No transfer requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $transfers->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#product_id').select2({
            placeholder: '-- Select Product --'
        });
        $('#from_branch_id').select2({
            placeholder: '-- Select Branch --'
        });
        $('#to_branch_id').select2({
            placeholder: '-- Select Branch --'
        });
    });
</script>
@endpush
