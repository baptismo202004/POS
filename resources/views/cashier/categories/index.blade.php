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
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.13);
            --text:    #1a2744;
            --muted:   #6b84aa;
            --red:     #ef4444;
            --green:   #10b981;
            --amber:   #f59e0b;
            --shadow:  0 4px 28px rgba(13,71,161,0.09);
        }

        .categories-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .categories-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .categories-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .categories-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .categories-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .categories-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .categories-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .categories-page .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .categories-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .categories-page .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
            flex-shrink: 0;
        }
        .categories-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .categories-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .categories-page .action-bar {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .categories-page .search-wrapper { position: relative; }
        .categories-page .search-wrapper .fas.fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
            pointer-events: none;
            z-index: 2;
        }
        .categories-page .search-wrapper input {
            padding: 9px 14px 9px 36px !important;
            border-radius: 11px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--card);
            color: var(--text);
            outline: none;
            width: 220px;
            transition: border-color .18s, box-shadow .18s;
        }
        .categories-page .search-wrapper input:focus { border-color: var(--blue-lt); box-shadow: 0 0 0 3px rgba(66,165,245,0.12); }
        .categories-page .search-wrapper input::placeholder { color: #b0c0d8; }

        .categories-page .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .categories-page .btn:disabled { opacity: .5; cursor: not-allowed; transform: none !important; }

        .categories-page .btn-add-category,
        .categories-page .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .categories-page .btn-add-category:hover:not(:disabled),
        .categories-page .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(13,71,161,0.34);
        }

        .categories-page .btn-edit-category {
            background: linear-gradient(135deg, #d97706, var(--amber));
            color: #fff;
            box-shadow: 0 4px 14px rgba(245,158,11,0.22);
            border: none;
        }
        .categories-page .btn-edit-category:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(245,158,11,0.32);
        }

        .categories-page .btn-delete-category {
            background: linear-gradient(135deg, #dc2626, var(--red));
            color: #fff;
            box-shadow: 0 4px 14px rgba(239,68,68,0.22);
            border: none;
        }
        .categories-page .btn-delete-category:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(239,68,68,0.32);
        }

        .categories-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .categories-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .categories-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .categories-page .c-head::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -90px;
            right: -50px;
            pointer-events: none;
        }
        .categories-page .c-head-title {
            font-family:'Nunito',sans-serif;
            font-size:14.5px;
            font-weight:800;
            color:#fff;
            display:flex;
            align-items:center;
            gap:8px;
            position:relative;
            z-index:1;
        }
        .categories-page .c-head-title i { color:rgba(0,229,255,.85); }
        .categories-page .c-badge {
            position:relative;
            z-index:1;
            background:rgba(255,255,255,0.15);
            border:1px solid rgba(255,255,255,0.25);
            color:#fff;
            font-size:11px;
            font-weight:700;
            padding:3px 10px;
            border-radius:20px;
            font-family:'Nunito',sans-serif;
        }

        .categories-page .table-responsive {
            overflow-x: hidden;
            max-width: 100%;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .categories-page .table-responsive::-webkit-scrollbar { display: none; }
        .categories-page table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .categories-page table.table thead th {
            padding: 12px 18px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: nowrap;
        }
        .categories-page table.table tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
        }
        .categories-page table.table tbody tr:nth-child(odd) { background: #fff; }
        .categories-page table.table tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .categories-page table.table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }

        .categories-page table.table tbody tr.selected {
            background: rgba(21,101,192,0.08) !important;
            border-left: 3px solid var(--blue-lt);
        }

        .categories-page table.table td {
            padding: 13px 18px;
            font-size: 13.5px;
            vertical-align: middle;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }

        .categories-page .row-checkbox {
            width:16px;
            height:16px;
            accent-color: var(--navy);
            cursor:pointer;
        }

        /* ═══════════════════════════════════════════════════════
           ANIMATED STATUS BADGES
        ═══════════════════════════════════════════════════════ */
        .categories-page .badge.bg-success,
        .categories-page .badge.bg-secondary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            border: 1px solid transparent;
        }

        /* ── Active badge ── */
        .categories-page .badge.bg-success {
            background: rgba(16,185,129,0.11) !important;
            color: #047857 !important;
            border-color: rgba(16,185,129,0.22) !important;
        }

        /* ── Inactive badge ── */
        .categories-page .badge.bg-secondary {
            background: rgba(239,68,68,0.10) !important;
            color: #b91c1c !important;
            border-color: rgba(239,68,68,0.18) !important;
        }

        /* ── Dot base ── */
        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-block;
            position: relative;
        }

        /* ── Active dot: floating + ripple pulse ── */
        .status-dot.active-dot {
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16,185,129,0.6);
            animation: dot-float 2.4s ease-in-out infinite, dot-pulse 2s ease-in-out infinite;
        }

        @keyframes dot-float {
            0%,  100% { transform: translateY(0px);   }
            25%        { transform: translateY(-2.5px); }
            50%        { transform: translateY(0px);   }
            75%        { transform: translateY(2px);   }
        }

        @keyframes dot-pulse {
            0%   { box-shadow: 0 0 0 0   rgba(16,185,129,0.65); }
            50%  { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0   rgba(16,185,129,0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .categories-page .status-dot.active-dot {
                animation-duration: 2.4s, 2s !important;
                animation-iteration-count: infinite, infinite !important;
            }
        }

        /* ── Inactive dot: static red, no animation ── */
        .status-dot.inactive-dot {
            background: #ef4444;
        }

        /* ═══════════════════════════════════════════════════════
           END ANIMATED STATUS BADGES
        ═══════════════════════════════════════════════════════ */

        .categories-page .empty-row {
            padding: 52px 24px;
            text-align: center;
            color: var(--muted);
        }

        /* Bootstrap modal restyle (keep same IDs/behavior) */
        .categories-modal .modal-content {
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 60px rgba(13,71,161,0.22);
            overflow: hidden;
        }
        .categories-modal .modal-header {
            padding: 16px 22px;
            background: linear-gradient(135deg, var(--navy), var(--blue));
            border-bottom: 0;
            position: relative;
            overflow: hidden;
        }
        .categories-modal .modal-header::before {
            content:'';
            position:absolute;
            inset:0;
            background:radial-gradient(ellipse 80% 100% at 85% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events:none;
        }
        .categories-modal .modal-title {
            font-family:'Nunito',sans-serif;
            font-size: 15px;
            font-weight: 900;
            color: #fff;
            position: relative;
            z-index: 1;
        }
        .categories-modal .modal-header .btn-close {
            filter: invert(1) grayscale(1);
            opacity: .85;
            position: relative;
            z-index: 1;
        }
        .categories-modal .modal-body { padding: 22px; }
        .categories-modal .modal-footer {
            padding: 14px 22px;
            background: rgba(13,71,161,0.03);
            border-top: 1px solid var(--border);
            gap: 9px;
        }

        .categories-modal .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .categories-modal .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .categories-modal .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(13,71,161,0.34);
        }

        .categories-modal .btn-secondary {
            background: #fff;
            color: var(--muted);
            border: 1.5px solid var(--border);
        }
        .categories-modal .btn-secondary:hover {
            border-color: var(--blue-lt);
            color: var(--navy);
            background: rgba(66,165,245,0.06);
        }

        .categories-modal .modal-body .form-label {
            display:block;
            font-size:12px;
            font-weight:800;
            color:var(--muted);
            margin-bottom:6px;
            font-family:'Nunito',sans-serif;
        }
        .categories-modal .modal-body .form-control,
        .categories-modal .modal-body .form-select {
            border-radius: 11px;
            border: 1.5px solid var(--border);
            font-size: 13.5px;
            font-family:'Plus Jakarta Sans',sans-serif;
            background: #fff;
            color: var(--text);
            outline:none;
            transition:border-color .18s, box-shadow .18s;
        }
        .categories-modal .modal-body .form-control:focus,
        .categories-modal .modal-body .form-select:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }
    </style>
@endpush

@section('content')
<div class="categories-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-tags"></i></div>
                <div>
                    <div class="ph-title">Categories</div>
                    <div class="ph-sub">Manage your product categories</div>
                </div>
            </div>

            <div class="action-bar">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" id="category-search-input" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                </div>
                <button type="button" class="btn btn-add-category" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="fas fa-plus"></i>
                    Add Category
                </button>
                <button type="button" id="editCategoryBtn" class="btn btn-edit-category" disabled>
                    <i class="fas fa-edit"></i>
                    Edit Category
                </button>
                <button type="button" id="deleteBtn" class="btn btn-delete-category" disabled>
                    <i class="fas fa-trash"></i>
                    Delete Selected
                </button>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Category List</div>
                <span class="c-badge">{{ $categories->count() }} categor{{ $categories->count() == 1 ? 'y' : 'ies' }}</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}" data-name="{{ $category->category_name }}" data-status="{{ $category->status }}">
                                <td><input type="checkbox" class="row-checkbox"></td>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->category_name }}</td>
                                <td>
                                    @if($category->status === 'active')
                                        <span class="badge bg-success">
                                            <span class="status-dot active-dot"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <span class="status-dot inactive-dot"></span>
                                            {{ ucfirst($category->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-row">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade categories-modal" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name *</label>
                        <input type="text" name="category_name" id="edit_category_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade categories-modal" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm" action="{{ route('cashier.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Category
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Use standard CashierSidebar from layouts
        const editBtn = document.getElementById('editCategoryBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        let selectedIds = [];

        function updateButtonStates() {
            if (editBtn) editBtn.disabled = selectedIds.length !== 1;
            if (deleteBtn) deleteBtn.disabled = selectedIds.length === 0;
        }

        function editCategory(id, name, status) {
            // Find the category data
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;
            
            // Populate modal with category data
            document.getElementById('edit_category_name').value = row.dataset.name || name;
            document.getElementById('edit_status').value = row.dataset.status || status;
            
            // Set form action for update
            const form = document.getElementById('editCategoryForm');
            form.action = `/cashier/categories/${id}`;
            
            // Add PUT method override
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const name = row.dataset.name;
                const status = row.dataset.status;

                if (this.checked) {
                    selectedIds.push(id);
                    row.classList.add('selected');
                } else {
                    selectedIds = selectedIds.filter(selectedId => selectedId !== id);
                    row.classList.remove('selected');
                }
                updateButtonStates();
            });
        });

        if (editBtn) {
            editBtn.addEventListener('click', function() {
                if (selectedIds.length === 1) {
                    const row = document.querySelector(`tr[data-id="${selectedIds[0]}"]`);
                    if (row) editCategory(selectedIds[0], row.dataset.name, row.dataset.status);
                }
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                if (selectedIds.length === 0) return;
                
                const count = selectedIds.length;
                const message = count === 1 
                    ? 'Are you sure you want to delete this category? This action cannot be undone.'
                    : `Are you sure you want to delete these ${count} categories? This action cannot be undone.`;
                
                Swal.fire({
                    title: 'Delete Categories?',
                    html: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        // Show loading
                        Swal.showLoading();
                        
                        return new Promise((resolve, reject) => {
                            // Create form data
                            const formData = new FormData();
                            formData.append('_token', '{{ csrf_token() }}');
                            
                            selectedIds.forEach(id => {
                                formData.append('category_ids[]', id);
                            });
                            
                            // Send AJAX request
                            fetch('{{ route("cashier.categories.deleteMultiple.post") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    resolve(data);
                                } else {
                                    reject(data.message || 'Deletion failed');
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                reject('Network error occurred');
                            });
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Categories deleted successfully',
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        }).then(() => {
                            // Reload page after success message
                            window.location.reload();
                        });
                    }
                }).catch((error) => {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error || 'Something went wrong. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                });
            });
        }

    function openCategoryModal() {
        document.getElementById('categoryForm').action = '{{ route("cashier.categories.store") }}';
        document.getElementById('categoryForm').querySelector('input[name="_method"]')?.remove();
        document.getElementById('categoryModalLabel').textContent = 'Add Category';
        document.getElementById('category_name').value = '';
        document.getElementById('status').value = 'active';
    }

    // Search functionality
    const searchInput = document.getElementById('category-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const categoryName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const categoryId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                
                if (categoryName.includes(searchTerm) || categoryId.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle add category form submission
    document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("cashier.categories.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, treat as error
                throw new Error('Invalid response format');
            }
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                modal.hide();
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Category created successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                
                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Show error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Something went wrong'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Create error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.'
                });
            }
        });
    })

    // Handle edit form submission
    document.getElementById('editCategoryForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const categoryId = this.action.split('/').pop();
        
        // Add PUT method override
        formData.append('_method', 'PUT');
        
        fetch(`/cashier/categories/${categoryId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, treat as error
                throw new Error('Invalid response format');
            }
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                modal.hide();
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Category updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                
                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Show error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Something went wrong'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.'
                });
            }
        });
    });
</script>
@endsection