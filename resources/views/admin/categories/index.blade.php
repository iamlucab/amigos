@extends('adminlte::page')

@section('title', 'Product Categories')

@section('content_header')
    <h4 class="mb-3">🗂️ Product Categories</h4>
@endsection

@section('content')
    {{-- ✅ Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- ✅ Add New Category Button --}}
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">
        <i class="bi bi-plus"></i> Add Category
    </button>

    {{-- ✅ Category Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="bg-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th style="width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px; border-radius: 4px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $category->name }}</td>
                        <td>
                            {{-- ✏️ Edit --}}
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal{{ $category->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>

                            {{-- 🗑️ Delete --}}
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Add Category Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name..." required>
                    </div>
                    <div class="form-group">
                        <label>Category Icon/Image</label>
                        <input type="file" name="image" class="form-control-file" accept="image/*">
                        <small class="text-muted">Upload an icon or image for this category (optional)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ Edit Modals (after table for clarity) --}}
    @foreach($categories as $category)
        <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $category->id }}">Edit Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Category Icon/Image</label>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                         class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    <small class="d-block text-muted">Current image</small>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                            <small class="text-muted">Upload a new icon/image to replace the current one (optional)</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- 📱 Reusable Mobile Footer --}}
    @include('partials.mobile-footer')

@endsection
