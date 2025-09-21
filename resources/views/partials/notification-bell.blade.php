@if(auth()->check() && auth()->user()->role === 'Admin')
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
@endif