@php
    /**
     * Variables:
     * - $userTypes: collection of App\Models\UserType
     */
@endphp

@extends('layouts.app')
@section('title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="m-0">Create New Account</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.admin.users.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" id="employee_id_display" class="form-control" readonly value="{{ 'EMP' . str_pad(\App\Models\User::max('id') + 1, 5, '0', STR_PAD_LEFT) }}">
                            <small class="text-muted">Automatically generated</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="user_type_id" id="user_type_id" class="form-select" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('user_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Branch Assignment -->
                        <div class="mt-3">
                            <label class="form-label">Branch Assignment</label>
                            <select name="branch_id" id="branch_id" class="form-select">
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Profile Picture (optional)</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Branch field is now always visible - no need for toggle logic
</script>
@endpush
