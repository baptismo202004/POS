<?php $__env->startSection('title', 'Categories'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.card-base .card-header {
    background: #12dcc4 !important;
    background: linear-gradient(135deg, #12dcc4 0%, #0ea5e9 100%) !important;
    border: none !important;
    padding: 1.5rem !important;
    box-shadow: 0 4px 6px rgba(18, 220, 148, 0.3);
}

.page-header h3 {
    color: white;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #ffffff !important;
    margin-bottom: 0;
    font-size: 0.9rem;
    font-weight: 400;
}

.btn-outline-primary {
    background-color: #007bff;
    color: white;
    border: 1px solid #007bff;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.btn-outline-danger {
    background-color: #dc3545;
    color: white;
    border: 1px solid #dc3545;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.btn-outline-danger:hover {
    background-color: #c82333;
    border-color: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    font-weight: 500;
    padding: 0.5rem 1.2rem;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
    border: none;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.badge {
    padding: 0.4rem 0.8rem;
    font-weight: 500;
    border-radius: 20px;
    font-size: 0.8rem;
}

.bg-success {
    background-color: #28a745;
}

.bg-secondary {
    background-color: #6c757d;
}

.row-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Modal Improvements */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px 12px 0 0;
    padding: 1.5rem;
}

.modal-title {
    color: white;
    font-weight: 600;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    border: none;
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.theme-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card-base">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="page-header">
                <h3 class="m-0">Categories</h3>
                <p class="text-white mb-0">Manage product categories and classifications</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="editBtn" class="btn btn-primary" disabled>
                    <i class="fas fa-edit me-2"></i> Edit
                </button>
                <button type="button" id="deleteBtn" class="btn btn-danger" disabled>
                    <i class="fas fa-trash me-2"></i> Delete
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openCategoryModal()">
                    <i class="fas fa-plus me-2"></i> Add Category
                </button>
            </div>
        
                     
                    </div>
                    <div class="card-body">
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
                                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr data-id="<?php echo e($category->id); ?>" data-name="<?php echo e($category->category_name); ?>" data-status="<?php echo e($category->status); ?>">
                                            <td><input type="checkbox" class="row-checkbox"></td>
                                            <td><?php echo e($category->id); ?></td>
                                            <td><?php echo e($category->category_name); ?></td>
                                            <td>
                                                <span class="badge <?php echo e($category->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                                    <?php echo e(ucfirst($category->status)); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No categories found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Modal -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkEditModalLabel">Bulk Edit Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_status" class="form-label">Status</label>
                        <select class="form-select" id="bulk_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="bulkUpdateBtn" class="btn btn-primary">Update Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const editBtn = document.getElementById('editBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');

        function updateButtonStates() {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            editBtn.disabled = selectedCheckboxes.length === 0;
            deleteBtn.disabled = selectedCheckboxes.length === 0;
        }

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                updateButtonStates();
            });
        });

        editBtn.addEventListener('click', function () {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            if (selectedCheckboxes.length === 1) {
                const selectedRow = selectedCheckboxes[0].closest('tr');
                const id = selectedRow.dataset.id;
                const name = selectedRow.dataset.name;
                const status = selectedRow.dataset.status;
                editCategory(id, name, status);
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            } else if (selectedCheckboxes.length > 1) {
                new bootstrap.Modal(document.getElementById('bulkEditModal')).show();
            }
        });

        bulkUpdateBtn.addEventListener('click', function () {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            const status = document.getElementById('bulk_status').value;
            const ids = Array.from(selectedCheckboxes).map(cb => cb.closest('tr').dataset.id);

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("superadmin.categories.bulkUpdate")); ?>';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfInput);

            ids.forEach(id => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'ids[]';
                idInput.value = id;
                form.appendChild(idInput);
            });

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        });

        deleteBtn.addEventListener('click', function () {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            if (selectedCheckboxes.length > 0) {
                const categoryCount = selectedCheckboxes.length;
                const message = categoryCount === 1 
                    ? 'Are you sure you want to delete this category?' 
                    : `Are you sure you want to delete the selected ${categoryCount} categories?`;
                
                Swal.fire({
                    title: 'Confirm Delete',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const ids = Array.from(selectedCheckboxes).map(cb => cb.closest('tr').dataset.id);
                        
                        // Show loading SweetAlert
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the selected categories.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form via AJAX for better UX
                        const formData = new FormData();
                        formData.append('_method', 'DELETE');
                        formData.append('_token', '<?php echo e(csrf_token()); ?>');
                        
                        ids.forEach(id => {
                            formData.append('ids[]', id);
                        });
                        
                        fetch('<?php echo e(route("superadmin.categories.bulkDestroy")); ?>', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers);
                            
                            if (!response.ok) {
                                if (response.status === 403) {
                                    throw new Error('Permission denied. You may not have the required permissions to delete categories.');
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success) {
                                // Success notification
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Categories deleted successfully!',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    position: 'top-end',
                                    toast: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                // Error notification
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Something went wrong. Please try again.',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Fallback to form submission if AJAX fails
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '<?php echo e(route("superadmin.categories.bulkDestroy")); ?>';
                            
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';
                            form.appendChild(methodInput);
                            
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = '<?php echo e(csrf_token()); ?>';
                            form.appendChild(csrfInput);
                            
                            ids.forEach(id => {
                                const idInput = document.createElement('input');
                                idInput.type = 'hidden';
                                idInput.name = 'ids[]';
                                idInput.value = id;
                                form.appendChild(idInput);
                            });
                            
                            document.body.appendChild(form);
                            form.submit();
                        });
                    }
                });
            }
        });

        updateButtonStates();
    });

    // Handle category form submission with AJAX and SweetAlert
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
        
        // Show loading SweetAlert
        Swal.fire({
            title: 'Processing...',
            html: 'Please wait while we save the category.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: isEdit ? 'Category updated successfully!' : 'Category added successfully!',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                }).then(() => {
                    // Close modal and reload page
                    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                    location.reload();
                });
            } else {
                // Error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Something went wrong. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Network error. Please try again.',
                confirmButtonColor: '#dc3545'
            });
        });
    });

    function openCategoryModal() {
        document.getElementById('categoryForm').action = '<?php echo e(route("superadmin.categories.store")); ?>';
        document.getElementById('categoryForm').querySelector('input[name="_method"]')?.remove();
        document.getElementById('categoryModalLabel').textContent = 'Add Category';
        document.getElementById('category_name').value = '';
        document.getElementById('status').value = 'active';
    }

    function editCategory(id, name, status) {
        document.getElementById('categoryForm').action = '/superadmin/categories/' + id;
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/SuperAdmin/categories/index.blade.php ENDPATH**/ ?>