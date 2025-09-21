@extends('layouts.admin')

@section('title', 'Membership Code Requests')

@section('content_header')
    <h5>Membership Code Requests</h5>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h6 class="card-title" style="color:white !important ">Pending Requests</h6>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Member</th>
                                <th>Mobile Number</th>
                                <th>Quantity</th>
                                <th>Reserved Codes</th>
                                <th>Amount per Code</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->member->full_name ?? 'N/A' }}</td>
                                    <td>{{ $request->member->mobile_number ?? 'N/A' }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>{{ $request->reserved_codes_count ?? 0 }}</td>
                                    <td>₱{{ number_format($request->amount_per_code, 2) }}</td>
                                    <td>₱{{ number_format($request->total_amount, 2) }}</td>
                                    <td>{{ $request->payment_method }}</td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($request->status === 'cancelled')
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($request->proof_path)
                                            <button class="btn btn-sm btn-info mb-1" data-toggle="modal" data-target="#proofModal{{ $request->id }}">
                                                <i class="fas fa-file-image"></i> View Proof
                                            </button>
                                        @endif
                                        
                                        @if($request->status === 'pending')
                                            <!-- Button to trigger manual code assignment modal -->
                                            <button class="btn btn-sm btn-primary mb-1" data-toggle="modal" data-target="#assignCodesModal{{ $request->id }}">
                                                <i class="fas fa-code"></i> Assign Codes
                                            </button>
                                            
                                            <form action="{{ route('admin.membership-code-requests.update', $request) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Are you sure you want to approve this request? This will automatically generate codes.')">
                                                    <i class="fas fa-check"></i> Auto Approve
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.membership-code-requests.update', $request) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to reject this request?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No membership code requests found.
                </div>
            @endif
        </div>
    </div>
</div>
@stop

{{-- Proof Modals --}}
@foreach($requests as $request)
    @if($request->proof_path)
        <div class="modal" id="proofModal{{ $request->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Proof</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $request->proof_path) }}" class="img-fluid" alt="Payment Proof">
                        <br><br>
                        <a href="{{ asset('storage/' . $request->proof_path) }}" download class="btn btn-sm btn-secondary">
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Manual Code Assignment Modal -->
    <div class="modal" id="assignCodesModal{{ $request->id }}" tabindex="-1" role="dialog" data-quantity="{{ $request->quantity }}">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.membership-code-requests.assign-codes', $request) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Membership Codes</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Assigning <strong>{{ $request->quantity }}</strong> codes to <strong>{{ $request->member->full_name ?? 'N/A' }}</strong></p>
                        
                        <div class="form-group">
                            <label for="searchCodes{{ $request->id }}">Search Available Codes:</label>
                            <input type="text" class="form-control" id="searchCodes{{ $request->id }}" placeholder="Enter code to search...">
                            <button type="button" class="btn btn-sm btn-info mt-1" onclick="searchCodes({{ $request->id }})">Search</button>
                        </div>
                        
                        <div class="form-group">
                            <label>Select {{ $request->quantity }} Codes:</label>
                            <div id="availableCodesList{{ $request->id }}" class="border p-2" style="max-height: 300px; overflow-y: auto;">
                                <!-- Available codes will be loaded here -->
                                <p class="text-muted">Click "Search" to load available codes</p>
                            </div>
                        </div>
                        
                        <input type="hidden" id="selectedCodes{{ $request->id }}" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" onclick="return validateCodeSelection({{ $request->id }}, {{ $request->quantity }})">Assign Codes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
@stop

@section('js')
<script>
    // Load available codes when modal is shown
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener to all modals
        document.querySelectorAll('[id^="assignCodesModal"]').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                const requestId = this.id.replace('assignCodesModal', '');
                searchCodes(requestId);
            });
        });
    });
    
    function searchCodes(requestId) {
        const searchInput = document.getElementById('searchCodes' + requestId);
        const searchValue = searchInput.value;
        
        // Get the modal to access the data-quantity attribute
        const modal = document.getElementById('assignCodesModal' + requestId);
        const quantity = modal ? modal.getAttribute('data-quantity') : 20;
        
        // Show loading indicator
        const codesList = document.getElementById('availableCodesList' + requestId);
        codesList.innerHTML = '<p class="text-muted">Loading...</p>';
        
        // Make AJAX request to get available codes with the correct quantity
        fetch(`/admin/membership-code-requests/codes?search=${encodeURIComponent(searchValue)}&quantity=${quantity}`)
            .then(response => response.json())
            .then(result => {
                // Handle the updated response format
                const codes = result.data || result;
                codesList.innerHTML = '';
                
                if (!codes || codes.length === 0) {
                    codesList.innerHTML = '<p class="text-muted">No available codes found</p>';
                    return;
                }
                
                codes.forEach(code => {
                    const codeElement = document.createElement('div');
                    codeElement.className = 'form-check mb-2';
                    codeElement.innerHTML = `
                        <input class="form-check-input code-checkbox" type="checkbox" value="${code.id}" id="code${code.id}_${requestId}">
                        <label class="form-check-label" for="code${code.id}_${requestId}">
                            ${code.code} <span class="badge badge-secondary">${code.status || 'available'}</span>
                        </label>
                    `;
                    codesList.appendChild(codeElement);
                });
                
                // Add event listener to checkboxes
                document.querySelectorAll('.code-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateSelectedCodes(requestId);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                codesList.innerHTML = '<p class="text-danger">Error loading codes. Please try again.</p>';
            });
    }
    
    function updateSelectedCodes(requestId) {
        const checkboxes = document.querySelectorAll(`#assignCodesModal${requestId} .code-checkbox:checked`);
        const selectedCodesInput = document.getElementById('selectedCodes' + requestId);
        
        const selectedIds = Array.from(checkboxes).map(cb => cb.value);
        selectedCodesInput.value = selectedIds.join(',');
    }
    
    function validateCodeSelection(requestId, requiredQuantity) {
        const selectedCodesInput = document.getElementById('selectedCodes' + requestId);
        const selectedIds = selectedCodesInput.value ? selectedCodesInput.value.split(',').filter(id => id !== '') : [];
        
        if (selectedIds.length !== requiredQuantity) {
            alert(`Please select exactly ${requiredQuantity} codes.`);
            return false;
        }
        
        // Update the hidden input field with individual values for Laravel
        const form = document.querySelector(`#assignCodesModal${requestId} form`);
        
        // Remove all existing hidden inputs with name "code_ids[]"
        const existingHiddenInputs = form.querySelectorAll('input[name="code_ids[]"]');
        existingHiddenInputs.forEach(input => {
            input.remove();
        });
        
        // Add individual hidden inputs for each selected code
        selectedIds.forEach(id => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'code_ids[]';
            hiddenInput.value = id;
            form.appendChild(hiddenInput);
        });
        
        return true;
    }
</script>
@stop

@include('partials.mobile-footer')
