@extends('adminlte::page')

@section('title', 'Pending Members')

@section('content_header')
    <h5>Pending Member Approvals</h5>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header text-white">
            <h3 class="card-title text-white ">Members Awaiting Approval</h3>
        </div>
        <div class="card-body">
            @if($pendingMembers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingMembers as $member)
                                <tr>
                                    <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                                    <td>{{ $member->mobile_number }}</td>
                                    <td>
                                        @if($member->occupation === 'Merchant')
                                            <span class="badge badge-success">Merchant</span>
                                        @else
                                            <span class="badge badge-primary">Member</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->occupation === 'Merchant')
                                            <small class="text-muted">{{ $member->address ?? 'N/A' }}</small>
                                        @else
                                            @if($member->payment_method)
                                                <strong>{{ $member->payment_method }}</strong>
                                            @else
                                                <span class="text-muted">No payment method</span>
                                            @endif
                                            <br>
                                            @if($member->proof_of_payment)
                                                <a href="{{ asset('storage/' . $member->proof_of_payment) }}" target="_blank" class="btn btn-info btn-sm mt-1">
                                                    <i class="bi bi-eye"></i> View Proof
                                                </a>
                                            @else
                                                <span class="text-muted">No proof</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $member->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm approve-btn"
                                                data-toggle="modal"
                                                data-target="#approveModal"
                                                data-member-id="{{ $member->id }}"
                                                data-member-name="{{ $member->first_name }} {{ $member->last_name }}"
                                                data-member-occupation="{{ $member->occupation }}">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                        <form action="{{ route('admin.members.reject', $member->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this member?')">
                                                <i class="bi bi-x"></i> Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Single Approve Modal -->
                <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="approveMemberForm" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="approveModalLabel">
                                        Approve Member
                                        <span id="memberOccupationBadge"></span>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="memberId" name="member_id">
                                    <div class="form-group">
                                        <label for="sponsor_id">Select Sponsor</label>
                                        <select name="sponsor_id" id="sponsor_id" class="form-control" required>
                                            <option value="">-- Select Sponsor --</option>
                                            @foreach($sponsors as $sponsor)
                                                <option value="{{ $sponsor->id }}">{{ $sponsor->first_name }} {{ $sponsor->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="membership_code">Membership Code</label>
                                        <select name="membership_code" id="membership_code" class="form-control" required>
                                            <option value="">-- Select Membership Code --</option>
                                            @foreach($availableCodes as $code)
                                                <option value="{{ $code->code }}">{{ $code->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="approveSubmitBtn">
                                        Approve Member
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No pending members found.
                </div>
            @endif
        </div>
    </div>
@stop

@include('partials.mobile-footer')

@section('css')
    <style>
        @media (max-width: 576px) {
            .table-responsive {
                overflow-x: auto;
            }
            .btn {
                margin-bottom: 5px;
                display: block;
                width: 100%;
            }
        }
    </style>

    <style>
        /* Ensure modal works properly on mobile devices */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .modal-content {
                border-radius: 8px;
            }
        }

        /* Fix for modal flickering issues */
        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal {
            z-index: 1055 !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            // Initialize tooltips if they exist
            $('[data-toggle="tooltip"]').tooltip();

            // Handle approve button click
            $('.approve-btn').on('click', function() {
                var memberId = $(this).data('member-id');
                var memberName = $(this).data('member-name');
                var memberOccupation = $(this).data('member-occupation');

                // Set member ID in hidden input
                $('#memberId').val(memberId);

                // Update modal title
                $('#approveModalLabel').text('Approve ' + memberName);

                // Update occupation badge
                if (memberOccupation === 'Merchant') {
                    $('#memberOccupationBadge').html('<span class="badge badge-success ml-2">Merchant</span>');
                    $('#approveSubmitBtn').text('Approve Merchant');
                } else {
                    $('#memberOccupationBadge').empty();
                    $('#approveSubmitBtn').text('Approve Member');
                }

                // Update form action
                $('#approveMemberForm').attr('action', '/admin/members/approve');
            });

            // Handle modal close to reset form
            $('#approveModal').on('hidden.bs.modal', function() {
                // Reset form
                $('#approveMemberForm')[0].reset();
                $('#memberOccupationBadge').empty();
                $('#approveSubmitBtn').text('Approve Member');

                // Remove validation classes
                $('#approveMemberForm .is-invalid').removeClass('is-invalid');
                $('#approveMemberForm .invalid-feedback').remove();
            });

            // Handle form submission
            $('#approveMemberForm').on('submit', function(e) {
                var form = $(this);
                var button = $('#approveSubmitBtn');
                var originalText = button.html();

                // Disable button and show loading state
                button.prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                // If using AJAX form submission, handle it here
                // For now, we'll let the form submit normally but prevent multiple submissions
                if (form.data('submitting')) {
                    e.preventDefault();
                    return false;
                }

                form.data('submitting', true);
            });
        });
    </script>
@stop
