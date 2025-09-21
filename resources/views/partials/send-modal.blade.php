{{-- ✅ Send Modal --}}
<div class="modal fade" id="sendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('wallet.transfer') }}">
            @csrf
            <input type="hidden" name="_modal" value="send">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send to registered account</h5>
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
