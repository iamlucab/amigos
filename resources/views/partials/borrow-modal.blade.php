{{-- ✅ Borrow Modal --}}
<div class="modal fade" id="borrowModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('loan.request') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Borrow Money</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div id="monthlyPreview" class="mt-3 d-none">
                        <div class="alert alert-info small mb-0">
                            Estimated Monthly Payment: <strong id="monthlyAmount">₱0.00</strong>
                        </div>
                    </div>

                    @if ($errors->any() && old('_modal') === 'borrow')
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <input type="hidden" name="_modal" value="borrow">

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
