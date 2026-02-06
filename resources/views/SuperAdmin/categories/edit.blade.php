@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-base">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="m-0">Edit Category</h3>
                    <a href="{{ route('superadmin.categories.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Categories
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="category_name" class="form-control" value="{{ $category->category_name }}" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save me-2"></i>Update Category
                                </button>
                                <a href="{{ route('superadmin.categories.index') }}" class="btn btn-soft">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection