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
    .sp-table-scroll{max-height:520px;overflow-y:auto;overflow-x:hidden;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:900;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}
    .badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:900;letter-spacing:.02em;border:1px solid transparent;}
</style>

<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-tag"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Settings</div>
                        <div class="sp-ph-title">Brands</div>
                        <div class="sp-ph-sub">Manage product brands and manufacturers</div>
                    </div>
                </div>
                <button type="button" class="sp-btn sp-btn-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                    <i class="fas fa-plus"></i> Add Brand
                </button>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Brand List</div>
                    <div class="d-flex gap-2" style="position:relative;z-index:1;"></div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-table-wrap sp-table-scroll">
                        <table class="sp-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td><span class="badge badge-secondary">#{{ $brand->id }}</span></td>
                                        <td>
                                            <div class="fw-semibold" style="color: var(--electric-blue);">{{ $brand->brand_name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $brand->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                                {{ ucfirst($brand->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="editBrand({{ $brand->id }}, '{{ addslashes($brand->brand_name) }}', '{{ $brand->status }}')">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form method="POST" action="{{ route('superadmin.brands.destroy', $brand) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm js-brand-delete" data-brand-name="{{ $brand->brand_name }}">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-tag fa-3x mb-3"></i>
                                                <div class="fw-semibold">No brands found</div>
                                                <small>Start by adding your first brand</small>
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

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#0D47A1,#1976D2);">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle me-2"></i>Add Brand</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBrandForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_brand_name" name="brand_name" placeholder="e.g. Samsung" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1565C0,#42A5F5);">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>Edit Brand</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBrandForm">
                @csrf
                <input type="hidden" id="edit_brand_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_brand_name" name="brand_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const brandStoreUrl   = '{{ route("superadmin.brands.store") }}';
const brandUpdateBase = '{{ url("brands") }}';
const brandCsrf       = '{{ csrf_token() }}';

function editBrand(id, name, status) {
    document.getElementById('edit_brand_id').value   = id;
    document.getElementById('edit_brand_name').value = name;
    document.getElementById('edit_status').value     = status;
    new bootstrap.Modal(document.getElementById('editBrandModal')).show();
}

async function submitBrandForm(url, body, modalId, isUpdate) {
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
                title: isUpdate ? 'Updated!' : 'Brand Added!',
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

document.getElementById('addBrandForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const body = new URLSearchParams({
        _token:     brandCsrf,
        brand_name: document.getElementById('add_brand_name').value.trim(),
        status:     document.getElementById('add_status').value,
    });
    submitBrandForm(brandStoreUrl, body, 'addBrandModal', false);
});

document.getElementById('editBrandForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id   = document.getElementById('edit_brand_id').value;
    const body = new URLSearchParams({
        _token:     brandCsrf,
        _method:    'PUT',
        brand_name: document.getElementById('edit_brand_name').value.trim(),
        status:     document.getElementById('edit_status').value,
    });
    submitBrandForm(brandUpdateBase + '/' + id, body, 'editBrandModal', true);
});

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-brand-delete');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('form');
    Swal.fire({
        icon: 'warning',
        title: 'Delete this brand?',
        text: btn.getAttribute('data-brand-name') || '',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#E63946',
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush