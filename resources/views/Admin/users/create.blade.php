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
    :root{
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
        --green:#10b981;--red:#ef4444;--amber:#f59e0b;
        --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
        --text:#1a2744;--muted:#6b84aa;
    }

    .sp-page{position:relative;min-height:100vh;}
    .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
    .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
    .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
    .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
    .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    .sp-wrap{position:relative;z-index:1;padding:18px 10px 42px;}
    @media (min-width: 992px){.sp-wrap{padding:24px 18px 54px;}}

    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:14px;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:800;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;}
    .sp-ph-title{font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:800;cursor:pointer;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
    .sp-btn-ghost{background:rgba(13,71,161,0.04);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-ghost:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
    .sp-card-head-title{margin:0;font-size:16px;font-weight:900;color:#fff;position:relative;z-index:1;display:flex;align-items:center;gap:10px;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-card-body{padding:18px 22px;}

    .sp-label{display:block;font-size:11.5px;font-weight:800;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:7px;}
    .sp-input,.sp-select{width:100%;border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;}
    .sp-input:focus,.sp-select:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}
    .sp-hint{font-size:11.5px;color:var(--muted);margin-top:6px;}

    .sp-alert{border-radius:14px;border:1px solid rgba(239,68,68,0.20);background:rgba(239,68,68,0.08);color:#b91c1c;}
</style>
@endpush

@section('content')
<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-user-plus"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Admin</div>
                        <div class="sp-ph-title">Create User</div>
                        <div class="sp-ph-sub">Create a new account and assign access</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="sp-btn sp-btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
                    <button type="submit" form="spCreateUserForm" class="sp-btn sp-btn-primary"><i class="fas fa-save"></i> Create</button>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <h4 class="sp-card-head-title"><i class="fas fa-user"></i> Create New Account</h4>
                </div>
                <div class="sp-card-body">
                    @if ($errors->any())
                        <div class="alert sp-alert mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="spCreateUserForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="sp-label">Name</label>
                            <input type="text" name="name" class="sp-input" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="sp-label">Employee ID</label>
                            <input type="text" id="employee_id_display" class="sp-input" readonly value="{{ 'EMP' . str_pad(\App\Models\User::max('id') + 1, 5, '0', STR_PAD_LEFT) }}">
                            <div class="sp-hint">Automatically generated</div>
                        </div>

                        <div class="mb-3">
                            <label class="sp-label">Email</label>
                            <input type="email" name="email" class="sp-input" value="{{ old('email') }}" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="sp-label">Password</label>
                                <input type="password" name="password" class="sp-input" required>
                            </div>
                            <div class="col-md-6">
                                <label class="sp-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="sp-input" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="sp-label">Role</label>
                                <select name="user_type_id" id="user_type_id" class="sp-select" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('user_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="sp-label">Status</label>
                                <select name="status" class="sp-select">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Branch Assignment -->
                        <div class="mt-3">
                            <label class="sp-label">Branch Assignment</label>
                            <select name="branch_id" id="branch_id" class="sp-select">
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
                            <label class="sp-label">Profile Picture (optional)</label>
                            <input type="file" name="profile_picture" class="sp-input" accept="image/*">
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="sp-btn sp-btn-ghost">Cancel</a>
                            <button type="submit" class="sp-btn sp-btn-primary">Create Account</button>
                        </div>
                    </form>
                </div>
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
