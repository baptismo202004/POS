@extends('layouts.app')
@section('title', 'Categories')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:    #0D47A1;
        --blue:    #1976D2;
        --blue-lt: #42A5F5;
        --cyan:    #00E5FF;
        --green:   #10b981;
        --red:     #ef4444;
        --amber:   #f59e0b;
        --bg:      #EBF3FB;
        --card:    #ffffff;
        --border:  rgba(25,118,210,0.12);
        --text:    #1a2744;
        --muted:   #6b84aa;
    }

    /* ── Background ── */
    .sp-bg { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; background: var(--bg); }
    .sp-bg::before {
        content: ''; position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%, transparent 55%);
    }
    .sp-blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: .11; }
    .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
    .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    /* ── Wrap ── */
    .sp-wrap {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: 18px 18px 44px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ── Page header ── */
    .sp-page-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; flex-wrap: wrap; gap: 12px;
        animation: spUp .4s ease both;
    }
    .sp-ph-left { display: flex; align-items: center; gap: 13px; }
    .sp-ph-icon {
        width: 46px; height: 46px; border-radius: 14px;
        background: linear-gradient(135deg, var(--navy), var(--blue-lt));
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #fff;
        box-shadow: 0 6px 20px rgba(13,71,161,0.28);
    }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
    .sp-ph-actions { display:flex;align-items:center;gap:9px;flex-wrap:wrap; }

    /* ── Buttons ── */
    .sp-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 11px;
        font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: 'Nunito', sans-serif;
        border: none; transition: all .2s ease; text-decoration: none; white-space: nowrap;
    }
    .sp-btn-primary {
        background: linear-gradient(135deg, var(--navy), var(--blue));
        color: #fff; box-shadow: 0 4px 14px rgba(13,71,161,0.26);
    }
    .sp-btn-primary:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }
    .sp-btn-primary:disabled { opacity:.45;cursor:not-allowed;transform:none;box-shadow:none; }

    .sp-btn-amber {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #fff; box-shadow: 0 4px 14px rgba(245,158,11,0.28);
    }
    .sp-btn-amber:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(245,158,11,0.38);color:#fff; }
    .sp-btn-amber:disabled { opacity:.45;cursor:not-allowed;transform:none;box-shadow:none; }

    .sp-btn-danger {
        background: transparent; color: var(--red);
        border: 1.5px solid rgba(239,68,68,0.30);
    }
    .sp-btn-danger:hover { background:rgba(239,68,68,0.07);border-color:var(--red);color:var(--red); }
    .sp-btn-danger:disabled { opacity:.45;cursor:not-allowed;transform:none; }

    /* ── Main card ── */
    .sp-card {
        background: var(--card); border-radius: 20px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 28px rgba(13,71,161,0.09);
        overflow: hidden; animation: spUp .45s ease both;
    }

    /* ── Card gradient header ── */
    .sp-card-head {
        padding: 14px 18px;
        background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
        display: flex; align-items: center; justify-content: space-between;
        position: relative; overflow: hidden;
    }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-c-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif; }

    /* ── Table ── */
    .sp-table-wrap { overflow-x:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;} 
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}

    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th {
        position:sticky;top:0;z-index:3;
        background:rgba(13,71,161,0.03);
        padding:10px 14px;
        font-size:11px;font-weight:700;color:var(--navy);
        letter-spacing:.06em;text-transform:uppercase;
        border-bottom:1px solid var(--border);white-space:nowrap;
    }
    .sp-table tbody td {
        padding:11px 14px;font-size:13.5px;color:var(--text);
        border-bottom:1px solid rgba(25,118,210,0.06);
        vertical-align:middle;
        transition:background .15s;
    }
    .sp-table tbody tr:nth-child(even) td { background:rgba(240,246,255,0.55); }
    .sp-table tbody tr:hover td { background:rgba(21,101,192,0.05); }
    .sp-table tbody tr { animation:spRow .3s ease both; }
    .sp-table tbody tr:nth-child(1){animation-delay:.03s}
    .sp-table tbody tr:nth-child(2){animation-delay:.06s}
    .sp-table tbody tr:nth-child(3){animation-delay:.09s}
    .sp-table tbody tr:nth-child(4){animation-delay:.12s}
    .sp-table tbody tr:nth-child(5){animation-delay:.15s}
    @keyframes spRow{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}

    .sp-table td.empty-row { text-align:center;color:var(--muted);font-style:italic;padding:32px; }

    /* Badges */
    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }
    .sp-badge-muted { background:rgba(107,132,170,0.10);color:var(--muted); }

    /* Checkbox */
    .row-checkbox { cursor:pointer;accent-color:var(--navy);width:15px;height:15px; }

    /* ── Modals ── */
    .sp-modal .modal-content {
        border:none;border-radius:18px;
        box-shadow:0 16px 50px rgba(13,71,161,0.18);
        overflow:hidden;
    }
    .sp-modal .modal-header {
        padding:18px 24px;
        background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
        border:none;position:relative;overflow:hidden;
    }
    .sp-modal .modal-header::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-modal .modal-header::after  { content:'';position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.05);top:-70px;right:-40px;pointer-events:none; }
    .sp-modal .modal-title { font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;color:#fff;position:relative;z-index:1; }
    .sp-modal .btn-close { filter:brightness(0) invert(1);opacity:.7;position:relative;z-index:1; }
    .sp-modal .btn-close:hover { opacity:1; }

    .sp-modal .modal-body { padding:24px; }
    .sp-modal .modal-footer { border-top:1px solid var(--border);padding:16px 24px;background:rgba(13,71,161,0.02); }

    /* Modal form controls */
    .sp-modal .form-label { font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;font-family:'Nunito',sans-serif; }
    .sp-modal .form-control,
    .sp-modal .form-select {
        border-radius:11px;border:1.5px solid var(--border);
        padding:10px 14px;font-size:13.5px;color:var(--text);
        background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;
        transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;
    }
    .sp-modal .form-control:focus,
    .sp-modal .form-select:focus {
        border-color:var(--blue-lt);
        box-shadow:0 0 0 3px rgba(66,165,245,0.12);
        background:#fff;
    }

    /* Modal buttons */
    .sp-modal-btn {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 20px;border-radius:11px;border:none;cursor:pointer;
        font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;
        transition:all .2s ease;
    }
    .sp-modal-btn-primary {
        background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;
        box-shadow:0 4px 14px rgba(13,71,161,0.26);
    }
    .sp-modal-btn-primary:hover { transform:translateY(-1px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }
    .sp-modal-btn-cancel {
        background:transparent;color:var(--muted);
        border:1.5px solid var(--border);
    }
    .sp-modal-btn-cancel:hover { background:rgba(107,132,170,0.08);color:var(--text); }

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@include('layouts.theme-base')

@section('content')

<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <main class="flex-fill p-3" style="position:relative;z-index:1;">
        <div class="sp-wrap">

            {{-- ── Page header ── --}}
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-tags"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Management</div>
                        <div class="sp-ph-title">Categories</div>
                        <div class="sp-ph-sub">Manage product categories and classifications</div>
                    </div>
                </div>
                <div class="sp-ph-actions">
                    <button type="button" class="sp-btn sp-btn-primary"
                            data-bs-toggle="modal" data-bs-target="#categoryModal"
                            onclick="openCategoryModal()">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>
            </div>

            {{-- ── Main card ── --}}
            <div class="sp-card">

                {{-- Card gradient header --}}
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-list"></i> Category List
                    </div>
                    <span class="sp-c-badge">{{ $categories->count() }} categories</span>
                </div>

                {{-- Table --}}
                <div class="sp-table-wrap">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th style="width:110px;">Status</th>
                                <th style="width:130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr data-id="{{ $category->id }}"
                                    data-name="{{ $category->category_name }}"
                                    data-category-type="{{ $category->category_type ?? 'non_electronic' }}"
                                    data-status="{{ $category->status }}">
                                    <td style="font-weight:700;color:var(--navy);">#{{ $category->id }}</td>
                                    <td style="font-weight:500;">{{ $category->category_name }}</td>
                                    <td>
                                        <span class="sp-badge sp-badge-muted" style="font-size:10px;">
                                            {{ match($category->category_type ?? 'non_electronic') {
                                                'electronic_with_serial'    => 'Electronic (serial)',
                                                'electronic_without_serial' => 'Electronic',
                                                default                     => 'Non-Electronic',
                                            } }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->status === 'active')
                                            <span class="sp-badge sp-badge-green">
                                                <i class="fas fa-circle me-1" style="font-size:7px;"></i> Active
                                            </span>
                                        @else
                                            <span class="sp-badge sp-badge-muted">
                                                <i class="fas fa-circle me-1" style="font-size:7px;"></i> {{ ucfirst($category->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="editCategory({{ $category->id }}, '{{ addslashes($category->category_name) }}', '{{ $category->category_type ?? 'non_electronic' }}', '{{ $category->status }}')"
                                                    data-bs-toggle="modal" data-bs-target="#categoryModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm js-cat-delete-btn"
                                                    data-id="{{ $category->id }}"
                                                    data-name="{{ $category->category_name }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="fas fa-inbox me-2"></i>No categories found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>{{-- end sp-card --}}
        </div>{{-- end sp-wrap --}}
    </main>
</div>

{{-- ══════════════════════════════════════
     MODALS — all functionality preserved
══════════════════════════════════════ --}}

<!-- Category Modal (Add / Edit) -->
<div class="modal fade sp-modal" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name"
                               placeholder="Enter category name" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_type" class="form-label">Category Type</label>
                        <select class="form-select" id="category_type" name="category_type" required>
                            <option value="non_electronic">Non Electronic</option>
                            <option value="electronic_without_serial">Electronic without serial</option>
                            <option value="electronic_with_serial">Electronic with serial</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="sp-modal-btn sp-modal-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="sp-modal-btn sp-modal-btn-primary">
                        <i class="fas fa-save"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const catStoreUrl   = '{{ route("superadmin.categories.store") }}';
const catUpdateBase = '{{ url("superadmin/categories") }}';
const catCsrf       = '{{ csrf_token() }}';

function openCategoryModal() {
    document.getElementById('categoryForm').action = catStoreUrl;
    document.getElementById('categoryForm').querySelector('input[name="_method"]')?.remove();
    document.getElementById('categoryModalLabel').textContent = 'Add Category';
    document.getElementById('category_name').value  = '';
    document.getElementById('status').value         = 'active';
    document.getElementById('category_type').value  = 'non_electronic';
}

function editCategory(id, name, categoryType, status) {
    document.getElementById('categoryForm').action = catUpdateBase + '/' + id;
    if (!document.getElementById('categoryForm').querySelector('input[name="_method"]')) {
        const m = document.createElement('input');
        m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
        document.getElementById('categoryForm').appendChild(m);
    }
    document.getElementById('categoryModalLabel').textContent = 'Edit Category';
    document.getElementById('category_name').value = name;
    document.getElementById('status').value        = status;
    document.getElementById('category_type').value = categoryType || 'non_electronic';
}

document.getElementById('categoryForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form   = this;
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const body   = new FormData(form);

    fetch(form.action, {
        method: 'POST', body,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            Swal.fire({
                icon: 'success',
                title: isEdit ? 'Updated!' : 'Added!',
                text: isEdit ? 'Category updated successfully.' : 'Category added successfully.',
                timer: 2000, showConfirmButton: false,
                confirmButtonColor: '#0D47A1',
            }).then(() => location.reload());
        } else {
            const msgs = Object.values(data.errors || {}).flat().join('\n') || data.message || 'Something went wrong.';
            Swal.fire({ icon: 'error', title: 'Error', text: msgs, confirmButtonColor: '#ef4444' });
        }
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed. Please try again.', confirmButtonColor: '#ef4444' }));
});

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-cat-delete-btn');
    if (!btn) return;
    const id   = btn.dataset.id;
    const name = btn.dataset.name;
    Swal.fire({
        icon: 'warning',
        title: 'Delete this category?',
        text: name,
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ef4444',
    }).then(async r => {
        if (!r.isConfirmed) return;
        try {
            const body = new URLSearchParams({ _token: catCsrf, _method: 'DELETE' });
            const res  = await fetch(catUpdateBase + '/' + id, {
                method: 'POST', body,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                await Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, timer: 2000, showConfirmButton: false, confirmButtonColor: '#0D47A1' });
                location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not delete.', confirmButtonColor: '#ef4444' });
            }
        } catch {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed. Please try again.', confirmButtonColor: '#ef4444' });
        }
    });
});
</script>

@endsection