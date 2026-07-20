@extends('admin.layouts.app')
@section('panel')
    @push('topBar')
        @include('admin.storage.top_bar')
    @endpush
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Driver Name')</label>
                                    <input class="form-control form-control-lg" name="backblaze[driver]" type="text" value="{{ @gs('backblaze')->driver }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Key ID')</label>
                                    <input class="form-control form-control-lg" name="backblaze[key]" type="text" value="{{ @gs('backblaze')->key }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Application Key')</label>
                                    <input class="form-control form-control-lg" name="backblaze[secret]" type="text" value="{{ @gs('backblaze')->secret }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Region')</label>
                                    <input class="form-control form-control-lg" name="backblaze[region]" type="text" value="{{ @gs('backblaze')->region }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Bucket Name')</label>
                                    <input class="form-control form-control-lg" name="backblaze[bucket]" type="text" value="{{ @gs('backblaze')->bucket }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('End point')</label>
                                    <input class="form-control form-control-lg" name="backblaze[endpoint]" type="text" value="{{ @gs('backblaze')->endpoint }}" required>
                                    <code>(@lang('https://your-space-endpoint'))</code>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
