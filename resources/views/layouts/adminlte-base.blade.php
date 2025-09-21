@extends('adminlte::page')

{{-- Include logout form in all AdminLTE pages --}}
@section('adminlte_js')
    @parent
    @include('partials.logout-form')

    <script>
        // Global AJAX error handler for 419 (CSRF token mismatch) errors
        $(document).ajaxError(function(event, xhr, settings) {
            if (xhr.status === 419) {
                // Redirect to welcome page when CSRF token expires
                window.location.href = '/welcome.php';
            }
        });

        // Also handle fetch API errors
        const originalFetch = window.fetch;
        window.fetch = function() {
            return originalFetch.apply(this, arguments).then(function(response) {
                if (response.status === 419) {
                    window.location.href = '/welcome.php';
                }
                return response;
            });
        };
    </script>
@stop

{{-- Ensure CSRF token is available --}}
@section('adminlte_css_pre')
    @parent
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

{{-- Add notification bell to admin panel --}}
@section('adminlte_navbar')
    @parent
    @if(auth()->check() && auth()->user()->role === 'Admin')
        <!-- Notification Bell Debug: User is Admin -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-bell"></i>
                @php
                    // Calculate total notifications
                    $membershipRequestsCount = \App\Models\MembershipCodeRequest::where('status', 'pending')->count();
                    $cashinRequestsCount = \App\Models\CashInRequest::where('status', 'pending')->count();
                    $loanRequestsCount = \App\Models\Loan::where('status', 'pending')->count();
                    $productOrdersCount = \App\Models\Order::where('status', 'pending')->count();
                    $helpdeskMessagesCount = \App\Models\Ticket::where('status', 'open')->count();
                    $notificationCount = $membershipRequestsCount + $cashinRequestsCount + $loanRequestsCount + $productOrdersCount + $helpdeskMessagesCount;
                @endphp
                @if($notificationCount > 0)
                    <span class="badge badge-warning navbar-badge">{{ $notificationCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">{{ $notificationCount }} Pending Approvals</span>
                <div class="dropdown-divider"></div>

                <a href="{{ route('admin.membership-code-requests.index') }}" class="dropdown-item">
                    <i class="fas fa-id-card mr-2"></i>
                    <span class="float-right badge badge-warning">{{ $membershipRequestsCount }}</span>
                    Membership Code Requests
                </a>

                <a href="{{ route('admin.cashin.index') }}" class="dropdown-item">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    <span class="float-right badge badge-warning">{{ $cashinRequestsCount }}</span>
                    Cash In Requests
                </a>

                <a href="{{ route('admin.loans.management') }}" class="dropdown-item">
                    <i class="fas fa-hand-holding-usd mr-2"></i>
                    <span class="float-right badge badge-warning">{{ $loanRequestsCount }}</span>
                    Loan Applications
                </a>

                <a href="{{ route('admin.orders.index') }}" class="dropdown-item">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    <span class="float-right badge badge-warning">{{ $productOrdersCount }}</span>
                    Product Orders
                </a>

                <a href="{{ route('admin.tickets.index') }}" class="dropdown-item">
                    <i class="fas fa-headset mr-2"></i>
                    <span class="float-right badge badge-warning">{{ $helpdeskMessagesCount }}</span>
                    Helpdesk Messages
                </a>

                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">
                    <i class="fas fa-bell mr-2"></i> View All Notifications
                </a>
            </div>
        </li>
    @else
        <!-- Notification Bell Debug: User is not Admin or not logged in -->
    @endif
@stop
