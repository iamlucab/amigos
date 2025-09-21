@extends('adminlte::page')
@section('title', 'My Rewards')

@section('content')
<div class="container-fluid py-4 px-3" style="min-height: calc(100vh - 200px);">
    <h4 class="mb-4"><i class="bi bi-person-gift text-primary"></i> My Rewards</h4>

    @if($rewards->isEmpty())
        <div class="alert alert-info w-100 text-center mb-3 py-2 shadow-sm rounded">
            You haven't won any rewards yet.
        </div>
    @else
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Program</th>
                        <th>Description</th>
                        <th>Drawn At</th>
                        <th>Ineligible Until</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rewards as $reward)
                        <tr>
                            <td>{{ optional($reward->program)->title ?? 'N/A' }}</td>
                            <td>{{ optional($reward->program)->description ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($reward->drawn_at)->format('F d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reward->excluded_until)->format('F d, Y') }}</td>
                            <td>
                                @if ($reward->status === 'redeemed')
                                    <span class="badge badge-success">Redeemed</span><br>
                                    <small>{{ \Carbon\Carbon::parse($reward->updated_at)->format('F d, Y') }}</small>
                                @elseif ($reward->status === 'expired')
                                    <span class="badge badge-danger">Expired</span>
                                @else
                                    <span class="badge badge-secondary">Unclaimed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

{{-- Mobile Footer - Sticky at bottom --}}
@include('partials.mobile-footer')

{{-- Additional styling to ensure proper mobile footer positioning --}}
@push('css')
<style>
    /* Ensure content doesn't overlap with sticky footer on mobile */
    @media (max-width: 991px) {
        .content-wrapper {
            padding-bottom: 100px !important;
        }
        
        .container-fluid {
            margin-bottom: 20px;
        }
    }
</style>
@endpush