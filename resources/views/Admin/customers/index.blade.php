<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customers - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
            --green:#10b981;--red:#ef4444;--amber:#f59e0b;
            --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
            --text:#1a2744;--muted:#6b84aa;
        }

        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

        .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
        .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
        .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
        .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
        .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
        @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
        @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

        .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;}

        .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
        .sp-ph-left{display:flex;align-items:center;gap:13px;}
        .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
        .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
        .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
        .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

        .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
        .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
        .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
        .sp-btn-good{background:linear-gradient(135deg,#059669,#10b981);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,0.24);}
        .sp-btn-good:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(16,185,129,0.34);color:#fff;}

        .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .55s ease both;}
        .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
        .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
        .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
        .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
        .sp-card-head-title i{color:rgba(0,229,255,.85);}
        .sp-card-body{padding:18px 22px;}

        .sp-table-wrap{overflow-x:auto;}
        .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
        .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
        .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
        .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
        .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}

        .sp-table-scroll{max-height:520px;overflow-y:auto;overflow-x:hidden;}

        .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.02em;font-family:'Nunito',sans-serif;}
        .sp-badge-warn{background:rgba(245,158,11,0.12);color:#b45309;border:1px solid rgba(245,158,11,0.22);}
        .sp-badge-good{background:rgba(16,185,129,0.12);color:#047857;border:1px solid rgba(16,185,129,0.22);}
        .sp-badge-bad{background:rgba(239,68,68,0.12);color:#b91c1c;border:1px solid rgba(239,68,68,0.22);}

        .sp-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .sp-mini-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:7px 10px;border-radius:10px;border:1.5px solid var(--border);background:#fff;color:var(--navy);font-size:12px;font-weight:800;font-family:'Nunito',sans-serif;text-decoration:none;transition:all .15s ease;}
        .sp-mini-btn:hover{transform:translateY(-1px);box-shadow:0 8px 16px rgba(13,71,161,0.10);}
        .sp-mini-primary{border-color:rgba(25,118,210,0.22);}
        .sp-mini-danger{border-color:rgba(239,68,68,0.25);color:#b91c1c;}
        .sp-mini-success{border-color:rgba(16,185,129,0.25);color:#047857;}

        .pagination{margin-bottom:0;}
        .page-link{border-radius:10px !important;border:1.5px solid var(--border) !important;color:var(--navy) !important;}
        .page-item.active .page-link{background:linear-gradient(135deg,var(--navy),var(--blue)) !important;border-color:transparent !important;color:#fff !important;}

        .sp-swal.swal2-popup{border-radius:18px !important;border:1px solid var(--border) !important;box-shadow:0 22px 50px rgba(13,71,161,0.18) !important;padding:0 !important;overflow:hidden;}
        .sp-swal .swal2-title{margin:0 !important;padding:16px 20px !important;text-align:left !important;font-family:'Nunito',sans-serif !important;font-size:15px !important;font-weight:900 !important;color:#fff !important;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%) !important;}
        .sp-swal .swal2-html-container{margin:0 !important;padding:18px 20px 8px !important;text-align:left !important;color:var(--text) !important;font-family:'Plus Jakarta Sans',sans-serif !important;}
        .sp-swal .swal2-actions{margin:0 !important;padding:14px 20px 18px !important;gap:10px !important;justify-content:flex-end !important;background:rgba(13,71,161,0.02) !important;border-top:1px solid var(--border) !important;}
        .sp-swal .swal2-validation-message{margin:0 !important;border-radius:0 !important;background:rgba(239,68,68,0.08) !important;color:#b91c1c !important;font-family:'Plus Jakarta Sans',sans-serif !important;}

        .sp-swal .sp-label{display:block;font-size:11.5px;font-weight:800;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:7px;font-family:'Nunito',sans-serif;}
        .sp-swal .sp-input,.sp-swal .sp-select,.sp-swal .sp-textarea{width:100%;border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;}
        .sp-swal .sp-input:focus,.sp-swal .sp-select:focus,.sp-swal .sp-textarea:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}
        .sp-swal .sp-textarea{resize:vertical;min-height:92px;}

        .sp-swal-confirm{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:11px;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:#fff;border:none;background:linear-gradient(135deg,var(--navy),var(--blue));box-shadow:0 4px 14px rgba(13,71,161,0.26);transition:all .2s ease;}
        .sp-swal-confirm:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);}
        .sp-swal-cancel{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:11px;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:var(--navy);border:1.5px solid var(--border);background:var(--card);transition:all .2s ease;}
        .sp-swal-cancel:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
</head>
<body>

    <div class="d-flex min-vh-100">
        <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="sp-wrap">

                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Admin</div>
                            <div class="sp-ph-title">Customers</div>
                            <div class="sp-ph-sub">Manage customers and account status</div>
                        </div>
                    </div>
                    <div class="sp-actions">
                        <button class="sp-btn sp-btn-good" onclick="addNewCustomer()"><i class="fas fa-plus"></i> Add Customer</button>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-list"></i> Customer List</div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-table-wrap">
                            <div class="sp-table-scroll">
                                <table class="sp-table">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Created At</th>
                                        <th>Created By</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->customer_id ?? $customer->id }}</td>
                                            <td>
                                                <strong>{{ $customer->full_name }}</strong>
                                                @if(($customer->outstanding_balance ?? 0) > 0)
                                                    <span class="sp-badge sp-badge-warn ms-1"><i class="fas fa-exclamation-circle"></i> Has Balance</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</td>
                                            <td>{{ $customer->user->name ?? $customer->created_by ?? 'System' }}</td>
                                            <td>Main Branch</td>
                                            <td>
                                                @if($customer->status == 'active')
                                                    <span class="sp-badge sp-badge-good"><i class="fas fa-check-circle"></i> Active</span>
                                                @else
                                                    <span class="sp-badge sp-badge-bad"><i class="fas fa-ban"></i> {{ ucfirst($customer->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="sp-actions">
                                                    <a href="{{ route('admin.customers.show', $customer->customer_id ?? $customer->id) }}" class="sp-mini-btn sp-mini-primary" title="View Customer" style="text-decoration: none;">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($customer->status == 'active')
                                                        <button type="button" class="sp-mini-btn sp-mini-danger" onclick="toggleCustomerStatus({{ $customer->customer_id ?? $customer->id }}, '{{ $customer->status }}')" title="Block Customer">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="sp-mini-btn sp-mini-success" onclick="toggleCustomerStatus({{ $customer->customer_id ?? $customer->id }}, '{{ $customer->status }}')" title="Unblock Customer">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No customers found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                        
                        @if($customers->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $customers->links() }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Add event listeners for single selection
document.addEventListener('DOMContentLoaded', function() {
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox, .walk-in-checkbox');
    
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck all other checkboxes
                customerCheckboxes.forEach(otherCheckbox => {
                    if (otherCheckbox !== this) {
                        otherCheckbox.checked = false;
                    }
                });
            }
        });
    });
});

function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function addNewCustomer() {
    Swal.fire({
        title: 'Add New Customer',
        customClass: {
            popup: 'sp-swal',
            confirmButton: 'sp-swal-confirm',
            cancelButton: 'sp-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="sp-label">Full Name *</label>
                            <input type="text" class="sp-input" id="new_full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Phone</label>
                            <input type="text" class="sp-input" id="new_phone" placeholder="Enter phone number">
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Email</label>
                            <input type="email" class="sp-input" id="new_email" placeholder="Enter email address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="sp-label">Address</label>
                            <textarea class="sp-textarea" id="new_address" rows="3" placeholder="Enter address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Max Credit Limit</label>
                            <input type="number" class="sp-input" id="new_max_credit_limit" step="0.01" min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Account Status</label>
                            <select class="sp-select" id="new_status">
                                <option value="active">Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-user-plus"></i> Add Customer',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const fullName = document.getElementById('new_full_name').value;
            if (!fullName.trim()) {
                Swal.showValidationMessage('Full name is required');
                return false;
            }
            
            return {
                full_name: fullName,
                phone: document.getElementById('new_phone').value,
                email: document.getElementById('new_email').value,
                address: document.getElementById('new_address').value,
                max_credit_limit: parseFloat(document.getElementById('new_max_credit_limit').value) || 0,
                status: document.getElementById('new_status').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to create the customer
            const customerData = result.value;
        
            fetch('{{ route("admin.customers.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(customerData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Added',
                        text: 'New customer has been added successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Add Failed',
                        text: data.message || 'Failed to add customer.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding the customer.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function viewCustomerDetails(customerId) {
    // This would typically open a modal or navigate to a detail page
    Swal.fire({
        title: 'Customer Details',
        text: 'Detailed customer view coming soon!',
        icon: 'info',
        confirmButtonColor: '#2563eb'
    });
}

function toggleCustomerStatus(customerId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'blocked' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'block';
    
    Swal.fire({
        title: `Confirm ${action} customer?`,
        text: `Are you sure you want to ${action} this customer?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/superadmin/admin/customers/${customerId}/toggle-status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: `Customer has been ${action}d successfully.`,
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update customer status.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating customer status.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

</script>
