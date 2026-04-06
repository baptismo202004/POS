@extends('layouts.app')
@section('title', 'Supplier Details')

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
    .sp-btn-outline{background:rgba(13,71,161,0.06);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-outline:hover{background:var(--navy);color:#fff;}
    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;margin-bottom:20px;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head-title{font-size:14.5px;font-weight:900;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-card-body{padding:22px;}
    .sp-detail-row{display:flex;padding:12px 0;border-bottom:1px solid rgba(25,118,210,0.06);}
    .sp-detail-row:last-child{border-bottom:none;}
    .sp-detail-label{width:160px;flex-shrink:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);}
    .sp-detail-value{font-size:13.5px;color:var(--text);font-weight:500;}
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
                        <div class="sp-ph-crumb">Management · Suppliers</div>
                        <div class="sp-ph-title">{{ $supplier->supplier_name }}</div>
                        <div class="sp-ph-sub">Supplier details and information</div>
                    </div>
                </div>
                <div style="display:flex;gap:9px;flex-wrap:wrap;">
                    <a href="{{ route('suppliers.index') }}" class="sp-btn sp-btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Suppliers
                    </a>
                    <button type="button" class="sp-btn sp-btn-primary" data-bs-toggle="modal" data-bs-target="#editSupplierModal">
                        <i class="fas fa-edit"></i> Edit Supplier
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="sp-card">
                        <div class="sp-card-head">
                            <div class="sp-card-head-title"><i class="fas fa-info-circle"></i> Supplier Information</div>
                        </div>
                        <div class="sp-card-body">
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Supplier Name</div>
                                <div class="sp-detail-value fw-semibold" style="color:var(--navy);">{{ $supplier->supplier_name }}</div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Contact Person</div>
                                <div class="sp-detail-value">{{ $supplier->contact_person ?? '—' }}</div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Phone</div>
                                <div class="sp-detail-value">
                                    @if($supplier->phone)
                                        <a href="tel:{{ $supplier->phone }}" style="color:var(--blue);">{{ $supplier->phone }}</a>
                                    @else —
                                    @endif
                                </div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Email</div>
                                <div class="sp-detail-value">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" style="color:var(--blue);">{{ $supplier->email }}</a>
                                    @else —
                                    @endif
                                </div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Address</div>
                                <div class="sp-detail-value">{{ $supplier->address ?? '—' }}</div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Status</div>
                                <div class="sp-detail-value">
                                    <span class="badge {{ $supplier->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Created</div>
                                <div class="sp-detail-value">{{ $supplier->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                            <div class="sp-detail-row">
                                <div class="sp-detail-label">Last Updated</div>
                                <div class="sp-detail-value">{{ $supplier->updated_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sp-card">
                        <div class="sp-card-head">
                            <div class="sp-card-head-title"><i class="fas fa-bolt"></i> Quick Actions</div>
                        </div>
                        <div class="sp-card-body" style="display:flex;flex-direction:column;gap:10px;">
                            <a href="{{ route('superadmin.purchases.create') }}?supplier_id={{ $supplier->id }}" class="sp-btn sp-btn-primary" style="justify-content:center;">
                                <i class="fas fa-shopping-cart"></i> Create Purchase
                            </a>
                            @if($supplier->email)
                            <a href="https://mail.google.com/mail/?view=cm&to={{ $supplier->email }}" target="_blank" class="sp-btn sp-btn-outline" style="justify-content:center;">
                                <i class="fas fa-envelope"></i> Send Email
                            </a>
                            @endif
                            @if($supplier->phone)
                            <a href="tel:{{ $supplier->phone }}" class="sp-btn sp-btn-outline" style="justify-content:center;">
                                <i class="fas fa-phone"></i> Call Supplier
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

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
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="supplier_name" value="{{ $supplier->supplier_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Person</label>
                            <input type="text" class="form-control" name="contact_person" value="{{ $supplier->contact_person ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $supplier->email ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="{{ $supplier->phone ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea class="form-control" name="address" rows="2">{{ $supplier->address ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                <option value="active" {{ $supplier->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $supplier->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
document.getElementById('editSupplierForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const body = new URLSearchParams({
        _token:         '{{ csrf_token() }}',
        _method:        'PUT',
        supplier_name:  this.supplier_name.value.trim(),
        contact_person: this.contact_person.value.trim(),
        email:          this.email.value.trim(),
        phone:          this.phone.value.trim(),
        address:        this.address.value.trim(),
        status:         this.status.value,
    });
    try {
        const res  = await fetch('{{ url("suppliers/" . $supplier->id) }}', {
            method: 'POST', body,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editSupplierModal')).hide();
            await Swal.fire({ icon: 'success', title: 'Updated!', text: 'Supplier updated successfully.', timer: 2000, showConfirmButton: false, confirmButtonColor: '#0D47A1' });
            location.reload();
        } else {
            const msgs = Object.values(data.errors || {}).flat().join('\n') || data.message || 'Something went wrong.';
            Swal.fire({ icon: 'error', title: 'Error', text: msgs, confirmButtonColor: '#0D47A1' });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed. Please try again.', confirmButtonColor: '#0D47A1' });
    }
});
</script>
@endpush
