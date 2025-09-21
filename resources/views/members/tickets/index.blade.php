@extends('adminlte::page')

@section('title', 'My Tickets - Help Desk')

@section('content_header')
    <h5 class="fw-bold mb-0"><i class="bi bi-life-preserver me-2"></i> Help Desk</h5>
@stop

@section('content')

    {{-- Submit Ticket Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header text-white">
            <strong class="text-white"><i class="bi bi-plus-circle me-1"></i>&nbsp; Submit a New Ticket</strong><p class= "txt-white small" style="color: #fff !important" >If you have any concern, and inquiries just send us here your message</p>
        </div>
        <div class="card-body">
            <form action="{{ route('member.tickets.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="fw-semibold">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Enter your ticket subject" required>
                </div>
                <div class="form-group mb-3">
                    <label class="fw-semibold">Message</label>
                    <textarea name="message" rows="4" class="form-control" placeholder="Describe your issue or request" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send me-1"></i> Submit Ticket
                </button>
            </form>
        </div>
    </div>

    {{-- My Tickets --}}
    <div class="card shadow-sm text-white ">
        <div class="card-header bg-primary text-white">
            <strong class="text-white"><i class="bi bi-ticket-detailed me-1 text-white "></i> My Tickets</strong>
        </div>
        <div class="card-body p-0">

            {{-- Desktop Table --}}
            <div class="d-none d-md-block">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="fw-semibold">{{ $ticket->subject }}</td>
                                <td>
                                    <span class="{{ $ticket->status_badge_class }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at->format('F d, Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('member.tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No tickets yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile PWA-Style Cards --}}
            <div class="d-block d-md-none p-2">
                @forelse($tickets as $index => $ticket)
                    <div class="ticket-card border rounded p-3 mb-2 shadow-sm"
                         style="animation-delay: {{ $index * 0.08 }}s">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-bold text-primary">{{ $ticket->subject }}</div>
                            <span class="{{ $ticket->status_badge_class }}">
                                {{ $ticket->status_label }}
                            </span>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ $ticket->created_at->format('F d, Y') }}
                        </small>
                        <div class="mt-2 text-end">
                            <a href="{{ route('member.tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted p-3">
                        No tickets yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Animation Styles --}}
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .ticket-card {
            animation: fadeInUp 0.35s ease forwards;
            background: #fff;
            border-radius: 12px;
        }
        .ticket-card:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }
        textarea, input {
            font-size: 0.95rem;
        }
    </style>

    @include('partials.mobile-footer')

@endsection

@section('js')
    {{-- Toastr Notifications --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endsection
