@php
    /**
     * Variables:
     * - $userTypes: collection of App\Models\UserType
     */
@endphp

@extends('layouts.app')
@section('title', 'Create User')

@push('stylesDashboard')
<style>
    .create-account-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .create-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        animation: slideInUp 0.6s ease-out;
        border: 1px solid rgba(0,0,0,0.05);
    }

    @keyframes slideInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .create-card-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .create-card-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3) 50%, transparent);
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .create-card-header h4 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        letter-spacing: -0.5px;
    }

    .form-control {
        border: 2px solid rgba(79, 172, 254, 0.2);
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.95);
        border-color: #4facfe;
        box-shadow: 0 0 20px rgba(79, 172, 254, 0.3);
        transform: scale(1.02);
    }

    .form-select {
        border: 2px solid rgba(79, 172, 254, 0.2);
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        background: rgba(255, 255, 255, 0.95);
        border-color: #4facfe;
        box-shadow: 0 0 20px rgba(79, 172, 254, 0.3);
        transform: scale(1.02);
    }

    .form-label {
        color: #333;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 15px 30px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2) 50%, transparent);
        transition: all 0.5s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 10px 30px rgba(79, 172, 254, 0.4);
        background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:active {
        transform: scale(0.95);
        box-shadow: 0 2px 8px rgba(79, 172, 254, 0.5);
    }

    .alert-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(238, 90, 36, 0.3);
    }
</style>
@endpush

@section('content')
<div class="create-account-container">
    <div class="row">
        <div class="col-12">
            <div class="create-card">
                <div class="create-card-header">
                    <h4>Create New Account</h4>
                </div>
                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
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
                            <button type="submit" class="btn-submit">Create Account</button>
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
