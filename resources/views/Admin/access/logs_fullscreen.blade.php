@extends('layouts.app')
@section('title', 'Access Logs')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
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
</head>
<body class="bg-light">

    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
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
        </main>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
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
@endsection
