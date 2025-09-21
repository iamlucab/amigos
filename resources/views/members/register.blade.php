@extends('adminlte::page')

@section('title', 'Register Member')

@section('content_header')
    <h5>Register an Amigo</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('member.store') }}" method="POST" enctype="multipart/form-data" id="registerForm">
        @csrf

        <div class="row">
            {{-- First Name --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Middle Name --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control">
                    @error('middle_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Last Name --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required>
                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Birthday --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" name="birthday" class="form-control" required>
                    @error('birthday') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Mobile Number --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="mobile_number">Mobile Number <small class="text-muted">This mobile number is also his/her username. and the default password: is <code>secret123</code>.</small></label>
                    <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                           maxlength="11" pattern="\d{11}" title="Must be exactly 11 digits"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                    <small id="mobileFeedback" class="text-danger"></small>
                    @error('mobile_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Occupation --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="occupation">Occupation</label>
                    <input type="text" name="occupation" class="form-control">
                    @error('occupation') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Membership Code --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="membership_code">Membership Code</label>&nbsp;<small class="text-muted">(Pay and get it from the Admin.)</small>
                    <input type="text" name="membership_code" class="form-control" required>
                    @error('membership_code') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Sponsor --}}
            @if(auth()->user()->role !== 'Member')
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sponsor_id">Sponsor</label>
                        <select name="sponsor_id" class="form-control" required>
                            <option value="">Select Sponsor</option>
                            @foreach($sponsors as $sponsor)
                                <option value="{{ $sponsor->id }}">
                                    {{ $sponsor->first_name }} {{ $sponsor->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            {{-- Role --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="role">Select Role</label>
                    @if(auth()->check() && auth()->user()->role === 'Member')
                        <input type="text" class="form-control" value="Member" readonly>
                        <input type="hidden" name="role" value="Member">
                    @elseif(auth()->check())
                        <select name="role" class="form-control" required>
                            <option value="Member" {{ old('role') === 'Member' ? 'selected' : '' }}>Member</option>
                            <option value="Staff" {{ old('role') === 'Staff' ? 'selected' : '' }}>Staff</option>
                            <option value="Admin" {{ old('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    @else
                        <input type="hidden" name="role" value="Member">
                    @endif
                    @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Photo Upload --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="photo">Upload Photo</label>
                    <input type="file" name="photo" class="form-control">
                    @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-md-12 mt-3">
                <button class="btn btn-primary w-100 rounded-pill" id="submitBtn">Register</button>
            </div>
        </div>
    </form>
@stop

{{-- @include('partials.footer') --}}
@include('partials.mobile-footer')

@section('js')
<script>
    document.getElementById('mobile_number').addEventListener('blur', function() {
        const mobile = this.value;
        const feedback = document.getElementById('mobileFeedback');

        if (mobile.length === 11) {
            fetch(`/check-mobile?mobile_number=${mobile}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        feedback.textContent = 'Mobile number already registered and approved.';
                        document.getElementById('submitBtn').disabled = true;
                    } else {
                        feedback.textContent = '';
                        document.getElementById('submitBtn').disabled = false;
                    }
                });
        } else {
            feedback.textContent = '';
        }
    });
</script>
@endsection


