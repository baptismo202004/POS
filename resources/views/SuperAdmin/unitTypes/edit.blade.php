@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Unit Type</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.unit-types.update', $unitType) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="unit_name" class="form-control" value="{{ $unitType->unit_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Update Unit Type</button>
                                <a href="{{ route('superadmin.unit-types.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection