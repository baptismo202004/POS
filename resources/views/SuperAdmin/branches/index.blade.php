@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<style>
    :root{
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
        --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
        --text:#1a2744;--muted:#6b84aa;
    }
    .sp-page{position:relative;}
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
    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head-title{font-size:14.5px;font-weight:900;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-card-body{padding:18px 22px;}
    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:900;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}
    .badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:900;letter-spacing:.02em;border:1px solid transparent;}
    .user-avatar{width:42px;height:42px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;box-shadow:0 6px 18px rgba(13,71,161,0.18);}
</style>

<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-building"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Settings</div>
                        <div class="sp-ph-title">Branches</div>
                        <div class="sp-ph-sub">Manage branch locations and assignments</div>
                    </div>
                </div>
                <button type="button" class="sp-btn sp-btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                    <i class="fas fa-plus"></i> Add Branch
                </button>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Branch List</div>
                    <div class="d-flex gap-2" style="position:relative;z-index:1;"></div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-table-wrap">
                        <table class="sp-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                    <tr>
                                        <td><span class="badge badge-secondary">#{{ $branch->id }}</span></td>
                                        <td>
                                            <div class="fw-semibold" style="color: var(--electric-blue);">{{ $branch->branch_name }}</div>
                                        </td>
                                        <td>{{ $branch->address ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $branch->branch_type === 'electronics' ? 'badge-info' : 'badge-warning' }}">
                                                <i class="fas {{ $branch->branch_type === 'electronics' ? 'fa-microchip' : 'fa-shopping-cart' }} me-1"></i>
                                                {{ ucfirst($branch->branch_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $branch->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                                {{ ucfirst($branch->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="editBranch({{ $branch->id }}, '{{ addslashes($branch->branch_name) }}', '{{ addslashes($branch->address ?? '') }}', '{{ $branch->branch_type }}', '{{ $branch->status }}')">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form method="POST" action="{{ route('superadmin.branches.destroy', $branch) }}" class="d-inline js-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-sm js-delete-btn" data-name="{{ $branch->branch_name }}">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-building fa-3x mb-3"></i>
                                                <div class="fw-semibold">No branches found</div>
                                                <small>Start by adding your first branch</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#0D47A1,#1976D2);">
                <h5 class="modal-title text-white" id="addBranchModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add Branch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBranchForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_branch_name" name="branch_name" placeholder="e.g. Main Branch" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" id="add_address" name="address" placeholder="e.g. 123 Street, City">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="add_branch_type" name="branch_type" required>
                            <option value="grocery">Grocery</option>
                            <option value="electronics">Electronics</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="add_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1" aria-labelledby="editBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1565C0,#42A5F5);">
                <h5 class="modal-title text-white" id="editBranchModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Branch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBranchForm">
                @csrf
                <input type="hidden" id="edit_branch_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_branch_name" name="branch_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" id="edit_address" name="address">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_branch_type" name="branch_type" required>
                            <option value="grocery">Grocery</option>
                            <option value="electronics">Electronics</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const storeUrl   = '{{ route("superadmin.branches.store") }}';
const updateBase = '{{ url("branches") }}';
const csrfToken  = '{{ csrf_token() }}';

function editBranch(id, name, address, branchType, status) {
    document.getElementById('edit_branch_id').value        = id;
    document.getElementById('edit_branch_name').value      = name;
    document.getElementById('edit_address').value          = address || '';
    document.getElementById('edit_branch_type').value      = branchType;
    document.getElementById('edit_status').value           = status;
    new bootstrap.Modal(document.getElementById('editBranchModal')).show();
}

async function submitForm(url, body, modalId, isUpdate) {
    try {
        const res  = await fetch(url, {
            method: 'POST',
            body,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
            await Swal.fire({
                icon: 'success',
                title: isUpdate ? 'Updated!' : 'Branch Added!',
                text: data.message,
                confirmButtonColor: '#0D47A1',
                timer: 2000,
                showConfirmButton: false,
            });
            location.reload();
        } else {
            const msgs = Object.values(data.errors || {}).flat().join('\n');
            Swal.fire({ icon: 'error', title: 'Validation Error', text: msgs, confirmButtonColor: '#0D47A1' });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed. Please try again.', confirmButtonColor: '#0D47A1' });
    }
}

document.getElementById('addBranchForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const body = new URLSearchParams({
        _token:      csrfToken,
        branch_name: document.getElementById('add_branch_name').value.trim(),
        address:     document.getElementById('add_address').value.trim(),
        branch_type: document.getElementById('add_branch_type').value,
        status:      document.getElementById('add_status').value,
    });
    submitForm(storeUrl, body, 'addBranchModal', false);
});

document.getElementById('editBranchForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id   = document.getElementById('edit_branch_id').value;
    const body = new URLSearchParams({
        _token:      csrfToken,
        _method:     'PUT',
        branch_name: document.getElementById('edit_branch_name').value.trim(),
        address:     document.getElementById('edit_address').value.trim(),
        branch_type: document.getElementById('edit_branch_type').value,
        status:      document.getElementById('edit_status').value,
    });
    submitForm(updateBase + '/' + id, body, 'editBranchModal', true);
});
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-delete-btn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('.js-delete-form');
    const name = btn.dataset.name || '';
    Swal.fire({
        icon: 'warning',
        title: 'Delete this branch?',
        text: name,
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#E63946',
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush