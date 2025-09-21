{{-- AdminLTE Footer Content --}}
@section('footer')
<div class="d-none d-lg-block footer-desktop">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="text-primary">Amigos 98 Community</h5>
                <p class="small text-muted">
                    A membership platform with configurable referral and cashback systems.
                    Empowering communities through shared prosperity.
                </p>
            </div>
            
            <div class="col-md-2 mb-3">
                <h6 class="text-primary">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('member.dashboard') }}" class="text-muted">Dashboard</a></li>
                    <li><a href="{{ route('shop.index') }}" class="text-muted">Shop</a></li>
                    <li><a href="{{ route('wallet.history', ['type' => 'main']) }}" class="text-muted">Wallet</a></li>
                    <li><a href="{{ route('genealogy.index') }}" class="text-muted">Network</a></li>
                </ul>
            </div>
            
            <div class="col-md-3 mb-3">
                <h6 class="text-primary">Support</h6>
                <ul class="list-unstyled small">
                    <li><a href="#" class="text-muted">Help Center</a></li>
                    <li><a href="#" class="text-muted">Contact Us</a></li>
                    <li><a href="#" class="text-muted">Terms of Service</a></li>
                    <li><a href="#" class="text-muted">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div class="col-md-3 mb-3">
                <h6 class="text-primary">Connect With Us</h6>
                <div class="d-flex gap-2 mb-2">
                    <a href="#" class="text-muted"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-muted"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
                <p class="small text-muted">
                    <i class="bi bi-envelope me-1"></i> support@ebili.com<br>
                    <i class="bi bi-telephone me-1"></i> +63 123 456 7890
                </p>
            </div>
        </div>
        
        <hr class="my-2">
        
        <div class="text-center small text-muted">
            &copy; {{ date('Y') }} Amigos 98 Community. All rights reserved.
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
