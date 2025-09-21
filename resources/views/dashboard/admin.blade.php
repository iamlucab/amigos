@extends('layouts.adminlte-base')
@section('title', 'Admin Dashboard')

{{-- E-Bili Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

@section('content_header')
<div class="text-center mb-4 fade-in">
    {{-- Logo Container --}}
    <div class="logo-container mx-auto mb-3" style="width: 80px; height: 80px;">
        <img src="{{ asset('ebili-logo.png') }}" alt="Amigos \'98'" style="width: 80px; height: 80px; object-fit: contain;">
    </div>

    <h2 class="fw-bold mb-2" style="color: var(--primary-purple);">Admin Dashboard</h2>
    <p class="slogan mb-0" style="font-size: 0.9rem;">Manage Your Amigos '98 Community</p>
</div>
@stop

@section('content')

{{-- ✅ Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card fade-in">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);">
                        <i class="bi bi-people-fill fs-1 text-white"></i>
                    </div>
                    <div class="text-start">
                        <h3 class="fw-bold mb-0" style="color: var(--primary-purple);">{{ $totalMembers }}</h3>
                        <p class="text-muted mb-0"><a href="">Registered Members </a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card fade-in">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                        <i class="bi bi-check-circle-fill fs-1 text-white"></i>
                    </div>
                    <div class="text-start">
                        <h3 class="fw-bold mb-0" style="color: var(--primary-purple);">{{ $usedCodes }}</h3>
                        <p class="text-muted mb-0">Used Codes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Admin Action Icons --}}
<div class="mb-4">
    <h4 class="section-title text-center">Admin Tools</h4>
    <div class="row text-center">
        <div class="col-6 col-md-4 mb-4">
            <a href="{{ url('/admin/members') }}" class="text-decoration-none">
                <div class="card fade-in p-4 h-100">
                    <i class="bi bi-people-fill" style="color: var(--primary-purple); font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div class="fw-bold" style="color: var(--primary-purple);">Members</div>
                    <small class="text-muted">Manage member accounts</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 mb-4">
            <a href="{{ url('/admin/cashin-approvals') }}" class="text-decoration-none">
                <div class="card fade-in p-4 h-100">
                    <i class="bi bi-wallet2" style="color: #28a745; font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div class="fw-bold" style="color: var(--primary-purple);">Cash In</div>
                    <small class="text-muted">Approve cash requests</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 mb-4">
            <a href="{{ url('/admin/membership-codes') }}" class="text-decoration-none">
                <div class="card fade-in p-4 h-100">
                    <i class="bi bi-qr-code" style="color: var(--dark-purple); font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div class="fw-bold" style="color: var(--primary-purple);">Codes</div>
                    <small class="text-muted">Membership codes</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 mb-4">
            <a href="{{ url('/genealogy') }}" class="text-decoration-none">
                <div class="card fade-in p-4 h-100">
                    <i class="bi bi-diagram-3" style="color: #17a2b8; font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div class="fw-bold" style="color: var(--primary-purple);">Genealogy</div>
                    <small class="text-muted">Network structure</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 mb-4">
            <a href="{{ route('referral.report') }}" class="text-decoration-none">
                <div class="card fade-in p-4 h-100">
                    <i class="bi bi-graph-up" style="color: #ffc107; font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div class="fw-bold" style="color: var(--primary-purple);">Reports</div>
                    <small class="text-muted">Analytics & insights</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4 mb-4">
            <a href="{{ route('wallet.index') }}" class="text-decoration-none">
                <div class="card fade-in p-4 h-100">
                    <i class="bi bi-wallet2" style="color: var(--secondary-purple); font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div class="fw-bold" style="color: var(--primary-purple);">Wallets</div>
                    <small class="text-muted">Wallet management</small>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- ✅ Active Referral Configuration --}}
<div class="col-12 mb-4">
    <div class="card fade-in">
        <div class="card-header">
            <h4 class="card-title text-white fw-bold mb-0">
                <i class="bi bi-gear-fill me-2"></i>Active Referral Configuration
            </h4>
        </div>
        <div class="card-body">
            @php
                $config = \App\Models\ReferralConfiguration::getActive();
            @endphp

            @if ($config)
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold" style="color: var(--primary-purple);">{{ $config->name }}</h5>
                        <p class="text-muted">{{ $config->description }}</p>
                        <div class="mb-3">
                            <span class="badge bg-success rounded-pill me-2">
                                <i class="bi bi-cash me-1"></i>
                                Total: ₱{{ number_format($config->total_allocation, 2) }}
                            </span>
                            <span class="badge bg-info rounded-pill">
                                <i class="bi bi-layers me-1"></i>
                                Max Level: {{ $config->max_level }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3" style="color: var(--primary-purple);">Bonus Distribution</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="color: var(--primary-purple);">Level</th>
                                        <th style="color: var(--primary-purple);">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($config->getAllBonuses() as $level => $amount)
                                        <tr>
                                            <td class="fw-bold">Level {{ $level }}</td>
                                            <td class="price-tag">₱{{ number_format($amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.referral-configurations.index') }}" class="btn btn-primary">
                        <i class="bi bi-gear me-2"></i> Manage Configurations
                    </a>
                </div>
            @else
                <div class="alert alert-warning slide-up">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle fs-1 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1"> No Active Configuration</h6>
                            <p class="mb-0"> No active referral configuration found.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.referral-configurations.create') }}" class="btn btn-warning">
                            <i class="bi bi-plus me-2"></i>Create Configuration
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

</div>

@php
    $versionFile = base_path('version.txt');
    $version = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'v1.0.0';
@endphp

<div style="position: fixed; bottom: 5px; right: 10px; font-size: 0.8rem; color: #888;">
    Version {{ $version }}
</div>
@stop


{{-- @include('partials.mobile-footer') --}}
