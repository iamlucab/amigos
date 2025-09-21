{{-- Hidden logout form for AdminLTE menu --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>