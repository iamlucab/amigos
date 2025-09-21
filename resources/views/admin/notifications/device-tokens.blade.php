@extends('adminlte::page')
@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Device Tokens Management</h1>
                    <p class="text-muted mb-0">Manage registered devices for push notifications</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.notifications.push') }}" class="btn btn-success">
                        <i class="bi bi-bell"></i> Send Push Notification
                    </a>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-phone fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary">{{ number_format($stats['total_devices']) }}</h4>
                    <small class="text-muted">Total Devices</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="text-success">{{ number_format($stats['active_devices']) }}</h4>
                    <small class="text-muted">Active Devices</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-android fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning">{{ number_format($stats['android_devices']) }}</h4>
                    <small class="text-muted">Android Devices</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-apple fa-2x text-info mb-2"></i>
                    <h4 class="text-info">{{ number_format($stats['ios_devices']) }}</h4>
                    <small class="text-muted">iOS Devices</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title text-white mb-0">
                                <i class="bi bi-list me-2"></i>Registered Device Tokens
                            </h5>
                        </div>
                        <div class="col-auto">
                            <!-- Search and Filters -->
                            <div class="d-flex gap-2">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" class="form-control" id="searchInput" 
                                           placeholder="Search users or devices..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="performSearch()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-funnel me-1"></i>Filter
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="?platform=all">All Platforms</a></li>
                                        <li><a class="dropdown-item" href="?platform=android">Android Only</a></li>
                                        <li><a class="dropdown-item" href="?platform=ios">iOS Only</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="?status=all">All Status</a></li>
                                        <li><a class="dropdown-item" href="?status=active">Active Only</a></li>
                                        <li><a class="dropdown-item" href="?status=inactive">Inactive Only</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="?sort=newest">Newest First</a></li>
                                        <li><a class="dropdown-item" href="?sort=oldest">Oldest First</a></li>
                                        <li><a class="dropdown-item" href="?sort=user">By User Name</a></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="cleanupInactiveTokens()" title="Cleanup Inactive Tokens">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($deviceTokens->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>User</th>
                                        <th>Device Info</th>
                                        <th>Platform</th>
                                        <th>Token Preview</th>
                                        <th>Status</th>
                                        <th>Last Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deviceTokens as $token)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input token-checkbox" 
                                                       value="{{ $token->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        @if($token->user->avatar)
                                                            <img src="{{ $token->user->avatar }}" 
                                                                 class="rounded-circle" width="32" height="32">
                                                        @else
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                 style="width: 32px; height: 32px; font-size: 14px;">
                                                                {{ strtoupper(substr($token->user->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $token->user->name }}</strong>
                                                        <br><small class="text-muted">{{ $token->user->email }}</small>
                                                        @if($token->user->mobile_number)
                                                            <br><small class="text-muted">{{ $token->user->mobile_number }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    @if($token->device_name)
                                                        <strong>{{ $token->device_name }}</strong><br>
                                                    @endif
                                                    @if($token->device_model)
                                                        <small class="text-muted">{{ $token->device_model }}</small><br>
                                                    @endif
                                                    @if($token->app_version)
                                                        <small class="text-muted">App v{{ $token->app_version }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($token->platform === 'android')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-android me-1"></i>Android
                                                    </span>
                                                @elseif($token->platform === 'ios')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-apple me-1"></i>iOS
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-question-circle me-1"></i>{{ ucfirst($token->platform) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="token-preview">
                                                    <code class="small">{{ Str::limit($token->token, 20) }}...</code>
                                                    <button type="button" class="btn btn-link btn-sm p-0 ms-1" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#tokenModal{{ $token->id }}"
                                                            title="View Full Token">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                @if($token->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($token->last_used_at)
                                                    <div>{{ $token->last_used_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $token->last_used_at->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">Never used</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="testToken({{ $token->id }})"
                                                            title="Test Notification">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                    @if($token->is_active)
                                                        <button type="button" class="btn btn-outline-warning"
                                                                onclick="deactivateToken({{ $token->id }})"
                                                                title="Deactivate">
                                                            <i class="bi bi-pause"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-info"
                                                                onclick="activateToken({{ $token->id }})"
                                                                title="Activate">
                                                            <i class="bi bi-play"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteToken({{ $token->id }})"
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Token Modal -->
                                        <div class="modal fade" id="tokenModal{{ $token->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Device Token Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <h6>User Information</h6>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Name:</strong></td>
                                                                        <td>{{ $token->user->name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Email:</strong></td>
                                                                        <td>{{ $token->user->email }}</td>
                                                                    </tr>
                                                                    @if($token->user->mobile_number)
                                                                    <tr>
                                                                        <td><strong>Mobile:</strong></td>
                                                                        <td>{{ $token->user->mobile_number }}</td>
                                                                    </tr>
                                                                    @endif
                                                                    <tr>
                                                                        <td><strong>Role:</strong></td>
                                                                        <td>{{ $token->user->role ?? 'Member' }}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Device Information</h6>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Platform:</strong></td>
                                                                        <td>
                                                                            @if($token->platform === 'android')
                                                                                <i class="bi bi-android text-success"></i> Android
                                                                            @elseif($token->platform === 'ios')
                                                                                <i class="bi bi-apple text-info"></i> iOS
                                                                            @else
                                                                                {{ ucfirst($token->platform) }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @if($token->device_name)
                                                                    <tr>
                                                                        <td><strong>Device Name:</strong></td>
                                                                        <td>{{ $token->device_name }}</td>
                                                                    </tr>
                                                                    @endif
                                                                    @if($token->device_model)
                                                                    <tr>
                                                                        <td><strong>Model:</strong></td>
                                                                        <td>{{ $token->device_model }}</td>
                                                                    </tr>
                                                                    @endif
                                                                    @if($token->app_version)
                                                                    <tr>
                                                                        <td><strong>App Version:</strong></td>
                                                                        <td>{{ $token->app_version }}</td>
                                                                    </tr>
                                                                    @endif
                                                                    <tr>
                                                                        <td><strong>Status:</strong></td>
                                                                        <td>
                                                                            @if($token->is_active)
                                                                                <span class="badge bg-success">Active</span>
                                                                            @else
                                                                                <span class="badge bg-danger">Inactive</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Registered:</strong></td>
                                                                        <td>{{ $token->created_at->format('M d, Y h:i A') }}</td>
                                                                    </tr>
                                                                    @if($token->last_used_at)
                                                                    <tr>
                                                                        <td><strong>Last Used:</strong></td>
                                                                        <td>{{ $token->last_used_at->format('M d, Y h:i A') }}</td>
                                                                    </tr>
                                                                    @endif
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <h6>FCM Token</h6>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control font-monospace small" 
                                                                       value="{{ $token->token }}" readonly>
                                                                <button class="btn btn-outline-secondary" type="button" 
                                                                        onclick="copyToClipboard('{{ $token->token }}')">
                                                                    <i class="bi bi-clipboard"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-success" 
                                                                onclick="testToken({{ $token->id }})">
                                                            <i class="bi bi-send me-1"></i>Test Notification
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="d-flex align-items-center gap-2" id="bulkActions" style="display: none !important;">
                                        <span class="text-muted">Selected: <span id="selectedCount">0</span> tokens</span>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="testSelectedTokens()">
                                            <i class="bi bi-send me-1"></i>Test Selected
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="deactivateSelectedTokens()">
                                            <i class="bi bi-pause me-1"></i>Deactivate Selected
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteSelectedTokens()">
                                            <i class="bi bi-trash me-1"></i>Delete Selected
                                        </button>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if($deviceTokens->hasPages())
                                        {{ $deviceTokens->links() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-phone fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No device tokens found</h5>
                            <p class="text-muted">Device tokens will appear here when users register for push notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.mobile-footer')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const tokenCheckboxes = document.querySelectorAll('.token-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    // Select all functionality
    selectAll.addEventListener('change', function() {
        tokenCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox functionality
    tokenCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.token-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        
        if (count > 0) {
            bulkActions.style.display = 'flex';
        } else {
            bulkActions.style.display = 'none';
        }

        // Update select all checkbox
        selectAll.indeterminate = count > 0 && count < tokenCheckboxes.length;
        selectAll.checked = count === tokenCheckboxes.length;
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value;
    const url = new URL(window.location);
    
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    } else {
        url.searchParams.delete('search');
    }
    
    window.location = url;
}

function testToken(tokenId) {
    fetch(`/admin/notifications/device-tokens/${tokenId}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test notification sent successfully!');
        } else {
            alert('Failed to send test notification: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function activateToken(tokenId) {
    updateTokenStatus(tokenId, true);
}

function deactivateToken(tokenId) {
    updateTokenStatus(tokenId, false);
}

function updateTokenStatus(tokenId, isActive) {
    const action = isActive ? 'activate' : 'deactivate';
    
    fetch(`/admin/notifications/device-tokens/${tokenId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(`Failed to ${action} token: ` + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function deleteToken(tokenId) {
    if (!confirm('Are you sure you want to delete this device token? This action cannot be undone.')) {
        return;
    }

    fetch(`/admin/notifications/device-tokens/${tokenId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to delete token: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function testSelectedTokens() {
    const selectedTokens = Array.from(document.querySelectorAll('.token-checkbox:checked')).map(cb => cb.value);
    
    if (selectedTokens.length === 0) {
        alert('Please select at least one token.');
        return;
    }

    fetch('/admin/notifications/device-tokens/test-bulk', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ token_ids: selectedTokens })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Test notifications sent to ${selectedTokens.length} devices!`);
        } else {
            alert('Failed to send test notifications: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function deactivateSelectedTokens() {
    const selectedTokens = Array.from(document.querySelectorAll('.token-checkbox:checked')).map(cb => cb.value);
    
    if (selectedTokens.length === 0) {
        alert('Please select at least one token.');
        return;
    }

    if (!confirm(`Are you sure you want to deactivate ${selectedTokens.length} selected tokens?`)) {
        return;
    }

    fetch('/admin/notifications/device-tokens/deactivate-bulk', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ token_ids: selectedTokens })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to deactivate tokens: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function deleteSelectedTokens() {
    const selectedTokens = Array.from(document.querySelectorAll('.token-checkbox:checked')).map(cb => cb.value);
    
    if (selectedTokens.length === 0) {
        alert('Please select at least one token.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${selectedTokens.length} selected tokens? This action cannot be undone.`)) {
        return;
    }

    fetch('/admin/notifications/device-tokens/delete-bulk', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ token_ids: selectedTokens })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to delete tokens: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function cleanupInactiveTokens() {
    if (!confirm('Are you sure you want to cleanup all inactive device tokens? This will remove tokens that haven\'t been used in the last 30 days.')) {
        return;
    }

    fetch('/admin/notifications/device-tokens/cleanup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Cleanup completed! Removed ${data.cleaned_count} inactive tokens.`);
            location.reload();
        } else {
            alert('Failed to cleanup tokens: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Token copied to clipboard!');
    }, function(err) {
        alert('Failed to copy token: ' + err);
    });
}
</script>

<style>
.avatar-sm img,
.avatar-sm div {
    width: 32px;
    height: 32px;
}

.token-preview {
    max-width: 150px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.table-responsive {
    border-radius: 0.375rem;
}

#bulkActions {
    display: none !important;
}

#bulkActions.show {
    display: flex !important;
}
</style>
@endsection