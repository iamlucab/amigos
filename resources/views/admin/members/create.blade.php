@extends('adminlte::page')
<style>
.upload-btn {
    font-size: 0.9rem;
    transition: all 0.3s ease;
}
.upload-btn:hover {
    background-color: #007bff;
    color: white;
}
</style>

@section('content')
<div class="card card-body">
    <h4 class="text-center">Register a Friend </h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<form method="POST" action="{{ route('admin.members.store') }}" enctype="multipart/form-data">
    @csrf

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Middle Name (optional)</label>
            <input type="text" name="middle_name" class="form-control">
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Birthday</label>
            <input type="date" name="birthday" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" name="mobile_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Occupation</label>
            <input type="text" name="occupation" class="form-control">
        </div>
<div class="form-group text-center">
    <label for="photo" class="d-block mb-2 font-weight-bold">Profile Photo (Optional)</label>

    <label for="photo" class="upload-btn btn btn-outline-primary rounded-pill px-4 py-2">
        <i class="bi bi-camera"></i> Choose Photo
    </label>
    
    <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewPhoto(event)">
    
    <!-- Preview Image -->
    <div class="mt-3">
        <img id="photoPreview" src="{{ asset('images/default-profile.png') }}" alt="Preview" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
    </div>
</div>
        <div class="form-group">
            <label for="sponsor_id">Select Sponsor</label>
            <select name="sponsor_id" class="form-control" required>
                <option value="">-- Choose Sponsor --</option>
                @foreach($sponsors as $sponsor)
                    <option value="{{ $sponsor->id }}">
                        {{ $sponsor->first_name }} {{ $sponsor->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
<input type="hidden" name="role" value="member">

        <button type="submit" class="btn btn-primary w-100 rounded-pill mt-2">Register</button>
    </form>
</div>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
@include('partials.mobile-footer')