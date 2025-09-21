@extends('adminlte::page')
@section('title', 'Edit Member')

@section('content_header')
    <h1>Edit Member</h1>
@stop

@section('content')
    <form action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('members.partials.form', ['member' => $member, 'sponsors' => $sponsors])

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('members.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
@stop

@section('js')
    <script>
        $(function () {
            // Initialize any tooltips if needed
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
