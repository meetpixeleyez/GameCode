@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">@lang('Blog Categories')</h5>
            </div>
            <div class="card-body px-0">
                <div class="table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Slug')</th>
                                <th>@lang('Active')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                                <tr>
                                    <td>{{ $cat->name }}</td>
                                    <td>{{ $cat->slug }}</td>
                                    <td><span class="badge {{ $cat->is_active ? 'badge--success' : 'badge--warning' }}">{{ $cat->is_active ? 'Yes' : 'No' }}</span></td>
                                    <td>
                                        <button class="btn btn--primary btn--sm editBtn" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-slug="{{ $cat->slug }}" data-active="{{ (int)$cat->is_active }}">@lang('Edit')</button>
                                        <form class="d-inline" method="post" action="{{ route('admin.blog.categories.delete', $cat->id) }}">
                                            @csrf
                                            <button class="btn btn--danger btn--sm" onclick="return confirm('Delete category?')">@lang('Delete')</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">@lang('No categories')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title addEditTitle">@lang('Add Category')</h5></div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.blog.categories.store') }}" id="catForm">
                    @csrf
                    <input type="hidden" name="id" id="cat_id" />
                    <div class="mb-3">
                        <label class="form-label">@lang('Name')</label>
                        <input type="text" class="form-control" name="name" id="cat_name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Slug')</label>
                        <input type="text" class="form-control" name="slug" id="cat_slug" required />
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="cat_active" name="is_active" checked>
                        <label class="form-check-label" for="cat_active">@lang('Active')</label>
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
        document.querySelectorAll('.editBtn').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.querySelector('.addEditTitle').innerText = 'Edit Category';
                document.getElementById('cat_id').value = this.dataset.id;
                document.getElementById('cat_name').value = this.dataset.name;
                document.getElementById('cat_slug').value = this.dataset.slug;
                document.getElementById('cat_active').checked = this.dataset.active == '1';
            });
        });
    })();
</script>
@endpush
@endsection


