@extends('adminlte::page')

@section('title', 'Create Reward Program')

@section('content_header')
    <h5>Create New Reward Program</h5>
@stop

@section('content')
    <form action="{{ route('admin.rewards.store') }}" method="POST">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>There were some issues:</strong>
                <ul>
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label for="title">Program Title</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>

        <div class="form-group">
            <label for="description">Program Description</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>
<div class="form-group">
    <label>Number of Winners</label>
    <input type="number" name="winner_count" class="form-control" min="1" value="1">
</div>
        <div class="form-group">
            <label for="draw_date">Draw Date</label>
            <input type="date" name="draw_date" class="form-control" required value="{{ old('draw_date') }}">
        </div>

        <button type="submit" class="btn btn-success">Create Program</button>
        <a href="{{ route('admin.rewards.index') }}" class="btn btn-secondary" style="color:white !important ">Back</a>
    </form>
@stop

@include('partials.mobile-footer')