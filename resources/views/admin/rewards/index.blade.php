@extends('adminlte::page')

@section('title', 'Reward Programs')

@section('content_header')
    <h4>Reward Programs</h4>
@stop

@section('content')
    <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary mb-3">Create New Reward Program</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Draw Date</th>
                    <th>Created At</th>
               
                    <th>Draw</th>
                         <th>Winner(s)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $program)
                    <tr>
                        <td>{{ $program->title }}</td>
                        <td>{{ $program->description }}</td>
                        <td>{{ $program->draw_date->toFormattedDateString() }}</td>
                        <td>{{ $program->created_at->toDateString() }}</td>
                        <td>
 <td>
    @if($program->winners->count())
        <ul class="mb-0 pl-3">
            @foreach($program->winners as $winner)
                <li>
                    {{ $winner->member->first_name }} {{ $winner->member->last_name }}<br>
                    <small>{{ $winner->member->mobile_number }}</small>
                </li>
            @endforeach
        </ul>
    @else
    <span class="badge text-bg-secondary d-block w-100 text-center">No winners yet</span>
    @endif
</td>
                        <td>


@if($program->winners->isEmpty())
 <form action="{{ route('admin.rewards.pick', $program->id) }}" method="POST" onsubmit="return confirm('Pick random winners?');">
    @csrf
    <div class="input-group input-group-sm mb-2">
        <input type="number" name="count" class="form-control" value="1" min="1" max="50" style="max-width: 70px;" required>
        <button type="submit" class="btn btn-warning">Pick Winners</button>
    </div>
</form>
    @else
        <span class="text-success">Winner Picked</span>
    @endif
</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

@include('partials.mobile-footer')

