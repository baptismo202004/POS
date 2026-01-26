@extends('layouts.app')

@section('content')
<style>
    /* Force standard select to be visible */
    select.form-control {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
        color: #212529 !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }
</style>
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
                        </select>
                        {{-- Debug: {{ $branches->count() }} branches found --}}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="to_branch_id" class="form-label">To Branch</label>
                        <select name="to_branch_id" id="to_branch_id" class="form-control" required>
                            <option value="">-- Select Branch --</option>
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
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Debug: Log branches data to console
    console.log('Branches data:', @json($branches));
    console.log('Branches count:', {{ $branches->count() }});

    $(document).ready(function() {
        // Prevent Select2 from being applied to branch dropdowns
        $('#from_branch_id, #to_branch_id').removeClass('select2-hidden-accessible');
        
        // Destroy any existing Select2 instances on branch dropdowns
        if ($('#from_branch_id').data('select2')) {
            $('#from_branch_id').select2('destroy');
        }
        if ($('#to_branch_id').data('select2')) {
            $('#to_branch_id').select2('destroy');
        }
        
        // Remove Select2 containers
        $('#from_branch_id').siblings('.select2').remove();
        $('#to_branch_id').siblings('.select2').remove();
        
        // Make sure the selects are visible
        $('#from_branch_id, #to_branch_id').show();
        
        // Populate branch dropdowns
        try {
            const branches = {!! $branchesJson !!};
            console.log('Parsed branches:', branches);
            
            const $fromBranchSelect = $('#from_branch_id');
            const $toBranchSelect = $('#to_branch_id');

            if (Array.isArray(branches) && branches.length > 0) {
                branches.forEach(branch => {
                    const option = `<option value="${branch.id}">${branch.branch_name}</option>`;
                    $fromBranchSelect.append(option);
                    $toBranchSelect.append(option);
                });
                console.log('Branches added to dropdowns');
            } else {
                console.error('Branches data is not an array or is empty');
            }
        } catch (error) {
            console.error('Error parsing branches JSON:', error);
        }

        // Only apply Select2 to product dropdown
        $('#product_id').select2({
            placeholder: '-- Select Product --'
        });

        // Handle form submission with AJAX
        $('form[action="{{ route('superadmin.stocktransfer.store') }}"]').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            
            // Disable submit button to prevent double submission
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Great!',
                            confirmButtonColor: '#2563eb',
                            background: '#fff',
                            backdrop: `rgba(37, 99, 235, 0.1)`,
                            showClass: {
                                popup: 'animate__animated animate__zoomIn',
                                icon: 'animate__animated animate__heartBeat'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary px-4 py-2',
                                title: 'fw-bold text-primary',
                                content: 'text-muted'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reset form
                                $form[0].reset();
                                // Reset Select2
                                $('#product_id').val(null).trigger('change');
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: '#dc3545',
                            background: '#fff',
                            backdrop: `rgba(220, 53, 69, 0.1)`,
                            showClass: {
                                popup: 'animate__animated animate__tada',
                                icon: 'animate__animated animate__swing'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutDown'
                            },
                            customClass: {
                                confirmButton: 'btn btn-danger px-4 py-2',
                                title: 'fw-bold text-danger',
                                content: 'text-muted'
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while processing your request.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545',
                        background: '#fff',
                        backdrop: `rgba(220, 53, 69, 0.1)`,
                        showClass: {
                            popup: 'animate__animated animate__bounceIn',
                            icon: 'animate__animated animate__flash'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__zoomOut'
                        },
                        customClass: {
                            confirmButton: 'btn btn-danger px-4 py-2',
                            title: 'fw-bold text-danger',
                            content: 'text-muted'
                        }
                    });
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html('Submit');
                }
            });
        });
    });
</script>
@endpush
