<li class="nav-item">
    <a href="#" class="nav-link" onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right nav-icon"></i>
        <p>Logout</p>
    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</li>