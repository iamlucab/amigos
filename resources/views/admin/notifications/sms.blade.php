@extends('adminlte::page')

@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">SMS Text Blasting</h1>
                    <p class="text-muted mb-0">Send bulk SMS messages via SMS service</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <button type="button" class="btn btn-info" onclick="checkSmsBalance()">
                        <i class="bi bi-wallet2"></i> Check Balance
                    </button>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- SMS Blast Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-chat-dots me-2"></i> Send SMS Blast
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.send.sms') }}" method="POST" id="smsBlastForm">
                        @csrf
                        
                        <!-- Campaign Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="campaign_name" class="form-label">Campaign Name</label>
                                <input type="text" class="form-control @error('campaign_name') is-invalid @enderror" 
                                       id="campaign_name" name="campaign_name" value="{{ old('campaign_name') }}" 
                                       placeholder="e.g., Monthly Promotion">
                                @error('campaign_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional campaign identifier</small>
                            </div>
                            <div class="col-md-6">
                                <label for="target_type" class="form-label">Target Audience <span class="text-danger">*</span></label>
                                <select class="form-select @error('target_type') is-invalid @enderror" 
                                        id="target_type" name="target_type" required>
                                    <option value="">Select target audience</option>
                                    <option value="all_users" {{ old('target_type') == 'all_users' ? 'selected' : '' }}>
                                        All Users ({{ $userStats['users_with_mobile'] }} recipients)
                                    </option>
                                    <option value="all_members" {{ old('target_type') == 'all_members' ? 'selected' : '' }}>
                                        All Members ({{ $userStats['members_with_mobile'] }} recipients)
                                    </option>
                                    <option value="role_based" {{ old('target_type') == 'role_based' ? 'selected' : '' }}>
                                        By Role
                                    </option>
                                    <option value="specific_users" {{ old('target_type') == 'specific_users' ? 'selected' : '' }}>
                                        Specific Users
                                    </option>
                                </select>
                                @error('target_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role Selection (hidden by default) -->
                        <div class="mb-3 d-none" id="roleSelection">
                            <label for="role" class="form-label">Select Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Select role</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>
                                    Admin ({{ $userStats['admins'] }} users)
                                </option>
                                <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>
                                    Staff ({{ $userStats['staff'] }} users)
                                </option>
                                <option value="Member" {{ old('role') == 'Member' ? 'selected' : '' }}>
                                    Members ({{ $userStats['members'] }} users)
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
                                        {{ $user->name }} ({{ $user->mobile_number }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                        </div>

                        <!-- Message Content -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="6" 
                                      placeholder="Enter your SMS message here..." 
                                      maxlength="1000" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    <span id="charCount">0</span>/1000 characters
                                    • <span id="smsCount">1</span> SMS
                                    • Est. cost: ₱<span id="estimatedCost">0.00</span>
                                </small>
                                <small class="text-muted">
                                    Recipients: <span id="recipientCount">0</span>
                                </small>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2" 
                                      placeholder="Optional notes about this campaign...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SMS Preview -->
                        <div class="card bg-success bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-eye me-1"></i> SMS Preview
                                </h6>
                                <div class="sms-preview">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <div class="sms-bubble">
                                                <div class="sms-sender">{{ config('services.semaphore.sender_name', 'E-Bili') }}</div>
                                                <div class="sms-content" id="previewMessage">Your SMS message will appear here...</div>
                                                <div class="sms-time">now</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-info" id="testSmsBtn">
                                <i class="bi bi-flask me-1"></i> Test SMS
                            </button>
                            <button type="submit" class="btn btn-success" id="sendSmsBtn">
                                <i class="bi bi-send me-1"></i> Send SMS Blast
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <!-- SMS Balance -->
            @if($smsBalance && $smsBalance['success'])
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-wallet2 me-2"></i> SMS Balance
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success">₱{{ number_format($smsBalance['balance'], 2) }}</h3>
                    <p class="text-muted mb-0">{{ $smsBalance['account_name'] ?? 'Semaphore Account' }}</p>
                    <small class="text-muted">Approximately {{ floor($smsBalance['balance'] / 2.5) }} SMS messages</small>
                </div>
            </div>
            @endif

            <!-- User Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-people me-2"></i> User Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h4 class="text-primary">{{ number_format($userStats['users_with_mobile']) }}</h4>
                            <small class="text-muted">Users with Mobile</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">{{ number_format($userStats['total_users']) }}</h4>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-shield-check text-danger"></i> Admins</span>
                            <span class="badge bg-danger">{{ $userStats['admins'] }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-person-badge text-warning"></i> Staff</span>
                            <span class="badge bg-warning">{{ $userStats['staff'] }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-person text-success"></i> Members</span>
                            <span class="badge bg-success">{{ $userStats['members'] }}</span>
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
                        <a href="{{ route('admin.notifications.sms.history') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-clock-history me-1"></i> SMS History
                        </a>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="checkSmsBalance()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Balance
                        </button>
                        <a href="{{ route('admin.notifications.push') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-bell me-1"></i> Switch to Push
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test SMS Modal -->
<div class="modal fade" id="testSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test SMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="testMobileNumber" class="form-label">Mobile Number</label>
                    <input type="tel" class="form-control" id="testMobileNumber" 
                           placeholder="09171234567" maxlength="11">
                    <small class="text-muted">Enter 11-digit mobile number (09XXXXXXXXX)</small>
                </div>
                <div class="mb-3">
                    <label for="testMessage" class="form-label">Test Message</label>
                    <textarea class="form-control" id="testMessage" rows="3" 
                              placeholder="Test message from E-Bili admin panel" maxlength="160"></textarea>
                    <small class="text-muted">Maximum 160 characters for single SMS</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="sendTestSms()">
                    <i class="bi bi-send me-1"></i> Send Test
                </button>
            </div>
        </div>
    </div>
</div>
@include('partials.mobile-footer')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const smsCount = document.getElementById('smsCount');
    const estimatedCost = document.getElementById('estimatedCost');
    const recipientCount = document.getElementById('recipientCount');
    const previewMessage = document.getElementById('previewMessage');
    const targetType = document.getElementById('target_type');
    const roleSelection = document.getElementById('roleSelection');
    const userSelection = document.getElementById('userSelection');

    // User statistics for recipient calculation
    const userStats = @json($userStats);

    // Character counter and cost calculation
    messageTextarea.addEventListener('input', function() {
        const length = this.value.length;
        const smsCountValue = Math.ceil(length / 160) || 1;
        
        charCount.textContent = length;
        smsCount.textContent = smsCountValue;
        
        // Update preview
        previewMessage.textContent = this.value || 'Your SMS message will appear here...';
        
        // Calculate cost and recipients
        updateCostAndRecipients();
    });

    // Target type change handler
    targetType.addEventListener('change', function() {
        roleSelection.classList.add('d-none');
        userSelection.classList.add('d-none');

        if (this.value === 'role_based') {
            roleSelection.classList.remove('d-none');
        } else if (this.value === 'specific_users') {
            userSelection.classList.remove('d-none');
        }

        updateCostAndRecipients();
    });

    // Role change handler
    document.getElementById('role').addEventListener('change', updateCostAndRecipients);
    
    // User selection change handler
    document.getElementById('user_ids').addEventListener('change', updateCostAndRecipients);

    function updateCostAndRecipients() {
        let recipients = 0;
        const targetTypeValue = targetType.value;
        
        switch (targetTypeValue) {
            case 'all_users':
                recipients = userStats.users_with_mobile;
                break;
            case 'all_members':
                recipients = userStats.members_with_mobile;
                break;
            case 'role_based':
                const role = document.getElementById('role').value;
                if (role === 'Admin') recipients = userStats.admins;
                else if (role === 'Staff') recipients = userStats.staff;
                else if (role === 'Member') recipients = userStats.members;
                break;
            case 'specific_users':
                recipients = document.getElementById('user_ids').selectedOptions.length;
                break;
        }

        recipientCount.textContent = recipients;
        
        // Calculate cost (₱2.50 per SMS)
        const smsCountValue = parseInt(smsCount.textContent) || 1;
        const totalCost = recipients * smsCountValue * 2.50;
        estimatedCost.textContent = totalCost.toFixed(2);
    }

    // Test SMS button
    document.getElementById('testSmsBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('testSmsModal'));
        modal.show();
    });

    // Form submission confirmation
    document.getElementById('smsBlastForm').addEventListener('submit', function(e) {
        const recipients = parseInt(recipientCount.textContent);
        const cost = parseFloat(estimatedCost.textContent);
        
        if (recipients === 0) {
            e.preventDefault();
            alert('No recipients selected. Please choose a target audience.');
            return;
        }

        if (!confirm(`Are you sure you want to send SMS to ${recipients} recipients?\nEstimated cost: ₱${cost.toFixed(2)}`)) {
            e.preventDefault();
        }
    });
});

function sendTestSms() {
    const mobileNumber = document.getElementById('testMobileNumber').value;
    const message = document.getElementById('testMessage').value;
    
    if (!mobileNumber || !message) {
        alert('Please enter both mobile number and message');
        return;
    }

    if (!/^09[0-9]{9}$/.test(mobileNumber)) {
        alert('Please enter a valid 11-digit mobile number (09XXXXXXXXX)');
        return;
    }

    fetch('{{ route("admin.notifications.test.sms") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            mobile_number: mobileNumber,
            message: message 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test SMS sent successfully!');
            const modal = document.getElementById('testSmsModal');
            const modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
            modalInstance.hide();
        } else {
            alert('Failed to send test SMS: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function checkSmsBalance() {
    fetch('{{ route("admin.notifications.sms.balance") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`SMS Balance: ₱${data.balance.toFixed(2)}\nAccount: ${data.account_name || 'Semaphore Account'}`);
        } else {
            alert('Failed to get SMS balance: ' + data.error);
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
}

.sms-bubble {
    background: #007bff;
    color: white;
    border-radius: 18px;
    padding: 12px 16px;
    max-width: 250px;
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
    margin-bottom: 4px;
}

.sms-time {
    font-size: 0.7rem;
    opacity: 0.7;
    text-align: right;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn:hover {
    transform: translateY(-1px);
}

.form-control:focus,
.form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection