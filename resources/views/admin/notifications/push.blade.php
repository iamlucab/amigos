@extends('adminlte::page')

@section('adminlte_css_pre')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Push Notifications</h1>
                    <p class="text-muted mb-0">Send push notifications to mobile devices and web browsers</p>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Send Push Notification Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-send me-2"></i> Send Push Notification
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.send.push') }}" method="POST" id="pushNotificationForm">
                        @csrf
                        
                        <!-- Notification Content -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Notification title" maxlength="255" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maximum 255 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label for="target_type" class="form-label">Target Audience <span class="text-danger">*</span></label>
                                <select class="form-select @error('target_type') is-invalid @enderror" 
                                        id="target_type" name="target_type" required>
                                    <option value="">Select target audience</option>
                                    <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>All Users</option>
                                    <option value="platform" {{ old('target_type') == 'platform' ? 'selected' : '' }}>Specific Platform</option>
                                    <option value="users" {{ old('target_type') == 'users' ? 'selected' : '' }}>Specific Users</option>
                                </select>
                                @error('target_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" name="body" rows="4" 
                                      placeholder="Enter your notification message here..." 
                                      maxlength="1000" required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 1000 characters. <span id="charCount">0</span>/1000</small>
                        </div>

                        <!-- Platform Selection (hidden by default) -->
                        <div class="mb-3 d-none" id="platformSelection">
                            <label for="platform" class="form-label">Platform</label>
                            <select class="form-select" id="platform" name="platform">
                                <option value="">Select platform</option>
                                <option value="ios" {{ old('platform') == 'ios' ? 'selected' : '' }}>
                                    iOS ({{ $deviceStats['ios'] }} devices)
                                </option>
                                <option value="android" {{ old('platform') == 'android' ? 'selected' : '' }}>
                                    Android ({{ $deviceStats['android'] }} devices)
                                </option>
                                <option value="web" {{ old('platform') == 'web' ? 'selected' : '' }}>
                                    Web ({{ $deviceStats['web'] }} devices)
                                </option>
                            </select>
                        </div>

                        <!-- User Selection (hidden by default) -->
                        <div class="mb-3 d-none" id="userSelection">
                            <label for="user_ids" class="form-label">Select Users</label>
                            <select class="form-select" id="user_ids" name="user_ids[]" multiple size="8">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                        </div>

                        <!-- Advanced Options -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <button class="btn btn-link p-0 text-decoration-none text-white " type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#advancedOptions">
                                        <i class="bi bi-gear me-1"></i> Advanced Options
                                        <i class="bi bi-chevron-down ms-1"></i>
                                    </button>
                                </h6>
                            </div>
                            <div class="collapse" id="advancedOptions">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="data_type" class="form-label">Action Type</label>
                                            <select class="form-select" id="data_type" name="data[type]">
                                                <option value="">No specific action</option>
                                                <option value="order_update">Order Update</option>
                                                <option value="loan_update">Loan Update</option>
                                                <option value="wallet_update">Wallet Update</option>
                                                <option value="promotion">Promotion</option>
                                                <option value="announcement">Announcement</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="data_url" class="form-label">Action URL</label>
                                            <input type="url" class="form-control" id="data_url" name="data[action_url]" 
                                                   placeholder="https://example.com/page">
                                            <small class="text-muted">URL to open when notification is tapped</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="card bg-primary bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </h6>
                                <div class="notification-preview">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <img src="{{ asset('logo.png') }}" alt="App Icon" class="rounded" width="40" height="40">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold" id="previewTitle">Notification Title</div>
                                            <div class="text-muted" id="previewBody">Your notification message will appear here...</div>
                                            <small class="text-muted">E-Bili Online • now</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-info" id="testNotificationBtn">
                                <i class="bi bi-flask me-1"></i> Test Notification
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Send Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-bar-chart me-2"></i> Device Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h4 class="text-primary">{{ number_format($deviceStats['active']) }}</h4>
                            <small class="text-muted">Active Devices</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">{{ number_format($deviceStats['total']) }}</h4>
                            <small class="text-muted">Total Devices</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-apple text-secondary"></i> iOS</span>
                            <span class="badge bg-secondary">{{ $deviceStats['ios'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $deviceStats['active'] > 0 ? ($deviceStats['ios'] / $deviceStats['active']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-android text-success"></i> Android</span>
                            <span class="badge bg-success">{{ $deviceStats['android'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $deviceStats['active'] > 0 ? ($deviceStats['android'] / $deviceStats['active']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-globe text-primary"></i> Web</span>
                            <span class="badge bg-primary">{{ $deviceStats['web'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $deviceStats['active'] > 0 ? ($deviceStats['web'] / $deviceStats['active']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-tools me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.notifications.devices') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-phone me-1"></i> Manage Devices
                        </a>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="cleanupTokens()">
                            <i class="bi bi-trash3 me-1"></i> Cleanup Expired Tokens
                        </button>
                        <a href="{{ route('admin.notifications.sms') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-chat-text me-1"></i> Switch to SMS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Test Notification Modal -->
<div class="modal fade" id="testNotificationModal" tabindex="-1" aria-labelledby="testNotificationModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testNotificationModalLabel">Test Push Notification</h5>
                <button type="button" class="btn-close" aria-label="Close" id="modalCloseBtn"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="testDeviceToken" class="form-label">Device Token</label>
                    <input type="text" class="form-control" id="testDeviceToken"
                           placeholder="Enter FCM device token">
                    <small class="text-muted">You can get device tokens from the device management page</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendTestNotification()" id="sendTestBtn">
                    <i class="bi bi-send me-1"></i> Send Test
                </button>
            </div>
        </div>
    </div>
</div>
@include('partials.mobile-footer')


<script>
// Global modal instance variable
let testModalInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const bodyTextarea = document.getElementById('body');
    const charCount = document.getElementById('charCount');
    
    bodyTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Preview updates
    const titleInput = document.getElementById('title');
    const previewTitle = document.getElementById('previewTitle');
    const previewBody = document.getElementById('previewBody');

    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Notification Title';
    });

    bodyTextarea.addEventListener('input', function() {
        previewBody.textContent = this.value || 'Your notification message will appear here...';
    });

    // Target type change handler
    const targetType = document.getElementById('target_type');
    const platformSelection = document.getElementById('platformSelection');
    const userSelection = document.getElementById('userSelection');

    targetType.addEventListener('change', function() {
        platformSelection.classList.add('d-none');
        userSelection.classList.add('d-none');

        if (this.value === 'platform') {
            platformSelection.classList.remove('d-none');
        } else if (this.value === 'users') {
            userSelection.classList.remove('d-none');
        }
    });

    // Test notification button
    document.getElementById('testNotificationBtn').addEventListener('click', function() {
        const modalElement = document.getElementById('testNotificationModal');
        testModalInstance = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        testModalInstance.show();
    });

    // Close button handler
    document.getElementById('modalCloseBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeTestModal();
    });

    // Cancel button handler
    document.getElementById('modalCancelBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeTestModal();
    });
});

function sendTestNotification() {
    const deviceToken = document.getElementById('testDeviceToken').value;
    
    if (!deviceToken) {
        alert('Please enter a device token');
        return;
    }

    if (!deviceToken.trim()) {
        alert('Please enter a valid device token');
        return;
    }

    // Disable the send button to prevent multiple clicks
    const sendBtn = document.getElementById('sendTestBtn');
    const originalText = sendBtn.innerHTML;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Sending...';

    fetch('{{ route("admin.notifications.test.push") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ device_token: deviceToken.trim() })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test notification sent successfully!');
            // Close the modal properly
            closeTestModal();
            
            // Clear the input field
            document.getElementById('testDeviceToken').value = '';
        } else {
            alert('Failed to send test notification: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        console.error('Push notification test error:', error);
        alert('Error sending test notification. Please check the console for details.');
    })
    .finally(() => {
        // Re-enable the send button
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalText;
    });
}

function closeTestModal() {
    console.log('closeTestModal called');
    
    try {
        // Method 1: Use global instance
        if (testModalInstance) {
            console.log('Using global instance');
            testModalInstance.hide();
            testModalInstance = null;
            return;
        }
    } catch (e) {
        console.log('Global instance method failed:', e);
    }
    
    try {
        // Method 2: Get instance from element
        const modal = document.getElementById('testNotificationModal');
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            console.log('Using getInstance');
            modalInstance.hide();
            return;
        }
    } catch (e) {
        console.log('getInstance method failed:', e);
    }
    
    try {
        // Method 3: Force close with jQuery if available
        if (typeof $ !== 'undefined') {
            console.log('Using jQuery');
            $('#testNotificationModal').modal('hide');
            return;
        }
    } catch (e) {
        console.log('jQuery method failed:', e);
    }
    
    try {
        // Method 4: Manual DOM manipulation
        console.log('Using manual method');
        const modal = document.getElementById('testNotificationModal');
        
        // Hide modal
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('role');
        
        // Remove backdrop
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Reset body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        console.log('Manual close completed');
    } catch (e) {
        console.log('Manual method failed:', e);
    }
}

function cleanupTokens() {
    if (confirm('Are you sure you want to cleanup expired device tokens?')) {
        fetch('{{ route("admin.notifications.cleanup.tokens") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Expired tokens cleaned up successfully!');
                location.reload();
            } else {
                alert('Failed to cleanup tokens: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
</script>

<style>
.notification-preview {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.progress {
    background-color: #e9ecef;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn:hover {
    transform: translateY(-1px);
}

.form-control:focus,
.form-select:focus {
    border-color: #63189e;
    box-shadow: 0 0 0 0.2rem rgba(99, 24, 158, 0.25);
}
</style>
@endsection