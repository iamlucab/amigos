<div class="modal fade" id="merchantRegisterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('member.merchant.register') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Become a Merchant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="shop_name">Shop Name</label>
                        <input type="text" name="shop_name" id="shop_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="mobile_number">Mobile Number (Username)</label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                               maxlength="11" minlength="11"
                               pattern="^09\d{9}$"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required
                               placeholder="e.g. 09123456789">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="product_line">Product Line</label>
                        <input type="text" name="product_line" id="product_line" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Registration</button>
                </div>
            </div>
        </form>
    </div>
</div>
