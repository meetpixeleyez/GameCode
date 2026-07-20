@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="card-title">@lang('Change Password')</h6>
                </div>
                <div class="card-body">
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Current Password')</label>
                            <input type="password" class="form--control form--control--sm" name="current_password" required
                                autocomplete="current-password">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Password')</label>
                            <input type="password"
                                class="form--control form--control--sm @if (gs('secure_password')) secure-password @endif"
                                name="password" required autocomplete="current-password">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Confirm Password')</label>
                            <input type="password" class="form--control form--control--sm" name="password_confirmation"
                                required autocomplete="current-password">
                        </div>
                        <button type="submit" class="btn btn--base w-100 btn--sm">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
