@extends('adminlte::page')

@section('title', 'My Profile')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
@endsection

@section('content_header')
    <h1>My Profile</h1>
@stop

@section('content')
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Profile Photo --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label>Profile Photo</label><br>
                    @if($member && $member->photo)
<img src="{{ asset('storage/photos/' . $member->photo) }}" width="100" class="img-thumbnail mb-2">
                    @else
                        <img id="photo-preview" src="{{ asset('images/default-profile.png') }}" alt="photo" width="100" class="img-thumbnail mb-2">
                    @endif
                    <input type="file" name="photo" class="form-control" onchange="previewPhoto(this)">
                </div>
            </div>

            <div class="col-md-8">
                <div class="row">
                    {{-- First Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name</label>
                            <input name="first_name" class="form-control" value="{{ old('first_name', $member->first_name ?? '') }}" required>
                        </div>
                    </div>

                    {{-- Middle Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input name="middle_name" class="form-control" value="{{ old('middle_name', $member->middle_name ?? '') }}">
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name</label>
                            <input name="last_name" class="form-control" value="{{ old('last_name', $member->last_name ?? '') }}" required>
                        </div>
                    </div>

                    {{-- Birthday --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Birthday</label>
                            <input type="date" name="birthday" class="form-control" value="{{ old('birthday', $member->birthday ?? '') }}" required>
                        </div>
                    </div>

                    {{-- Mobile Number --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input name="mobile_number" class="form-control" value="{{ old('mobile_number', $member->mobile_number ?? '') }}" required>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control"
                                value="{{ old('email', $member && $member->user ? $member->user->email : '') }}" required>
                        </div>
                    </div>

                    {{-- Occupation --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Occupation</label>
                            <input name="occupation" class="form-control" value="{{ old('occupation', $member->occupation ?? '') }}">
                        </div>
                    </div>

                    {{-- Address (Full Width) --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Address</label>
                            <input name="address" class="form-control" value="{{ old('address', $member->address ?? '') }}">
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>New Password <small class="text-muted">(Leave blank to keep old password)</small></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="toggle-icon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
            </div>
        </div>
    </form>
@stop
@include('partials.mobile-footer')
@section('js')
    {{-- Dependencies --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- Success Message --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                toastr.success("{{ session('success') }}", "Success", {
                    timeOut: 3000,
                    progressBar: true,
                    positionClass: 'toast-top-right'
                });
            });
        </script>
    @endif

    {{-- JS Utilities --}}
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function previewPhoto(input) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('photo-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endsection

{{-- @include('partials.footer') --}}
