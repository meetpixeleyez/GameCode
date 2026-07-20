@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="card-title">@lang('Author Information')</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.author.form.submit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-viser-form identifier="act" identifierValue="author_info" frontend="true" />
                        <div class="form-group">
                            <button type="submit" class="btn btn--base btn--sm w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
