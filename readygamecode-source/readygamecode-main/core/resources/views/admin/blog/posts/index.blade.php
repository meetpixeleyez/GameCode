@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">@lang('Blog Posts')</h5>
            </div>
            <div class="card-body px-0">
                <div class="table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('Title')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Updated')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ $post->category?->name ?? '-' }}</td>
                                    <td><span class="badge {{ $post->is_published ? 'badge--success' : 'badge--warning' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span></td>
                                    <td>{{ $post->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <button class="btn btn--primary btn--sm editPost"
                                            data-id="{{ $post->id }}"
                                            data-title="{{ $post->title }}"
                                            data-slug="{{ $post->slug }}"
                                            data-excerpt="{{ e($post->excerpt) }}"
                                            data-body='@json($post->body)'
                                            data-category="{{ $post->blog_category_id }}"
                                            data-published="{{ (int)$post->is_published }}"
                                        >@lang('Edit')</button>
                                        <form class="d-inline" method="post" action="{{ route('admin.blog.posts.delete', $post->id) }}">
                                            @csrf
                                            <button class="btn btn--danger btn--sm" onclick="return confirm('Delete post?')">@lang('Delete')</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">@lang('No posts')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title postFormTitle">@lang('Add Post')</h5></div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.blog.posts.store') }}" enctype="multipart/form-data" id="postForm">
                    @csrf
                    <input type="hidden" name="id" id="post_id" />
                    <div class="mb-3">
                        <label class="form-label">@lang('Title')</label>
                        <input type="text" class="form-control" name="title" id="post_title" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Slug')</label>
                        <input type="text" class="form-control" name="slug" id="post_slug" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Category')</label>
                        <select class="form-control" name="blog_category_id" id="post_category">
                            <option value="">@lang('None')</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Excerpt')</label>
                        <textarea class="form-control" name="excerpt" id="post_excerpt" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Body')</label>
                        <textarea class="form-control" name="body" id="post_body" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Cover Image')</label>
                        <input type="file" class="form-control" name="cover_image" accept="image/*" />
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="post_published" name="is_published">
                        <label class="form-check-label" for="post_published">@lang('Publish')</label>
                    </div>
                    <button class="btn btn--primary w-100">@lang('Save')</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    (function(){
        document.querySelectorAll('.editPost').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.querySelector('.postFormTitle').innerText = 'Edit Post';
                document.getElementById('post_id').value = this.dataset.id;
                document.getElementById('post_title').value = this.dataset.title;
                document.getElementById('post_slug').value = this.dataset.slug;
                document.getElementById('post_excerpt').value = this.dataset.excerpt ?? '';
                document.getElementById('post_body').value = JSON.parse(this.dataset.body);
                document.getElementById('post_category').value = this.dataset.category || '';
                document.getElementById('post_published').checked = this.dataset.published == '1';
                window.scrollTo({top:0, behavior:'smooth'});
            });
        });
    })();
</script>
@endpush
@endsection


