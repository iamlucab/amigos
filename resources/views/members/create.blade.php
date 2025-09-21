@extends('adminlte::page')

@section('content')
<div class="card card-body">
    <h3 class="text-center">Register a Member</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('member.store') }}" enctype="multipart/form-data">
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

        <div class="form-group">
            <label>Photo (optional)</label>
            <input type="file" name="photo" class="form-control">
        </div>

        <div class="form-group">
            <label for="sponsor_id">Select Sponsor</label>
            <select name="sponsor_id" class="form-control" required>
                <option value="">-- Choose Sponsor --</option>
                @foreach($sponsors->where('status', 'Approved') as $sponsor)
                    <option value="{{ $sponsor->id }}">
                        {{ $sponsor->first_name }} {{ $sponsor->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="membership_code">Membership Code <span class="text-danger">*</span></label>
            <select name="membership_code" class="form-control" required>
                <option value="">-- Select Membership Code --</option>
                @foreach(\App\Models\MembershipCode::where('used', false)->get() as $code)
                    <option value="{{ $code->code }}">{{ $code->code }}</option>
                @endforeach
            </select>
            <small class="text-muted">Required for all members</small>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
            </select>
        </div>

<input type="hidden" name="role" value="Member">

        <button type="submit" class="btn btn-primary btn-block mt-2">Register</button>
    </form>
</div>
@endsection
@include('partials.mobile-footer')