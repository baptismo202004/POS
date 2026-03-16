@extends('layouts.app')
@section('title', 'Access Logs')

@section('content')
<style>
    :root{
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
        --green:#10b981;--red:#ef4444;--amber:#f59e0b;
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
    .sp-btn-ghost{background:rgba(13,71,161,0.04);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-ghost:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
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
    .bg-info{background:rgba(6,182,212,0.12) !important;color:#0369a1 !important;border-color:rgba(6,182,212,0.22) !important;}
    .bg-success{background:rgba(16,185,129,0.12) !important;color:#047857 !important;border-color:rgba(16,185,129,0.22) !important;}
    .bg-secondary{background:rgba(148,163,184,0.16) !important;color:#334155 !important;border-color:rgba(148,163,184,0.24) !important;}
    .bg-primary{background:rgba(25,118,210,0.10) !important;color:var(--navy) !important;border-color:rgba(25,118,210,0.18) !important;}

    .user-avatar{width:32px;height:32px;object-fit:cover;}

    .pagination{margin-bottom:0;}
    .page-link{border-radius:10px !important;border:1.5px solid var(--border) !important;color:var(--navy) !important;}
    .page-item.active .page-link{background:linear-gradient(135deg,var(--navy),var(--blue)) !important;border-color:transparent !important;color:#fff !important;}

    .sp-ud-modal .modal-content{border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 18px 60px rgba(13,71,161,0.28);}
    .sp-ud-modal .modal-header{background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);border-bottom:none;position:relative;overflow:hidden;padding:16px 18px;}
    .sp-ud-modal .modal-header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-ud-modal .modal-title{color:#fff;font-weight:900;letter-spacing:.01em;position:relative;z-index:1;display:flex;align-items:center;gap:10px;}
    .sp-ud-modal .modal-title i{color:rgba(0,229,255,.85);}
    .sp-ud-modal .btn-close{filter:invert(1);opacity:.85;position:relative;z-index:1;}
    .sp-ud-modal .modal-body{background:#fff;padding:16px 18px;}

    .sp-ud-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px;}
    .sp-ud-name{font-weight:900;color:var(--navy);font-size:16px;line-height:1.2;}
    .sp-ud-email{color:var(--muted);font-size:12.5px;margin-top:2px;}
    .sp-ud-meta{margin-top:8px;color:var(--muted);font-size:12px;}
    .sp-ud-meta .sp-ud-k{font-weight:900;color:var(--navy);}
    .sp-ud-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:14px;}
    @media (max-width: 575.98px){.sp-ud-grid{grid-template-columns:1fr;}}
    .sp-ud-stat{border:1px solid var(--border);border-radius:16px;padding:12px 14px;background:linear-gradient(180deg,#ffffff 0%, #f7fbff 100%);}
    .sp-ud-stat-title{font-weight:900;color:var(--navy);margin-bottom:8px;display:flex;align-items:center;gap:8px;}
    .sp-ud-stat-title i{color:rgba(13,71,161,0.75);}
    .sp-ud-row{display:flex;align-items:center;justify-content:space-between;font-size:12.5px;margin-top:5px;}
    .sp-ud-row .sp-ud-l{color:var(--muted);}
    .sp-ud-row .sp-ud-v{color:var(--navy);font-weight:900;}

    .sp-ud-section{margin-top:14px;}
    .sp-ud-section-title{font-weight:900;color:var(--navy);font-size:13px;margin:0 0 10px;}
    .sp-ud-table{width:100%;border-collapse:separate;border-spacing:0;}
    .sp-ud-table thead th{background:rgba(13,71,161,0.03);padding:10px 12px;font-size:10.5px;font-weight:900;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-ud-table tbody td{padding:10px 12px;font-size:12.5px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-ud-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-ud-table tbody tr:hover td{background:rgba(21,101,192,0.05);}
</style>

<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Admin</div>
                        <div class="sp-ph-title">Access Logs</div>
                        <div class="sp-ph-sub">Monitor recent activity and user access</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.access.index') }}" class="sp-btn sp-btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
                    <button type="button" class="sp-btn sp-btn-primary" onclick="refreshLogs()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Access Logs</div>
                    <div class="d-flex gap-2" style="position:relative;z-index:1;"></div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-table-wrap">
                    <table class="sp-table" width="100%" cellspacing="0">
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
        <div class="modal-content sp-ud-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user"></i> User Details</h5>
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
        html += `<div class="sp-ud-head">`;
        html += `  <div>`;
        html += `    <div class="sp-ud-name">${user.name || ''}</div>`;
        html += `    <div class="sp-ud-email">${user.email || ''}</div>`;
        html += user.role ? `    <div class="mt-2"><span class="badge bg-info">${user.role}</span></div>` : '';
        if (user.employee_id || user.branch_id) {
            html += `    <div class="sp-ud-meta">`;
            html += user.employee_id ? `<span class="sp-ud-k">Employee ID:</span> <span class="fw-semibold">${safe(user.employee_id)}</span>` : '';
            html += (user.employee_id && user.branch_id) ? ' &nbsp;|&nbsp; ' : '';
            html += user.branch_id ? `<span class="sp-ud-k">Branch ID:</span> <span class="fw-semibold">${safe(user.branch_id)}</span>` : '';
            html += `    </div>`;
        }
        html += `  </div>`;
        html += `</div>`;

        html += `<div class="sp-ud-grid">`;
        html += `  <div class="sp-ud-stat">`;
        html += `    <div class="sp-ud-stat-title"><i class="fas fa-receipt"></i> Sales</div>`;
        html += `    <div class="sp-ud-row"><span class="sp-ud-l">Count</span><span class="sp-ud-v">${safe(sales.count || 0)}</span></div>`;
        html += `    <div class="sp-ud-row"><span class="sp-ud-l">Total</span><span class="sp-ud-v">${formatMoney(sales.total_amount || 0)}</span></div>`;
        html += `  </div>`;
        html += `  <div class="sp-ud-stat">`;
        html += `    <div class="sp-ud-stat-title"><i class="fas fa-truck"></i> Purchases</div>`;
        html += `    <div class="sp-ud-row"><span class="sp-ud-l">Count</span><span class="sp-ud-v">${safe(purchases.count || 0)}</span></div>`;
        html += `    <div class="sp-ud-row"><span class="sp-ud-l">Total</span><span class="sp-ud-v">${formatMoney(purchases.total_cost || 0)}</span></div>`;
        html += `  </div>`;
        html += `</div>`;

        const recentSales = Array.isArray(sales.recent) ? sales.recent : [];
        if (recentSales.length > 0) {
            html += `<div class="sp-ud-section">`;
            html += `  <div class="sp-ud-section-title">Recent Sales</div>`;
            html += `  <div class="table-responsive">`;
            html += `    <table class="sp-ud-table">`;
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
            html += `<div class="sp-ud-section">`;
            html += `  <div class="sp-ud-section-title">Recent Purchases</div>`;
            html += `  <div class="table-responsive">`;
            html += `    <table class="sp-ud-table">`;
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

        html += `<div class="sp-ud-section">`;
        html += `  <div class="sp-ud-section-title">Activity Logs</div>`;

        if (logs.length === 0) {
            html += '<div class="text-muted">No activity recorded yet.</div>';
            html += `</div>`;
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
        html += '<table class="sp-ud-table">';
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
        html += `</div>`;

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
