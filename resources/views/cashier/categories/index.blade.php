@extends('layouts.app')
@section('title', 'Categories')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-fixed {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    </style>
@endpush

@section('content')
<div class="d-flex min-vh-100">
    
    <div class="container-fluid categories-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="categories-header-card">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                <div>
                                    <h2 class="m-0 mb-1">Categories</h2>
                                    <p class="mb-0">Manage your product categories</p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="search-wrapper">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <path d="m21 21-4.35-4.35"></path>
                                        </svg>
                                        <input type="text" name="search" id="category-search-input" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                                    </div>
                                    <a href="{{ route('cashier.categories.create') }}" class="btn btn-add-category d-flex align-items-center gap-2">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 5v14M5 12h14"></path>
                                        </svg>
                                        Add Category
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="categories-table-card">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
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
                                                    <span class="badge {{ $category->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($category->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">No categories found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
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
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarHTML = sessionStorage.getItem('cashierSidebarHTML') || localStorage.getItem('cashierSidebarHTML');
        if (sidebarHTML) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = sidebarHTML;
            const appendedSidebar = wrapper.firstElementChild;
            if (appendedSidebar) {
                document.body.appendChild(appendedSidebar);
            }

            const sidebar = appendedSidebar || document.querySelector('body > div[style*="position: fixed"][style*="left: 0"]');
            if (sidebar) {
                // Ensure the sidebar itself is visible
                sidebar.style.transform = 'translateX(0)';
                sidebar.style.zIndex = '2000';

                // The cloned cards are initially hidden by inline styles, so we need to make them visible.
                const navItems = sidebar.querySelectorAll('.nav-card');
                navItems.forEach(item => {
                    item.style.transform = 'translateX(0)';
                    item.style.opacity = '1';
                });

                // Re-attach event listener for the logo to navigate back to the dashboard
                const logoImg = sidebar.querySelector('img[src*="BGH LOGO.png"]');
                if (logoImg) {
                    logoImg.addEventListener('click', () => {
                        window.location.href = '{{ route('cashier.dashboard') }}';
                    });
                }

                // Add hover-expandable functionality
                const expandedWidth = 220;
                sidebar.style.width = expandedWidth + 'px';
                sidebar.style.padding = '20px 10px';
                sidebar.style.overflowX = 'hidden';

                // Keep page content visible (sidebar should not cover headers)
                const page = document.querySelector('.categories-page');
                if (page) {
                    page.style.transition = 'margin-left 0.2s ease';
                    page.style.marginLeft = expandedWidth + 'px';
                }

                // Ensure nav text stays visible
                navItems.forEach(item => {
                    item.style.justifyContent = 'flex-start';
                    item.style.gap = '16px';
                    item.style.paddingLeft = '20px';
                    item.style.paddingRight = '20px';

                    const icon = item.querySelector('.nav-icon');
                    if (icon) icon.style.margin = '0';

                    const content = item.querySelector('.nav-content');
                    if (content) {
                        content.style.opacity = '1';
                        content.style.pointerEvents = 'auto';
                    }
                });

                // Nest Product Category under Products
                const itemsArr = Array.from(navItems);
                const productsItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Products');
                const categoryItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Product Category');
                if (productsItem && categoryItem) {
                    categoryItem.style.marginLeft = '18px';
                    categoryItem.style.paddingLeft = '20px';
                    productsItem.insertAdjacentElement('afterend', categoryItem);
                }
            }
        } else {
            console.warn('Cashier sidebar not found in sessionStorage/localStorage (cashierSidebarHTML).');
        }
        const editBtn = document.getElementById('editBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        let selectedIds = [];

        function updateButtonStates() {
            if (editBtn) editBtn.disabled = selectedIds.length !== 1;
            if (deleteBtn) deleteBtn.disabled = selectedIds.length === 0;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const name = row.dataset.name;
                const status = row.dataset.status;

                if (this.checked) {
                    selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(selectedId => selectedId !== id);
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
                if (selectedIds.length > 0 && confirm('Are you sure you want to delete the selected categories?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("cashier.categories.deleteMultiple") }}';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    selectedIds.forEach(id => {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'ids[]';
                        idInput.value = id;
                        form.appendChild(idInput);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });

    function openCategoryModal() {
        document.getElementById('categoryForm').action = '{{ route("cashier.categories.store") }}';
        document.getElementById('categoryForm').querySelector('input[name="_method"]')?.remove();
        document.getElementById('categoryModalLabel').textContent = 'Add Category';
        document.getElementById('category_name').value = '';
        document.getElementById('status').value = 'active';
    }

    function editCategory(id, name, status) {
        document.getElementById('categoryForm').action = '/cashier/categories/' + id;
        if (!document.getElementById('categoryForm').querySelector('input[name="_method"]')) {
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            document.getElementById('categoryForm').appendChild(methodInput);
        }
        document.getElementById('categoryModalLabel').textContent = 'Edit Category';
        document.getElementById('category_name').value = name;
        document.getElementById('status').value = status;
    }
</script>
@endsection
