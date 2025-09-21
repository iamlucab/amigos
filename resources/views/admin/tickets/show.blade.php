@extends('adminlte::page')

@section('title', 'Ticket Detail')

@section('content_header')
    <h3>Support Ticket</h3>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <style>
    .chat-bubble {
        position: relative;
        border-radius: 20px;
        padding: 12px 16px;
        max-width: 75%;
        word-wrap: break-word;
    }

    /* Admin Bubble */
    .admin-bubble {
        background-color: #dcefc0 !important;
    }

    /* Member Bubble */
    .member-bubble {
        background-color: #e9f7ff !important;
    }

    /* Pointer Tail - Admin (left) */
    .admin-bubble::after {
        content: "";
        position: absolute;
        top: 15px;
        left: -10px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-right: 10px solid #dcefc0;
        border-bottom: 10px solid transparent;
    }

    /* Pointer Tail - Member (right) */
    .member-bubble::after {
        content: "";
        position: absolute;
        top: 15px;
        right: -10px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-left: 10px solid #d8f1ff;
        border-bottom: 10px solid transparent;
    }
</style>

@endsection

@section('content')

    {{-- Ticket Info --}}
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>From:</strong> {{ $ticket->member->full_name }}</p>
            <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
            <p><strong>Message:</strong> {{ $ticket->message }}</p>
            <p><strong>Status:</strong> 
                <span class="{{ $ticket->status_badge_class }}">
                    {{ $ticket->status_label }}
                </span>
            </p>
        </div>
    </div>

    {{-- Conversation --}}
    <div class="card mb-3">
        <div class="card-header">Conversation</div>
        <div class="card-body" style="background: #f8f9fa;">

@forelse ($ticket->replies as $reply)
    @php
    $isAdmin = $reply->user->role === 'Admin';

    $defaultAvatar = $isAdmin 
        ? asset('images/admin-avatar.png') 
        : asset('images/user-avatar.png');

    $avatar = ($reply->user->member && $reply->user->member->photo)
        ? asset('storage/photos/' . $reply->user->member->photo)
        : $defaultAvatar;

    $bubbleClass = $isAdmin ? 'admin-bubble' : 'member-bubble';
    $alignClass = $isAdmin ? 'justify-content-start' : 'justify-content-end';
    $flexDirection = $isAdmin ? '' : 'flex-row-reverse';
@endphp

    <div class="d-flex mb-3 {{ $alignClass }}">
        <div class="d-flex align-items-end {{ $flexDirection }}">
            
           <img src="{{ $avatar }}" class="rounded-circle" width="50" height="50" alt="Avatar">
            
            <div class="mx-2 chat-bubble {{ $bubbleClass }}">
                <div class="fw-bold mb-1">
                    {{ $reply->user->name }}
                    <span class="badge {{ $isAdmin ? 'bg-info' : 'bg-success' }}">
                        {{ $reply->user->role }}
                    </span>
                </div>
                <div>{!! nl2br(e($reply->message)) !!}</div>
                <small class="text-muted d-block mt-1">{{ $reply->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
@empty
    <p class="text-muted">No replies yet.</p>
@endforelse


        </div>
    </div>




    {{-- Reply Form --}}
    @if($ticket->status !== 'closed')
        <div class="card">
            <div class="card-header"><strong>Reply to Ticket</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="message">Your Reply</label>
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
    @endif

@stop

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endsection
@include('partials.mobile-footer')