@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Brand</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.brands.update', $brand) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="brand_name" class="form-control" value="{{ $brand->brand_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Update Brand</button>
                                <a href="{{ route('superadmin.brands.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection