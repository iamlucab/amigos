{{-- ✅ Send Modal --}}
<div class="modal fade" id="sendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('wallet.transfer') }}">
            @csrf
            <input type="hidden" name="_modal" value="send">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send to any member account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && old('_modal') === 'send')
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" class="form-control"
                           value="{{ old('mobile_number') }}"
                           maxlength="11" minlength="11"
                           pattern="^09\d{9}$"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           required
                           placeholder="e.g. 09123456789">

                    <small id="recipientName" class="text-muted d-block mt-1"></small>

                    <label class="mt-3">Amount</label>
                    <small class="d-block text-muted">Available: ₱{{ number_format($wallet->balance, 2) }}</small>
                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>

                    <div class="alert mt-3 small" style="background-color: #fff9db; color: #856404;" role="alert">
                        Confirmed transactions will not be refunded. Please make sure the mobile number and amount are correct.
                    </div>

                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="confirmSendCheckbox">
                        <label class="form-check-label" for="confirmSendCheckbox">I confirm that the details are correct.</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="sendButton" disabled>Send</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Borrow Modal --}}
<div class="modal fade" id="borrowModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('loan.request') }}">
            @csrf
            <input type="hidden" name="_modal" value="borrow">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Borrow Money</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @if ($errors->any() && old('_modal') === 'borrow')
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="monthlyPreview" class="mt-3 d-none">
                        <div class="alert alert-info small mb-0">
                            Estimated Monthly Payment: <strong id="monthlyAmount">₱0.00</strong>
                        </div>
                    </div>

                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>

                    <label class="mt-3">Terms</label>
                    <select name="term_months" class="form-control" required>
                        <option value="" disabled selected>Select Term</option>
                        <option value="6" {{ old('term_months') == '6' ? 'selected' : '' }}>6 Months</option>
                        <option value="12" {{ old('term_months') == '12' ? 'selected' : '' }}>12 Months</option>
                    </select>

                    <label class="mt-3">Purpose <small class="text-muted">(optional)</small></label>
                    <input type="text" name="purpose" class="form-control" placeholder="e.g., School Fees, Vacation, etc." value="{{ old('purpose') }}">

                    <div class="alert alert-warning mt-3 small" role="alert">
                        Your loan request will be subject to approval. Make sure the amount and term are correct before submitting.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Borrow</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Cash In Modal --}}
<div class="modal fade" id="cashinModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('wallet.cashin') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Cash In</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label class="mt-3">Payment Method</label>
                    <select name="payment_method" class="form-control" id="paymentMethodSelect" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank">Bank Transfer</option>
                        <option value="Others">Others</option>
                    </select>

                    <div id="gcashCollapse" class="mt-3" style="display: none;">
                        <div class="card border rounded shadow-sm p-3">
                            <div class="text-center">
                                <label class="fw-bold d-block mb-2">Scan GCash QR Code</label>
                                <small class="text-muted d-block mb-2">Use this QR in GCash to complete your cash in request.</small>
                                <img src="{{ asset('images/gcashQR.jpeg') }}" alt="GCash QR Code"
                                     class="img-fluid rounded shadow-sm mb-2" style="max-width: 200px;">
                                <small class="text-muted d-block mb-2">GCash Account: <strong>LU*** CAB*</strong></small>
                                <a href="{{ asset('images/gcashQR.jpeg') }}" download class="btn btn-sm btn-primary">
                                    <i class="bi bi-download"></i> or Download QR Code
                                </a>
                            </div>
                            <label class="mt-3">Amount Sent</label>
                            <input type="number" class="form-control" name="amount" value="{{ old('amount') }}">
                            <label class="mt-3">Reference / Notes</label>
                            <input type="text" class="form-control" name="gcash_note" value="{{ old('gcash_note') }}">
                            <small class="text-muted">Make sure the amount matches what you sent via GCash.</small>
                        </div>
                    </div>

                    <div id="bankCollapse" class="mt-3" style="display: none;">
                        <div class="card border rounded shadow-sm p-3">
                            <label class="fw-bold mb-2">Bank Information</label>
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('images/bdo-logo.png') }}" alt="BDO Logo" class="me-2" style="max-width: 60px;">
                                <div>
                                    <div><strong>Bank:</strong> BDO</div>
                                    <div><strong>Account Name:</strong> Hugpong Amigos</div>
                                    <div><strong>Account No:</strong> <span id="bankAccount">0071 5801 3083</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 ms-1" onclick="navigator.clipboard.writeText('007158013083')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Please send the exact amount and upload the proof below.</small>
                        </div>
                    </div>

                    <label class="mt-3">Upload Proof of Payment <small class="text-muted">(optional)</small></label>
                    <input type="file" name="proof" class="form-control" accept="image/*">
                    <small class="text-muted">Accepted: JPG/PNG. Max size: 2MB.</small>

                    <label class="mt-3">Notes <small class="text-muted">(optional)</small></label>
                    <input type="text" name="note" class="form-control" placeholder="e.g., Reference number, time sent">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- JS for Modals --}}
