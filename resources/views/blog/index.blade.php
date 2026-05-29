@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-newspaper me-2" style="color:var(--admin-primary)"></i>Blog Posts
    </h5>
    <a href="{{ route('blog.create') }}" class="btn-admin-accent">
        <i class="fas fa-plus me-1"></i> Add Blog
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        @if ($blog->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="gp-mobile-keep" style="width:32px;"></th>
                    <th>Image</th>
                    <th class="gp-mobile-keep">Title</th>
                    <th>Category</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody data-sortable-url="{{ route('admin.sort', 'blogs') }}">
                @foreach ($blog as $post)
                <tr data-id="{{ $post->id }}">
                    <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                    <td>
                        <img src="{{ $post->image_url }}" class="img-thumb" alt="{{ $post->title }}"
                             onerror="this.onerror=null; this.src='{{ asset('images/about.jpg') }}';">
                    </td>
                    <td class="gp-mobile-keep"><strong>{{ $post->title }}</strong></td>
                    <td>
                        @if ($post->category)
                            <a href="{{ route('categories.edit', $post->category->id) }}" class="badge-cat">
                                {{ $post->category->name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('blog.edit', $post->id) }}" class="btn-admin-sm btn-admin-edit"
                               title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('blog.destroy', $post->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this blog post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-admin-sm btn-admin-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="admin-empty">
            <i class="fas fa-pen-alt"></i>
            <h5>No Blog Posts Yet</h5>
            <p>Write your first blog post to engage visitors</p>
        </div>
        @endif
    </div>
</div>
@endsection