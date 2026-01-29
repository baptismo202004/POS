@extends('layouts.app')
@section('title', 'Create Tax')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-rounded shadow-sm">
                <div class="card-header">
                    <h4 class="m-0">Create New Tax</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.taxes.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tax Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Tax Code *</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ old('code') }}" required placeholder="e.g., VAT, GST">
                                    @error('code')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rate" class="form-label">Tax Rate (%) *</label>
                                    <input type="number" class="form-control" id="rate" name="rate" 
                                           value="{{ old('rate') }}" required step="0.01" min="0" max="100" placeholder="e.g., 12.5">
                                    @error('rate')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tax Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                            Percentage
                                        </option>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>
                                            Fixed Amount
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Optional description for this tax">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Tax
                            </button>
                            <a href="{{ route('superadmin.taxes.index') }}" class="btn btn-secondary">
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
