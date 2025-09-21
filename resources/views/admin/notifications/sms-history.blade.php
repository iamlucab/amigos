@extends('adminlte::page')

@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">SMS History</h1>
                    <p class="text-muted mb-0">View and manage SMS campaign history</p>
                </div>
                <div class="btn-group text-white">
                    <a href="{{ route('admin.notifications.sms') }}" class="btn btn-info text-white">
                        <i class="bi bi-plus text-white"></i> New SMS Blast
                    </a>&nbsp;
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
                    <i class="bi bi-send fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary">{{ number_format($stats['total_campaigns']) }}</h4>
                    <small class="text-muted">Total Campaigns</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="text-success">{{ number_format($stats['total_sent']) }}</h4>
                    <small class="text-muted">Messages Sent</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-clock fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning">{{ number_format($stats['pending']) }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-currency-dollar fa-2x text-info mb-2"></i>
                    <h4 class="text-info">₱{{ number_format($stats['total_cost'], 2) }}</h4>
                    <small class="text-muted">Total Cost</small>
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
                                <i class="bi bi-clock-history me-2"></i> SMS Campaign History
                            </h5>
                        </div>
                        <div class="col-auto">
                            <!-- Filters -->
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-warning dropdown-toggle" 
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?status=all">All Status</a></li>
                                    <li><a class="dropdown-item" href="?status=sent">Sent</a></li>
                                    <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                                    <li><a class="dropdown-item" href="?status=failed">Failed</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="?period=today">Today</a></li>
                                    <li><a class="dropdown-item" href="?period=week">This Week</a></li>
                                    <li><a class="dropdown-item" href="?period=month">This Month</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($smsLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Recipients</th>
                                        <th>Message Preview</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Sent At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($smsLogs as $log)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $log->campaign_name ?: 'SMS Campaign #' . $log->id }}</strong>
                                                    @if($log->target_type)
                                                        <br><small class="text-muted">
                                                            Target: {{ ucfirst(str_replace('_', ' ', $log->target_type)) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($log->recipient_count) }}</span>
                                                @if($log->successful_count !== $log->recipient_count)
                                                    <br><small class="text-success">
                                                        {{ $log->successful_count }} sent
                                                    </small>
                                                    @if($log->failed_count > 0)
                                                        <br><small class="text-danger">
                                                            {{ $log->failed_count }} failed
                                                        </small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="message-preview">
                                                    {{ Str::limit($log->message, 50) }}
                                                    @if(strlen($log->message) > 50)
                                                        <button type="button" class="btn btn-link btn-sm p-0" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#messageModal{{ $log->id }}">
                                                            <small>Read more</small>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @switch($log->status)
                                                    @case('sent')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check me-1"></i>Sent
                                                        </span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-clock me-1"></i>Pending
                                                        </span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x me-1"></i>Failed
                                                        </span>
                                                        @break
                                                    @case('partial')
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>Partial
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <strong>₱{{ number_format($log->cost, 2) }}</strong>
                                                @if($log->recipient_count > 0)
                                                    <br><small class="text-muted">
                                                        ₱{{ number_format($log->cost / $log->recipient_count, 2) }}/SMS
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detailModal{{ $log->id }}"
                                                            title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    @if($log->status === 'failed' || $log->failed_count > 0)
                                                        <button type="button" class="btn btn-outline-warning"
                                                                onclick="retryFailedSms({{ $log->id }})"
                                                                title="Retry Failed">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteSmsLog({{ $log->id }})"
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Message Modal -->
                                        <div class="modal fade" id="messageModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Full Message</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="sms-preview mb-3">
                                                            <div class="sms-bubble">
                                                                <div class="sms-sender">{{ config('services.semaphore.sender_name', 'E-Bili') }}</div>
                                                                <div class="sms-content">{{ $log->message }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="row text-center">
                                                            <div class="col-4">
                                                                <strong>{{ strlen($log->message) }}</strong>
                                                                <br><small class="text-muted">Characters</small>
                                                            </div>
                                                            <div class="col-4">
                                                                <strong>{{ ceil(strlen($log->message) / 160) }}</strong>
                                                                <br><small class="text-muted">SMS Parts</small>
                                                            </div>
                                                            <div class="col-4">
                                                                <strong>{{ $log->recipient_count }}</strong>
                                                                <br><small class="text-muted">Recipients</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Campaign Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <h6>Campaign Information</h6>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Campaign Name:</strong></td>
                                                                        <td>{{ $log->campaign_name ?: 'N/A' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Target Type:</strong></td>
                                                                        <td>{{ ucfirst(str_replace('_', ' ', $log->target_type)) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Status:</strong></td>
                                                                        <td>
                                                                            @switch($log->status)
                                                                                @case('sent')
                                                                                    <span class="badge bg-success">Sent</span>
                                                                                    @break
                                                                                @case('pending')
                                                                                    <span class="badge bg-warning">Pending</span>
                                                                                    @break
                                                                                @case('failed')
                                                                                    <span class="badge bg-danger">Failed</span>
                                                                                    @break
                                                                                @case('partial')
                                                                                    <span class="badge bg-warning">Partial</span>
                                                                                    @break
                                                                                @default
                                                                                    <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                                                            @endswitch
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Created:</strong></td>
                                                                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Delivery Statistics</h6>
                                                                <table class="table table-sm">
                                                                    <tr>
                                                                        <td><strong>Total Recipients:</strong></td>
                                                                        <td>{{ number_format($log->recipient_count) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Successfully Sent:</strong></td>
                                                                        <td class="text-success">{{ number_format($log->successful_count) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Failed:</strong></td>
                                                                        <td class="text-danger">{{ number_format($log->failed_count) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Total Cost:</strong></td>
                                                                        <td><strong>₱{{ number_format($log->cost, 2) }}</strong></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        @if($log->notes)
                                                            <div class="mb-3">
                                                                <h6>Notes</h6>
                                                                <div class="alert alert-info">{{ $log->notes }}</div>
                                                            </div>
                                                        @endif

                                                        @if($log->error_message)
                                                            <div class="mb-3">
                                                                <h6>Error Details</h6>
                                                                <div class="alert alert-danger">{{ $log->error_message }}</div>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <h6>Message Content</h6>
                                                            <div class="sms-preview">
                                                                <div class="sms-bubble">
                                                                    <div class="sms-sender">{{ config('services.semaphore.sender_name', 'E-Bili') }}</div>
                                                                    <div class="sms-content">{{ $log->message }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        @if($log->status === 'failed' || $log->failed_count > 0)
                                                            <button type="button" class="btn btn-warning" 
                                                                    onclick="retryFailedSms({{ $log->id }})">
                                                                <i class="bi bi-arrow-clockwise me-1"></i>Retry Failed
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($smsLogs->hasPages())
                            <div class="card-footer">
                                {{ $smsLogs->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No SMS campaigns found</h5>
                            <p class="text-muted">Start by creating your first SMS blast campaign.</p>
                            <a href="{{ route('admin.notifications.sms') }}" class="btn btn-info text-white">
                                <i class="bi bi-plus me-1"></i> Create SMS Campaign
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.mobile-footer')
<script>
function retryFailedSms(logId) {
    if (!confirm('Are you sure you want to retry sending failed SMS messages for this campaign?')) {
        return;
    }

    fetch(`/admin/notifications/sms/${logId}/retry`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Retry initiated successfully!');
            location.reload();
        } else {
            alert('Failed to retry SMS: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function deleteSmsLog(logId) {
    if (!confirm('Are you sure you want to delete this SMS campaign log? This action cannot be undone.')) {
        return;
    }

    fetch(`/admin/notifications/sms/${logId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('SMS campaign log deleted successfully!');
            location.reload();
        } else {
            alert('Failed to delete SMS log: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>

<style>
.sms-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    justify-content: flex-start;
}

.sms-bubble {
    background: #007bff;
    color: white;
    border-radius: 18px;
    padding: 12px 16px;
    max-width: 300px;
    position: relative;
}

.sms-bubble::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 10px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #007bff;
}

.sms-sender {
    font-size: 0.75rem;
    opacity: 0.8;
    margin-bottom: 4px;
}

.sms-content {
    font-size: 0.9rem;
    line-height: 1.4;
}

.message-preview {
    max-width: 200px;
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

.table-responsive {
    border-radius: 0.375rem;
}
</style>
@endsection