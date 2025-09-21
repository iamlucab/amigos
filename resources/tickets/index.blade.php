@extends('adminlte::page')

@section('title', 'All Support Tickets')

@section('content_header')
    <h1>All Support Tickets</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->member->full_name ?? 'N/A' }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->message }}</td>
                            <td>
                                <span class="{{ $ticket->status_badge_class }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('F d, Y') }}</td>
                            <td class="d-flex flex-column gap-1">
                                {{-- Status dropdown --}}
                                <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="mb-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_process" {{ $ticket->status == 'in_process' ? 'selected' : '' }}>In Process</option>
                                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </form>

                                {{-- Reply button --}}
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary">
                                    Reply
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No tickets submitted.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@include('partials.footer')

@section('js')
    {{-- Toastr Notification --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@stop
