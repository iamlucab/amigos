{{-- E-Bili Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

<div class="row g-3">
    {{-- Personal Information Section --}}
    <div class="col-12">
        <h5 class="section-title text-start" style="font-size: 1.1rem;">
            <i class="bi bi-person me-2"></i> Personal Information
        </h5>
    </div>

    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-person me-1"></i> First Name <span class="text-danger">*</span>
        </label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $member->first_name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-person me-1"></i> Middle Name
        </label>
        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $member->middle_name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-person me-1"></i> Last Name <span class="text-danger">*</span>
        </label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $member->last_name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-cake me-1"></i> Birthday <span class="text-danger">*</span>
        </label>
        <input type="date" name="birthday" class="form-control" value="{{ old('birthday', $member->birthday ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-phone me-1"></i> Mobile Number <span class="text-danger">*</span>
        </label>
        <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number', $member->mobile_number ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-envelope me-1"></i> Email
        </label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $member->user->email ?? '') }}">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i> Optional - for login and notifications
        </small>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-briefcase me-1"></i> Occupation
        </label>
        <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $member->occupation ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-geo-alt me-1"></i> Address
        </label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $member->address ?? '') }}">
    </div>

    {{-- System Information Section --}}
    <div class="col-12 mt-4">
        <h5 class="section-title text-start" style="font-size: 1.1rem;">
            <i class="bi bi-gear-fill me-2"></i> System Information
        </h5>
    </div>

    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-person-tag me-1"></i> Role <span class="text-danger">*</span>
        </label>
        <select name="role" class="form-control" required>
            <option value="Admin" {{ (old('role', $member->role ?? '') == 'Admin') ? 'selected' : '' }}>
                <i class="bi bi-award"></i> Admin
            </option>
            <option value="Staff" {{ (old('role', $member->role ?? '') == 'Staff') ? 'selected' : '' }}>
                <i class="bi bi-person-tie"></i> Staff
            </option>
            <option value="Member" {{ (old('role', $member->role ?? '') == 'Member') ? 'selected' : '' }}>
                <i class="bi bi-person"></i> Member
            </option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-handshake me-1"></i>Sponsor
        </label>
        <select name="sponsor_id" class="form-control" id="sponsorSelect">
            <option value="">-- Select Sponsor --</option>
            @foreach ($sponsors->where('status', 'Approved') as $sponsor)
                <option value="{{ $sponsor->id }}" {{ (old('sponsor_id', $member->sponsor_id ?? '') == $sponsor->id) ? 'selected' : '' }}>
                    {{ $sponsor->first_name }} {{ $sponsor->last_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-check-circle me-1"></i> Status <span class="text-danger">*</span>
        </label>
        <select name="status" class="form-control" id="statusSelect">
            <option value="Pending" {{ (old('status', $member->status ?? '') == 'Pending') ? 'selected' : '' }}>
                <span class="badge bg-warning"> Pending</span>
            </option>
            <option value="Approved" {{ (old('status', $member->status ?? '') == 'Approved') ? 'selected' : '' }}>
                <span class="badge bg-success"> Approved</span>
            </option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-qr-code me-1"></i> Membership Code <span class="text-danger">*</span>
        </label>
        <select name="membership_code" class="form-control" required>
            <option value="">-- Select Membership Code --</option>
            @php
                $unusedCodes = \App\Models\MembershipCode::where('used', false)->get();
                $currentCode = isset($member) && $member->membershipCode ? $member->membershipCode : null;
            @endphp

            {{-- Show current code first if editing --}}
            @if($currentCode)
                <option value="{{ $currentCode->code }}" selected>{{ $currentCode->code }} (Current)</option>
            @endif

            {{-- Show only unused codes --}}
            @foreach ($unusedCodes as $code)
                @if(!$currentCode || $code->code !== $currentCode->code)
                    <option value="{{ $code->code }}">{{ $code->code }}</option>
                @endif
            @endforeach
        </select>
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i> Only unused codes are shown
        </small>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-key me-1"></i> Reset Password (Optional)
        </label>
        <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i> Enter new password to reset, or leave blank to keep current
        </small>
    </div>

    {{-- Additional Settings Section --}}
    <div class="col-12 mt-4">
        <h5 class="section-title text-start" style="font-size: 1.1rem;">
            <i class="bi bi-sliders me-2"></i> Additional Settings
        </h5>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <div class="form-check">
                <input type="checkbox" name="loan_eligible" value="1" class="form-check-input" id="loanEligibleCheckbox"
                    {{ old('loan_eligible', $member->loan_eligible ?? false) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="loanEligibleCheckbox">
                    <i class="bi bi-cash-coin me-1" style="color: var(--primary-purple);"></i>
                    Eligible for Loan
                </label>
                <small class="text-muted d-block mt-1">
                    Allow this member to request loans
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-camera me-1"></i> Photo
        </label>
        <input type="file" name="photo" class="form-control" accept="image/*">
        @if (isset($member) && $member->photo)
            <div class="mt-3">
                <img src="{{ asset('storage/photos/' . $member->photo) }}"
                     width="80" height="80"
                     alt="Member Photo"
                     class="rounded-circle border border-3"
                     style="border-color: var(--primary-purple) !important; object-fit: cover;">
                <small class="text-muted d-block mt-1"> Current photo</small>
            </div>
        @else
            <small class="text-muted d-block mt-1">
                <i class="bi bi-info-circle me-1"></i> No photo uploaded
            </small>
        @endif
    </div>

    {{-- Proof of Payment Section --}}
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-receipt me-1"></i> Proof of Payment
        </label>
        @if (isset($member) && $member->proof_of_payment)
            <div class="mt-2">
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#proofOfPaymentModal">
                    <i class="bi bi-eye"></i> View Proof
                </button>
                <small class="text-muted d-block mt-1">
                    Click to view uploaded proof of payment
                </small>
            </div>

            <!-- Proof of Payment Modal -->
            <div class="modal fade" id="proofOfPaymentModal" tabindex="-1" role="dialog" aria-labelledby="proofOfPaymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="proofOfPaymentModalLabel">Proof of Payment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $member->proof_of_payment) }}"
                                 class="img-fluid"
                                 alt="Proof of Payment"
                                 style="max-height: 80vh;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <a href="{{ asset('storage/' . $member->proof_of_payment) }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-2">
                <span class="text-muted">
                    <i class="bi bi-x-circle me-1"></i> No proof of payment uploaded
                </span>
            </div>
        @endif
    </div>
</div>

<style>
    .form-label {
        font-weight: 600;
        color: var(--primary-purple);
        margin-bottom: 0.5rem;
    }

    .form-control:focus {
        border-color: var(--primary-purple);
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }

    .form-check-input:checked {
        background-color: var(--primary-purple);
        border-color: var(--primary-purple);
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .section-title::after {
        width: 30px;
        left: 0;
        transform: none;
    }
</style>
