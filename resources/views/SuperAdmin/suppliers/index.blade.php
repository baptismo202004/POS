@extends('layouts.app')
@section('title', 'Suppliers')

@include('layouts.theme-base')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root{--navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--bg:#EBF3FB;--card:#fff;--border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;}
    .sp-page{position:relative;}
    .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
    .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
    .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
    .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
    .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}
    .sp-wrap{position:relative;z-index:1;padding:18px 10px 42px;}
    @media(min-width:992px){.sp-wrap{padding:24px 18px 54px;}}
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
    .sp-search-bar{padding:12px 22px;border-bottom:1px solid var(--border);background:rgba(13,71,161,0.02);display:flex;align-items:center;gap:8px;}
    .sp-search-bar input{padding:7px 12px 7px 32px;border-radius:9px;border:1.5px solid var(--border);font-size:13px;background:var(--card);color:var(--text);outline:none;width:260px;}
    .sp-search-bar input:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.1);}
    .sp-search-wrap{position:relative;display:inline-block;}
    .sp-search-wrap i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;}
    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:900;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}
</style>
@endpush

@section('content')
<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Management</div>
                        <div class="sp-ph-title">Suppliers</div>
                        <div class="sp-ph-sub">Manage supplier information and contacts</div>
                    </div>
                </div>
                <button type="button" class="sp-btn sp-btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Supplier List</div>
                    <div style="position:relative;z-index:1;background:rgba(255,255,255,0.15);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">
                        {{ $suppliers->total() }} suppliers
                    </div>
                </div>

                <div class="sp-search-bar">
                    <div class="sp-search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search suppliers...">
                    </div>
                </div>

                <div class="sp-table-wrap">
                    <table class="sp-table" id="suppliersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Supplier Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td><span class="badge badge-secondary">#{{ $supplier->id }}</span></td>
                                    <td><div class="fw-semibold" style="color:var(--navy);">{{ $supplier->supplier_name }}</div></td>
                                    <td>{{ $supplier->contact_person ?? '—' }}</td>
                                    <td>{{ $supplier->phone ?? '—' }}</td>
                                    <td>{{ $supplier->email ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $supplier->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm js-edit-btn"
                                                    data-id="{{ $supplier->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm js-delete-btn"
                                                    data-id="{{ $supplier->id }}"
                                                    data-name="{{ $supplier->supplier_name }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-truck fa-3x mb-3 text-muted d-block"></i>
                                        <div class="fw-semibold">No suppliers found</div>
                                        <small class="text-muted">Start by adding your first supplier</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suppliers->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $suppliers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#0D47A1,#1976D2);">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle me-2"></i>Add Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSupplierForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_supplier_name" name="supplier_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Person</label>
                            <input type="text" class="form-control" id="add_contact_person" name="contact_person">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="add_email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" id="add_phone" name="phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" id="add_address" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="add_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1565C0,#42A5F5);">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>Edit Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSupplierForm">
                @csrf
                <input type="hidden" id="edit_supplier_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_supplier_name" name="supplier_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Person</label>
                            <input type="text" class="form-control" id="edit_contact_person" name="contact_person">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
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
const supStoreUrl   = '{{ route("suppliers.store") }}';
const supUpdateBase = '{{ url("suppliers") }}';
const supCsrf       = '{{ csrf_token() }}';

async function submitSupplierForm(url, body, modalId, isUpdate) {
    try {
        const res  = await fetch(url, {
            method: 'POST', body,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
            await Swal.fire({
                icon: 'success',
                title: isUpdate ? 'Updated!' : 'Added!',
                text: isUpdate ? 'Supplier updated successfully.' : 'Supplier added successfully.',
                confirmButtonColor: '#0D47A1', timer: 2000, showConfirmButton: false,
            });
            location.reload();
        } else {
            const msgs = Object.values(data.errors || {}).flat().join('\n') || data.message || 'Something went wrong.';
            Swal.fire({ icon: 'error', title: 'Validation Error', text: msgs, confirmButtonColor: '#0D47A1' });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed. Please try again.', confirmButtonColor: '#0D47A1' });
    }
}

document.getElementById('addSupplierForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const body = new URLSearchParams({
        _token:         supCsrf,
        supplier_name:  document.getElementById('add_supplier_name').value.trim(),
        contact_person: document.getElementById('add_contact_person').value.trim(),
        email:          document.getElementById('add_email').value.trim(),
        phone:          document.getElementById('add_phone').value.trim(),
        address:        document.getElementById('add_address').value.trim(),
        status:         document.getElementById('add_status').value,
    });
    submitSupplierForm(supStoreUrl, body, 'addSupplierModal', false);
});

document.getElementById('editSupplierForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id   = document.getElementById('edit_supplier_id').value;
    const body = new URLSearchParams({
        _token:         supCsrf,
        _method:        'PUT',
        supplier_name:  document.getElementById('edit_supplier_name').value.trim(),
        contact_person: document.getElementById('edit_contact_person').value.trim(),
        email:          document.getElementById('edit_email').value.trim(),
        phone:          document.getElementById('edit_phone').value.trim(),
        address:        document.getElementById('edit_address').value.trim(),
        status:         document.getElementById('edit_status').value,
    });
    submitSupplierForm(supUpdateBase + '/' + id, body, 'editSupplierModal', true);
});

document.addEventListener('click', async function (e) {
    const editBtn = e.target.closest('.js-edit-btn');
    if (editBtn) {
        const id = editBtn.dataset.id;
        try {
            const res  = await fetch(supUpdateBase + '/' + id + '/edit', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                const s = data.supplier;
                document.getElementById('edit_supplier_id').value    = s.id;
                document.getElementById('edit_supplier_name').value  = s.supplier_name || '';
                document.getElementById('edit_contact_person').value = s.contact_person || '';
                document.getElementById('edit_email').value          = s.email || '';
                document.getElementById('edit_phone').value          = s.phone || '';
                document.getElementById('edit_address').value        = s.address || '';
                document.getElementById('edit_status').value         = s.status || 'active';
                new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
            }
        } catch {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load supplier data.', confirmButtonColor: '#0D47A1' });
        }
        return;
    }

    const delBtn = e.target.closest('.js-delete-btn');
    if (delBtn) {
        const id   = delBtn.dataset.id;
        const name = delBtn.dataset.name;
        const r = await Swal.fire({
            icon: 'warning', title: 'Delete this supplier?', text: name,
            showCancelButton: true, confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel', confirmButtonColor: '#E63946',
        });
        if (!r.isConfirmed) return;
        try {
            const body = new URLSearchParams({ _token: supCsrf, _method: 'DELETE' });
            const res  = await fetch(supUpdateBase + '/' + id, {
                method: 'POST', body,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                await Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, timer: 2000, showConfirmButton: false, confirmButtonColor: '#0D47A1' });
                location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not delete.', confirmButtonColor: '#0D47A1' });
            }
        } catch {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed.', confirmButtonColor: '#0D47A1' });
        }
    }
});

// Search
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#suppliersTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
