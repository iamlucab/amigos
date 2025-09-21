@extends('adminlte::page')

@section('title', 'Reward Programs')

@section('content_header')
    <h1>Reward Programs</h1>
@stop

@section('content')
    <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary mb-3">Create New Reward Program</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Desktop Table --}}
    <div class="card d-none d-md-block">
        <div class="card-body table-responsive">
            <table id="rewards-table" class="table table-bordered table-hover table-striped dt-responsive nowrap" style="width:100%">
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
                                @if($program->winners->isEmpty())
                                    <form action="{{ route('admin.rewards.pick', $program->id) }}" method="POST" onsubmit="return confirm('Pick random winners?');">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="count" class="form-control" value="1" min="1" max="50" style="max-width: 60px;" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-warning btn-sm" type="submit">Pick</button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <span class="text-success">Picked</span>
                                @endif
                            </td>
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
                                    <span class="badge badge-secondary d-block w-100 text-center">No winners yet</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.rewards.edit', $program->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="d-md-none">
        @foreach($programs as $program)
            <div class="card mb-3">
                <div class="card-body">
                    <h5>{{ $program->title }}</h5>
                    <p class="mb-1"><strong>Description:</strong> {{ $program->description }}</p>
                    <p class="mb-1"><strong>Draw Date:</strong> {{ $program->draw_date->toFormattedDateString() }}</p>
                    <p class="mb-1"><strong>Created:</strong> {{ $program->created_at->toDateString() }}</p>
                    <p class="mb-1"><strong>Winner(s):</strong><br>
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
                            <span class="badge badge-secondary">No winners yet</span>
                        @endif
                    </p>

                    @if($program->winners->isEmpty())
                        <form action="{{ route('admin.rewards.pick', $program->id) }}" method="POST" onsubmit="return confirm('Pick random winners?');">
                            @csrf
                            <div class="input-group input-group-sm mb-2" style="max-width: 200px;">
                                <input type="number" name="count" class="form-control" value="1" min="1" max="50" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-warning btn-sm">Pick</button>
                                </div>
                            </div>
                        </form>
                    @else
                        <span class="text-success">Picked</span>
                    @endif

                    <a href="{{ route('admin.rewards.edit', $program->id) }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(function () {
            $('#rewards-table').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: true,
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search rewards..."
                }
            });
        });
    </script>
@stop
