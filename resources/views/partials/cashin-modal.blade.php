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
                    {{-- Amount --}}
                    <label>Amount to Load</label>
                    <input type="number" name="amount" class="form-control" min="1" step="0.01" required>

                    {{-- Payment Method --}}
                    <label class="mt-3">Payment Method</label>
                    <select name="payment_method" class="form-control" id="paymentMethodSelect" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank">Bank Transfer</option>
                        <option value="Others">Others</option>
                    </select>

                    {{-- GCash Details --}}
                    <div id="gcashCollapse" class="collapse mt-3">
                        <div class="card border rounded shadow-sm p-3">
                            <div class="text-center">
                                <label class="fw-bold d-block">Scan GCash QR Code</label>
                                <img src="{{ asset('images/gcash-qr.png') }}" alt="GCash QR" class="img-fluid mb-2" style="max-width: 200px; border: 1px solid #ddd; border-radius: 10px;">
                                <div class="small text-muted mt-2">
                                    📲 Open your GCash app and scan the QR to auto-fill recipient details.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bank Details --}}
                    <div id="bankCollapse" class="collapse mt-3">
                        <div class="card border rounded shadow-sm p-3">
                            <label class="fw-bold">Bank Details</label>
                            <div class="d-flex align-items-start mb-2">
                                <img src="{{ asset('images/bdo-logo.png') }}" alt="BDO Logo" style="max-width: 60px; margin-right: 20px;">
                                <div>
                                    <div><strong>Banco de Oro (BDO)</strong></div>
                                    <div>Account Name: Hugpong Amigos</div>
                                    <div>
                                        Account No: <span id="accountNumber">0123 4567 8901</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 py-0 px-2" onclick="copyAccount()">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Proof of Payment --}}
                    <label class="mt-3">Upload Proof of Payment <small class="text-muted">(optional)</small></label>
                    <input type="file" name="proof" id="proofInput" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <small class="text-muted">Accepted: JPG, PNG, PDF. Max size: 2MB.</small>
                    <div id="proofPreview" class="mt-2"></div>

                    {{-- Note --}}
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

<script>
    // Show/hide GCash or Bank sections
    document.addEventListener('DOMContentLoaded', function () {
        const paymentSelect = document.getElementById('paymentMethodSelect');
        const gcashSection = document.getElementById('gcashCollapse');
        const bankSection = document.getElementById('bankCollapse');

        paymentSelect.addEventListener('change', function () {
            gcashSection.classList.remove('show');
            bankSection.classList.remove('show');

            if (this.value === 'GCash') {
                gcashSection.classList.add('show');
            } else if (this.value === 'Bank') {
                bankSection.classList.add('show');
            }
        });
    });

    function copyAccount() {
        const account = document.getElementById('accountNumber').textContent.trim();
        navigator.clipboard.writeText(account).then(function () {
            alert('Account number copied!');
        });
    }
</script>