@push('js')
<script>
    $(document).ready(function () {
        if (@json($errors->any()) && '{{ old('_modal') }}' === 'send') {
            $('#sendModal').modal('show');
        } else if ('{{ old('_modal') }}' === 'borrow') {
            $('#borrowModal').modal('show');
        }

        $('#confirmSendCheckbox').on('change', function () {
            $('#sendButton').prop('disabled', !this.checked);
        });

        $('input[name="mobile_number"]').on('blur', function () {
            let number = $(this).val();
            if (number.length === 11) {
                $.get("{{ url('/api/member-name') }}/" + number, function (data) {
                    $('#recipientName').text('Send to ' + (data.full_name || '***'));
                }).fail(() => {
                    $('#recipientName').text('No record found');
                });
            }
        });

        $('#paymentMethodSelect').on('change', function () {
            $('#gcashCollapse').hide();
            $('#bankCollapse').hide();
            if (this.value === 'GCash') $('#gcashCollapse').show();
            if (this.value === 'Bank') $('#bankCollapse').show();
        }).trigger('change');
    });
</script>

{{-- script for self-send validate --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileInput = document.querySelector('input[name="mobile_number"]');
        const sendButton = document.getElementById('sendButton');
        const recipientName = document.getElementById('recipientName');

        if (mobileInput) {
            mobileInput.addEventListener('input', function () {
                const enteredNumber = this.value.replace(/\D/g, '');
                const senderNumber = "{{ auth()->user()->member->mobile_number }}";

                if (enteredNumber === senderNumber) {
                    recipientName.textContent = "⚠️ You cannot send to own account.";
                    recipientName.classList.add('text-danger');
                    sendButton.disabled = true;
                } else {
                    recipientName.textContent = "";
                    recipientName.classList.remove('text-danger');
                    sendButton.disabled = !document.getElementById('confirmSendCheckbox').checked;
                }
            });
        }

        const confirmSendCheckbox = document.getElementById('confirmSendCheckbox');
        confirmSendCheckbox.addEventListener('change', function () {
            const enteredNumber = mobileInput.value.replace(/\D/g, '');
            const senderNumber = "{{ auth()->user()->member->mobile_number }}";

            sendButton.disabled = !this.checked || enteredNumber === senderNumber;
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const availableBalance = parseFloat({{ $wallet->balance ?? 0 }});
        const amountInput = document.querySelector('input[name="amount"]');
        const checkbox = document.getElementById('confirmSendCheckbox');
        const sendButton = document.getElementById('sendButton');

        function validateSendForm() {
            const amount = parseFloat(amountInput.value);
            const isChecked = checkbox.checked;

            const hasEnoughBalance = !isNaN(amount) && amount <= availableBalance && amount > 0;

            sendButton.disabled = !(hasEnoughBalance && isChecked);

            // Show/hide warning
            let warning = document.getElementById('amountWarning');
            if (!hasEnoughBalance && amountInput.value) {
                if (!warning) {
                    warning = document.createElement('small');
                    warning.id = 'amountWarning';
                    warning.classList.add('text-danger', 'mt-1', 'd-block');
                    warning.innerText = 'Insufficient balance.';
                    amountInput.parentNode.insertBefore(warning, amountInput.nextSibling);
                }
            } else if (warning) {
                warning.remove();
            }
        }

        amountInput.addEventListener('input', validateSendForm);
        checkbox.addEventListener('change', validateSendForm);
    });
</script>

@endpush
