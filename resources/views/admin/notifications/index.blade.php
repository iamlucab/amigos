@extends('adminlte::page')

@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Notification Management</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.notifications.push') }}" class="btn btn-primary">
                        <i class="bi bi-bell"></i> Push Notifications
                    </a>
                    <a href="{{ route('admin.notifications.sms') }}" class="btn btn-success">
                        <i class="bi bi-chat-text"></i> SMS Blasting
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Push Notification Stats -->
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-bell me-2"></i>Push Notifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-primary">{{ number_format($pushStats['active_devices']) }}</h3>
                                <small class="text-muted">Active Devices</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-info">{{ number_format($pushStats['total_devices']) }}</h3>
                                <small class="text-muted">Total Devices</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="bi bi-apple text-secondary"></i>
                                <span class="d-block">{{ $pushStats['ios_devices'] }}</span>
                                <small class="text-muted">iOS</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="bi bi-android text-success"></i>
                                <span class="d-block">{{ $pushStats['android_devices'] }}</span>
                                <small class="text-muted">Android</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="bi bi-globe text-primary"></i>
                                <span class="d-block">{{ $pushStats['web_devices'] }}</span>
                                <small class="text-muted">Web</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS Stats -->
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-chat-text me-2"></i>SMS Blasting
                        @if($smsBalance && $smsBalance['success'])
                            <span class="badge bg-light text-dark ms-2">
                                Balance: ₱{{ number_format($smsBalance['balance'], 2) }}
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-success">{{ number_format($smsStats['total_campaigns']) }}</h3>
                                <small class="text-muted">Campaigns This Month</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-info">{{ number_format($smsStats['total_recipients']) }}</h3>
                                <small class="text-muted">Total Recipients</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                <span class="d-block">{{ number_format($smsStats['successful_sends']) }}</span>
                                <small class="text-muted">Sent</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                <span class="d-block">{{ number_format($smsStats['failed_sends']) }}</span>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="bi bi-currency-dollar text-warning"></i>
                                <span class="d-block">{{ number_format($smsStats['total_cost'], 2) }}</span>
                                <small class="text-muted">Cost</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title text-white mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.notifications.push') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-send mb-2 d-block"></i>
                                Send Push Notification
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.notifications.sms') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-chat-dots mb-2 d-block"></i>
                                Send SMS Blast
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.notifications.devices') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-phone mb-2 d-block"></i>
                                Manage Devices
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.notifications.sms.history') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-clock-history mb-2 d-block"></i>
                                SMS History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent SMS Campaigns -->
    @if($recentSmsLogs->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent SMS Campaigns</h5>
                    <a href="{{ route('admin.notifications.sms.history') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Message</th>
                                    <th>Recipients</th>
                                    <th>Status</th>
                                    <th>Success Rate</th>
                                    <th>Cost</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSmsLogs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->campaign_name ?: 'Untitled Campaign' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->recipient_type_display }}</small>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $log->message }}">
                                            {{ $log->message_preview }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($log->total_recipients) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->status_badge_class }}">
                                            {{ $log->status_display }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->status === 'completed')
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" style="width: {{ $log->success_rate }}%">
                                                    {{ $log->success_rate }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->estimated_cost)
                                            ₱{{ number_format($log->estimated_cost, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $log->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- User Notifications -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white ">
                        <i class="bi bi-bell me-2"></i> Your Notifications
                    </h5>
                    @if($notifications->count() > 0)
                        <button class="btn btn-sm btn-light" onclick="markAllAsRead()">Mark All as Read</button>
                    @endif
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->read ? '' : 'bg-light' }}">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">{{ $notification->message }}</h6>
                                        @if(!$notification->read)
                                            <button class="btn btn-sm btn-primary mark-as-read" data-id="{{ $notification->id }}">Mark as Read</button>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i> No notifications found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.mobile-footer')

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.progress {
    background-color: #e9ecef;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
}

.badge-success {
    background-color: #28a745 !important;
}

.badge-danger {
    background-color: #dc3545 !important;
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.badge-secondary {
    background-color: #6c757d !important;
}
</style>

<script>
    // Mark a single notification as read
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('mark-as-read')) {
            const id = e.target.dataset.id;
            fetch(`/admin/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the button and update the row style
                    e.target.closest('.list-group-item').classList.remove('bg-light');
                    e.target.remove();
                }
            });
        }
    });

    // Mark all notifications as read
    function markAllAsRead() {
        fetch('/admin/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update all rows and remove all buttons
                document.querySelectorAll('.list-group-item').forEach(item => {
                    item.classList.remove('bg-light');
                });
                document.querySelectorAll('.mark-as-read').forEach(button => {
                    button.remove();
                });
            }
        });
    }
</script>
@endsection