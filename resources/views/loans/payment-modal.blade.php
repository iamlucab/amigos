<div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pay Loan Installment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('loan-payments.pay-now', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="payment-details mb-3">
                        <h6 class="font-weight-bold">Payment Details</h6>
                        <p class="mb-1"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</p>
                        <p class="mb-1"><strong>Amount Due:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                        @if($payment->due_date < now() && !$payment->is_paid)
                            <div class="alert alert-danger py-1 px-2 mb-0 mt-2">
                                <small><i class="bi bi-exclamation-circle mr-1"></i> This payment is overdue.</small>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="Wallet">Wallet (Balance: ₱{{ number_format(auth()->user()->member->wallet->balance ?? 0, 2) }})</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <div id="external_payment_details" style="display: none;">
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" placeholder="Enter transaction reference number">
                            <small class="form-text text-muted">Enter the reference/transaction number from your payment receipt.</small>
                        </div>

                        <div class="form-group">
                            <label for="payment_proof">Payment Proof</label>
                            <input type="file" name="payment_proof" id="payment_proof" class="form-control-file">
                            <small class="form-text text-muted">Upload a screenshot or photo of your payment receipt (JPEG, PNG, PDF only, max 2MB).</small>
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Any additional information about your payment"></textarea>
                        </div>

                        <div class="alert alert-info">
                            <small><i class="bi bi-info-circle mr-1"></i> Your payment will be marked as pending until verified by an admin.</small>
                        </div>
                    </div>

                    <div id="wallet_payment_notice" style="display: none;">
                        <div class="alert alert-success">
                            <small><i class="bi bi-check-circle mr-1"></i> Payment will be automatically deducted from your wallet balance.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Pay Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const externalPaymentDetails = document.getElementById('external_payment_details');
        const walletPaymentNotice = document.getElementById('wallet_payment_notice');
        
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'Wallet') {
                externalPaymentDetails.style.display = 'none';
                walletPaymentNotice.style.display = 'block';
            } else if (this.value !== '') {
                externalPaymentDetails.style.display = 'block';
                walletPaymentNotice.style.display = 'none';
            } else {
                externalPaymentDetails.style.display = 'none';
                walletPaymentNotice.style.display = 'none';
            }
        });
    });
</script>