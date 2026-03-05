@extends('layouts.app')
@section('title', 'Access Logs')

@section('content')
<style>
.table th {
    font-weight: 600;
    color: #475569;
    background-color: #f8fafc;
}
.user-avatar {
    width: 32px;
    height: 32px;
    object-fit: cover;
}
.badge {
    font-size: 0.75rem;
}
</style>

<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="card card-rounded shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="m-0">Access Logs</h4>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Last Active</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(!empty($user->profile_picture))
                                                <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->name }}" 
                                                     class="rounded-circle me-2 user-avatar">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 user-avatar" 
                                                     style="font-size: 12px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span>{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $user->userType->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->updated_at)
                                            {{ $user->updated_at->format('M d, Y H:i:s') }}
                                            <small class="text-muted d-block">
                                                Last active: {{ $user->updated_at->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->updated_at && $user->updated_at->gt(now()->subMinutes(30)))
                                            <span class="badge bg-success">Recently Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="viewUserDetails({{ $user->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="resetUserPassword({{ $user->id }})">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                                            <p>No access logs found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                        </div>
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userDetailsContent">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshLogs() {
    location.reload();
}

function viewUserDetails(userId) {
    const content = document.getElementById('userDetailsContent');
    if (!content) return;

    content.innerHTML = '<div class="text-muted">Loading...</div>';

    fetch(`/admin/access/users/${userId}/activity`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async (res) => {
        if (!res.ok) {
            const text = await res.text();
            throw new Error(text || `Request failed with status ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to load activity');
        }

        const user = data.user || {};
        const sales = data.sales || {};
        const purchases = data.purchases || {};
        const logs = Array.isArray(data.logs) ? data.logs : [];

        const formatMoney = (n) => {
            const num = Number(n || 0);
            return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const safe = (v) => (v === null || typeof v === 'undefined') ? '' : String(v);

        let html = '';
        html += `<div class="mb-3">`;
        html += `  <div class="fw-bold" style="font-size:1.05rem">${user.name || ''}</div>`;
        html += `  <div class="text-muted">${user.email || ''}</div>`;
        html += user.role ? `  <div class="mt-1"><span class="badge bg-info">${user.role}</span></div>` : '';
        if (user.employee_id || user.branch_id) {
            html += `  <div class="mt-2 text-muted" style="font-size:0.9rem">`;
            html += user.employee_id ? `Employee ID: <span class="fw-semibold">${safe(user.employee_id)}</span>` : '';
            html += (user.employee_id && user.branch_id) ? ' &nbsp;|&nbsp; ' : '';
            html += user.branch_id ? `Branch ID: <span class="fw-semibold">${safe(user.branch_id)}</span>` : '';
            html += `  </div>`;
        }
        html += `</div>`;

        html += `<div class="row g-3 mb-3">`;
        html += `  <div class="col-12 col-md-6">`;
        html += `    <div class="border rounded p-3">`;
        html += `      <div class="fw-bold mb-2">Sales</div>`;
        html += `      <div class="d-flex justify-content-between"><span class="text-muted">Count</span><span class="fw-semibold">${safe(sales.count || 0)}</span></div>`;
        html += `      <div class="d-flex justify-content-between"><span class="text-muted">Total</span><span class="fw-semibold">${formatMoney(sales.total_amount || 0)}</span></div>`;
        html += `    </div>`;
        html += `  </div>`;
        html += `  <div class="col-12 col-md-6">`;
        html += `    <div class="border rounded p-3">`;
        html += `      <div class="fw-bold mb-2">Purchases</div>`;
        html += `      <div class="d-flex justify-content-between"><span class="text-muted">Count</span><span class="fw-semibold">${safe(purchases.count || 0)}</span></div>`;
        html += `      <div class="d-flex justify-content-between"><span class="text-muted">Total</span><span class="fw-semibold">${formatMoney(purchases.total_cost || 0)}</span></div>`;
        html += `    </div>`;
        html += `  </div>`;
        html += `</div>`;

        const recentSales = Array.isArray(sales.recent) ? sales.recent : [];
        if (recentSales.length > 0) {
            html += `<div class="mb-3">`;
            html += `  <div class="fw-bold mb-2">Recent Sales</div>`;
            html += `  <div class="table-responsive">`;
            html += `    <table class="table table-sm table-bordered mb-0">`;
            html += `      <thead><tr><th>Date</th><th>Reference</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>`;
            html += `      <tbody>`;
            recentSales.forEach(s => {
                const dt = s.created_at ? new Date(s.created_at).toLocaleString() : '';
                html += `        <tr>`;
                html += `          <td style="white-space:nowrap">${dt}</td>`;
                html += `          <td>${safe(s.reference_number)}</td>`;
                html += `          <td style="white-space:nowrap">${formatMoney(s.total_amount)}</td>`;
                html += `          <td>${safe(s.payment_method)}</td>`;
                html += `          <td>${safe(s.status)}</td>`;
                html += `        </tr>`;
            });
            html += `      </tbody>`;
            html += `    </table>`;
            html += `  </div>`;
            html += `</div>`;
        }

        const recentPurchases = Array.isArray(purchases.recent) ? purchases.recent : [];
        if (recentPurchases.length > 0) {
            html += `<div class="mb-3">`;
            html += `  <div class="fw-bold mb-2">Recent Purchases</div>`;
            html += `  <div class="table-responsive">`;
            html += `    <table class="table table-sm table-bordered mb-0">`;
            html += `      <thead><tr><th>Date</th><th>Reference</th><th>Total Cost</th><th>Payment</th></tr></thead>`;
            html += `      <tbody>`;
            recentPurchases.forEach(p => {
                const dt = p.purchase_date ? new Date(p.purchase_date).toLocaleDateString() : (p.created_at ? new Date(p.created_at).toLocaleString() : '');
                html += `        <tr>`;
                html += `          <td style="white-space:nowrap">${dt}</td>`;
                html += `          <td>${safe(p.reference_number)}</td>`;
                html += `          <td style="white-space:nowrap">${formatMoney(p.total_cost)}</td>`;
                html += `          <td>${safe(p.payment_status)}</td>`;
                html += `        </tr>`;
            });
            html += `      </tbody>`;
            html += `    </table>`;
            html += `  </div>`;
            html += `</div>`;
        }

        html += `<div class="fw-bold mb-2">Activity Logs</div>`;

        if (logs.length === 0) {
            html += '<div class="text-muted">No activity recorded yet.</div>';
            content.innerHTML = html;
            return;
        }

        const toTitle = (s) => {
            const str = safe(s).trim();
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        };

        const formatAccessedPage = (path, routeName) => {
            const rawPath = safe(path).split('?')[0].trim();
            if (rawPath) {
                const parts = rawPath.split('/').filter(Boolean);
                if (parts.length) {
                    const ignoreLast = new Set(['store', 'lookup']);
                    const last = String(parts[parts.length - 1] || '').toLowerCase();
                    if (parts.length > 1 && ignoreLast.has(last)) {
                        parts.pop();
                    }
                    return parts
                        .map(p => p.replace(/[-_]/g, ' '))
                        .map(toTitle)
                        .join(' > ');
                }
            }

            const rawRoute = safe(routeName).trim();
            if (rawRoute) {
                const routeParts = rawRoute
                    .split('.')
                    .filter(Boolean);

                const ignoreLast = new Set(['store', 'lookup']);
                const last = String(routeParts[routeParts.length - 1] || '').toLowerCase();
                if (routeParts.length > 1 && ignoreLast.has(last)) {
                    routeParts.pop();
                }

                return routeParts
                    .map(p => p.replace(/[-_]/g, ' '))
                    .map(toTitle)
                    .join(' > ');
            }

            return '';
        };

        html += '<div class="table-responsive">';
        html += '<table class="table table-sm table-bordered">';
        html += '<thead><tr><th>Date</th><th>Page</th><th>Path</th><th>IP</th></tr></thead>';
        html += '<tbody>';
        logs.forEach(l => {
            const dt = l.created_at ? new Date(l.created_at).toLocaleString() : '';
            const route = l.route_name || '';
            const path = l.path || '';
            const ip = l.ip_address || '';
            html += `<tr>`;
            html += `  <td style="white-space:nowrap">${dt}</td>`;
            html += `  <td>${formatAccessedPage(path, route)}</td>`;
            html += `  <td>${path}</td>`;
            html += `  <td style="white-space:nowrap">${ip}</td>`;
            html += `</tr>`;
        });
        html += '</tbody></table></div>';

        content.innerHTML = html;
    })
    .catch(err => {
        content.innerHTML = `<div class="text-danger">${(err && err.message) ? err.message : 'Failed to load activity.'}</div>`;
    })
    .finally(() => {
        const modalEl = document.getElementById('userDetailsModal');
        if (modalEl && window.bootstrap) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });
}

function resetUserPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password?')) {
        // You can implement AJAX call to reset password
        alert('Password reset feature coming soon for user ID: ' + userId);
    }
}

// Auto-refresh logs every 5 minutes
setInterval(function() {
    console.log('Auto-refreshing access logs...');
    // You can implement AJAX refresh here
}, 300000);
</script>
@endsection
