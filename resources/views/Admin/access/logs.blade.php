@extends('layouts.app')

@include('layouts.theme-base')

@push('stylesDashboard')
<style>
    /* Access Logs Table - Compact Row Heights */
    .log-row {
        transition: all 0.2s ease;
    }
    
    .log-row:hover {
        background: rgba(0, 229, 255, 0.05) !important;
        transform: scale(1.005);
    }
    
    /* Smaller avatar for compact rows */
    .user-avatar-small {
        width: 28px !important;
        height: 28px !important;
        object-fit: cover;
        flex-shrink: 0;
        font-size: 10px !important;
    }
    
    /* Compact user info layout */
    .user-name {
        font-weight: 600;
        color: var(--electric-blue);
        font-size: 0.875rem;
        line-height: 1.2;
    }
    
    .user-email {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.2;
    }
    
    /* Compact login time layout */
    .login-time {
        padding: 0.25rem 0;
    }
    
    .login-date {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-primary);
        line-height: 1.2;
        margin-bottom: 2px;
    }
    
    .last-active {
        font-size: 0.7rem;
        line-height: 1.1;
        margin: 0;
    }
    
    /* Reduce table row padding */
    .log-row td {
        padding: 0.5rem 0.75rem !important;
        vertical-align: middle;
    }
    
    /* Compact badges */
    .log-row .badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    /* Compact buttons */
    .log-row .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .user-avatar-small {
            width: 24px !important;
            height: 24px !important;
            font-size: 9px !important;
        }
        
        .user-name {
            font-size: 0.8rem;
        }
        
        .user-email {
            font-size: 0.75rem;
        }
        
        .login-date {
            font-size: 0.75rem;
        }
        
        .last-active {
            font-size: 0.65rem;
        }
        
        .log-row td {
            padding: 0.4rem 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-base">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="m-0">Access Logs</h3>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshLogs()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-base">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Last Login</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="log-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(!empty($user->profile_picture))
                                                    <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->name }}" 
                                                         class="rounded-circle me-2 user-avatar-small">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 user-avatar-small">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="user-name">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="user-email">{{ $user->email }}</td>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{ $user->userType->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="login-time">
                                            @if($user->updated_at)
                                                <div class="login-date">{{ $user->updated_at->format('M d, Y H:i:s') }}</div>
                                                <small class="text-muted last-active">
                                                    Last active: {{ $user->updated_at->diffForHumans() }}
                                                </small>
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->updated_at && $user->updated_at->gt(now()->subMinutes(30)))
                                                <span class="badge badge-success">Recently Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        onclick="viewUserDetails({{ $user->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                                        onclick="resetUserPassword({{ $user->id }})">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
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
@endsection

@push('scripts')
<script>
function refreshLogs() {
    location.reload();
}

function viewUserDetails(userId) {
    // You can implement AJAX call to get detailed user information
    alert('User details feature coming soon for user ID: ' + userId);
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
@endpush
