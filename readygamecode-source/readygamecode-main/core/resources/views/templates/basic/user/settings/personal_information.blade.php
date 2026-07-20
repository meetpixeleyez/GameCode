<div class="card custom--card mb-4">
    <div class="card-body">
        <div class="setting-content__item" id="personalInfo">
            <h5 class="setting-content__title">@lang('Personal Information')</h5>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="form--label">@lang('Username')</label>
                        <input type="text" class="form--control form--control--sm  bg--white checkUser" name="username"
                            value="{{ $user->username }}" />
                        <small class="text-danger usernameExist"></small>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="fName" class="form--label">@lang('First Name')</label>
                        <input type="text" class="form--control form--control--sm  bg--white" id="fName" name="firstname"
                            value="{{ $user->firstname }}" />
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="lName" class="form--label">@lang('Last Name')</label>
                        <input type="text" class="form--control form--control--sm  bg--white" id="lName" name="lastname"
                            value="{{ $user->lastname }}" />
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="country" class="form--label">@lang('Country')</label>
                        <select name="country" class="form-control form--control form--control--sm  bg--white" disabled>
                            @foreach ($countries as $key => $country)
                                <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}"
                                    @selected($key == $user->country_code)>
                                    {{ __($country->country) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="address" class="form--label">@lang('Address')</label>
                        <input type="text" class="form--control form--control--sm  bg--white" id="address" name="address"
                            value="{{ $user->address }}">
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="city" class="form--label">@lang('City')</label>
                        <input type="text" class="form--control form--control--sm  bg--white" id="city" name="city"
                            value="{{ @$user->city }}" />
                    </div>
                </div>
                <div class="col-sm-6 col-xsm-6">
                    <div class="form-group">
                        <label for="zipCode" class="form--label">@lang('Zip Code')</label>
                        <input type="text" class="form--control form--control--sm  bg--white" id="zipCode" name="zip"
                            value="{{ @$user->zip }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    username: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.usernameExist`).text(`This username already exists`);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
