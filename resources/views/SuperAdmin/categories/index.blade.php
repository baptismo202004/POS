@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0">Categories</h4>
            <div>
                <button type="button" id="editBtn" class="btn btn-warning" disabled><i class="fas fa-edit"></i> Edit</button>
                <button type="button" id="deleteBtn" class="btn btn-danger" disabled><i class="fas fa-trash"></i> Delete</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openCategoryModal()">
                    <i class="fas fa-plus"></i> Add Category
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
                    @csrf
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
            form.action = '{{ route("superadmin.categories.bulkUpdate") }}';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
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
                if (confirm('Are you sure you want to delete the selected categories?')) {
                    const ids = Array.from(selectedCheckboxes).map(cb => cb.closest('tr').dataset.id);
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("superadmin.categories.bulkDestroy") }}';
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
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
                }
            }
        });

        updateButtonStates();
    });

    function openCategoryModal() {
        document.getElementById('categoryForm').action = '{{ route("superadmin.categories.store") }}';
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
@endsection
