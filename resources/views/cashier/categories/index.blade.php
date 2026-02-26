@extends('layouts.app')
@section('title', 'Categories')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .search-wrapper .fas.fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
            font-size: 14px;
        }
        
        .search-wrapper input {
            padding-left: 40px !important;
            width: 250px;
        }
        
        .btn-edit-category {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        
        .btn-edit-category:hover:not(:disabled) {
            background-color: #e0a800;
            border-color: #e0a800;
            color: #fff;
        }
        
        .btn-edit-category:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-delete-category {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        
        .btn-delete-category:hover:not(:disabled) {
            background-color: #c82333;
            border-color: #c82333;
            color: #fff;
        }
        
        .btn-delete-category:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .table tbody tr.selected {
            background-color: #e3f2fd !important;
            border-left: 4px solid #2196f3;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .categories-page {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
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
                                        <i class="fas fa-search"></i>
                                        <input type="text" name="search" id="category-search-input" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                                    </div>
                                    <button type="button" class="btn btn-add-category d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                        <i class="fas fa-plus"></i>
                                        Add Category
                                    </button>
                                    <button type="button" id="editCategoryBtn" class="btn btn-edit-category d-flex align-items-center gap-2" disabled>
                                        <i class="fas fa-edit"></i>
                                        Edit Category
                                    </button>
                                    <button type="button" id="deleteBtn" class="btn btn-delete-category d-flex align-items-center gap-2" disabled>
                                        <i class="fas fa-trash"></i>
                                        Delete Selected
                                    </button>
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
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
