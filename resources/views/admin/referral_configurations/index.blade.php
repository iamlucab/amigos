@extends('adminlte::page')

@section('title', 'Referral Configurations')

@section('content_header')
    <h5>Referral Configurations</h5>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.referral-configurations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Create New Configuration
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total Allocation</th>
                            <th>Max Level</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($configurations as $config)
                            <tr>
                                <td>{{ $config->name }}</td>
                                <td>₱{{ number_format($config->total_allocation, 2) }}</td>
                                <td>{{ $config->max_level }}</td>
                                <td>
                                    @if ($config->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $config->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.referral-configurations.edit', $config) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        
                                        @if (!$config->is_active)
                                            <form action="{{ route('admin.referral-configurations.activate', $config) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check"></i> Activate
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.referral-configurations.destroy', $config) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No configurations found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function() {
            $('.delete-form').on('submit', function(e) {
                if (!confirm('Are you sure you want to delete this configuration?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@stop