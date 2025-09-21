{{-- ✅ Wallet Balance --}}
<div class="bg-primary text-white rounded-lg p-3 shadow-sm text-center mb-4">
    <small class="text-uppercase" style="font-size: 75%;">Available Balance</small><br>
    @isset($wallet)
        <h3 class="fw-bold mt-1">₱{{ number_format($wallet->balance, 2) }}</h3>
    @else
        <h3 class="fw-bold mt-1">₱0.00</h3>
    @endisset
</div>

{{-- ✅ Dashboard Action Icons --}}
<div class="row text-center mb-4 gx-2 gy-3">
    {{-- Send --}}
    <div class="col-4">
        <a href="#" data-toggle="modal" data-target="#sendModal" class="text-decoration-none text-dark d-block" role="button">
            <div class="card shadow-sm p-3">
                <i class="bi bi-send fa-2x text-primary mb-2"></i>
                <div class="small">Send</div>
            </div>
        </a>
    </div>

    {{-- Borrow --}}
    <div class="col-4">
        @if(auth()->user()->member->loan_eligible)
            <a href="#" data-toggle="modal" data-target="#borrowModal" class="text-decoration-none text-dark d-block" role="button">
                <div class="card shadow-sm p-3">
                    <i class="bi bi-cash-coin fa-2x text-danger mb-2"></i>
                    <div class="small">Borrow</div>
                </div>
            </a>
        @else
            <a href="javascript:void(0)" class="text-decoration-none text-muted d-block" tabindex="0" data-toggle="popover" data-trigger="focus" title="Notice!" data-content="You are currently not eligible to borrow.">
                <div class="card shadow-sm p-3" style="opacity: 0.5;">
                    <i class="bi bi-cash-coin fa-2x mb-2"></i>
                    <div class="small">Borrow</div>
                </div>
            </a>
        @endif
    </div>

    {{-- Cash In --}}
    <div class="col-4">
        <a href="#" data-toggle="modal" data-target="#cashinModal" class="text-decoration-none text-dark d-block" role="button">
            <div class="card shadow-sm p-3">
                <i class="bi bi-wallet2 fa-2x text-secondary mb-2"></i>
                <div class="small">Cash In</div>
            </div>
        </a>
    </div>

    {{-- Network --}}
    <div class="col-4">
        <a href="{{ route('genealogy.index') }}" class="text-decoration-none text-dark d-block">
            <div class="card shadow-sm p-3">
                <i class="bi bi-diagram-3 fa-2x text-info mb-2"></i>
                <div class="small">Network</div>
            </div>
        </a>
    </div>

    {{-- Bills --}}
    <div class="col-4">
        <a href="javascript:void(0)" onclick="toastr.info('Coming soon!')" class="text-decoration-none text-dark d-block">
            <div class="card shadow-sm p-3">
                <i class="bi bi-receipt fa-2x text-warning mb-2"></i>
                <div class="small">Bills</div>
            </div>
        </a>
    </div>

    {{-- Register --}}
    <div class="col-4">
        <a href="{{ route('member.register.form') }}" class="text-decoration-none text-dark d-block">
            <div class="card shadow-sm p-3">
                <i class="bi bi-person-plus fa-2x text-success mb-2"></i>
                <div class="small">Register</div>
            </div>
        </a>
    </div>
</div>
