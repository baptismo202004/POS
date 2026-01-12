@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Product Type</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.product-types.update', $productType) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="type_name" class="form-control" value="{{ $productType->type_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_electronic" class="form-check-input" id="isElectronic" {{ $productType->is_electronic ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isElectronic">Is Electronic</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Update Product Type</button>
                                <a href="{{ route('superadmin.product-types.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection