@extends('layouts.app')

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

    .sp-modal .modal-content{border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 18px 60px rgba(13,71,161,0.28);}
    .sp-modal .modal-header{background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);border-bottom:none;position:relative;overflow:hidden;padding:16px 18px;}
    .sp-modal .modal-header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-modal .modal-title{color:#fff;font-weight:900;letter-spacing:.01em;position:relative;z-index:1;display:flex;align-items:center;gap:10px;}
    .sp-modal .modal-title i{color:rgba(0,229,255,.85);}
    .sp-modal .btn-close{filter:invert(1);opacity:.85;position:relative;z-index:1;}
    .sp-modal .modal-body{background:#fff;padding:16px 18px;}
    .sp-modal .modal-footer{background:#fff;border-top:1px solid rgba(25,118,210,0.10);padding:14px 18px;}

    .sp-label{display:block;font-size:11.5px;font-weight:800;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:7px;}
    .sp-input{width:100%;border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;}
    .sp-input:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}
    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:800;cursor:pointer;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
    .sp-btn-ghost{background:rgba(13,71,161,0.04);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-ghost:hover{background:var(--navy);color:#fff;border-color:var(--navy);}
</style>

<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-ruler"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Settings</div>
                        <div class="sp-ph-title">Unit Types</div>
                        <div class="sp-ph-sub">Manage measurement unit types</div>
                    </div>
                </div>
                <button type="button" class="sp-btn sp-btn-primary" data-bs-toggle="modal" data-bs-target="#unitTypeModal" onclick="openUnitTypeModal()">
                    <i class="fas fa-plus"></i> Add Unit Type
                </button>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Unit Type List</div>
                    <div class="d-flex gap-2" style="position:relative;z-index:1;"></div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-table-wrap">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unitTypes as $unitType)
                                <tr>
                                    <td>{{ $unitType->id }}</td>
                                    <td>{{ $unitType->unit_name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#unitTypeModal" onclick="editUnitType({{ $unitType->id }}, '{{ $unitType->unit_name }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('superadmin.unit-types.destroy', $unitType) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No unit types found.</td>
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

<!-- Unit Type Modal -->
<div class="modal fade" id="unitTypeModal" tabindex="-1" aria-labelledby="unitTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content sp-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="unitTypeModalLabel"><i class="fas fa-ruler"></i> Add Unit Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="unitTypeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_name" class="sp-label">Unit Type Name</label>
                        <input type="text" class="sp-input" id="unit_name" name="unit_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="sp-btn sp-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="sp-btn sp-btn-primary">Save Unit Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setUnitTypeModalTitle(mode) {
    const el = document.getElementById('unitTypeModalLabel');
    if (!el) return;
    const title = mode === 'edit' ? 'Edit Unit Type' : 'Add Unit Type';
    el.innerHTML = `<i class="fas fa-ruler"></i> ${title}`;
}

function openUnitTypeModal() {
    document.getElementById('unitTypeForm').action = '{{ route("superadmin.unit-types.store") }}';
    document.getElementById('unitTypeForm').querySelector('input[name="_method"]')?.remove();
    setUnitTypeModalTitle('add');
    document.getElementById('unit_name').value = '';
}

function editUnitType(id, name) {
    document.getElementById('unitTypeForm').action = '/superadmin/unit-types/' + id;
    if (!document.getElementById('unitTypeForm').querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('unitTypeForm').appendChild(methodInput);
    }
    setUnitTypeModalTitle('edit');
    document.getElementById('unit_name').value = name;
}
</script>
@endpush