@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $user = auth()->user();
    @endphp

    <section class="account py-120">
        <div class="account-inner">
            <div class="container">
                <div class="row gy-4 flex-wrap-reverse align-items-center justify-content-center">
                    <div class="col-xl-5 col-md-8">
                        <div class="account-form">
                            <div class="text-center mb--4">
                                <h5 class="account-form__title mb-2">@lang('Complete your profile')</h5>
                                <p>@lang('You need to complete your profile by providing below information.')</p>
                            </div>

                            <form method="POST" action="{{ route('user.data.submit') }}">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label class="form--label">@lang('Username')</label>
                                        <input type="text" class="form--control form--control--sm checkUser"
                                               name="username" value="{{ old('username') }}" required>
                                        <small class="text-danger usernameExist"></small>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Country')</label>
                                        <select name="country" class="form--control form--control--sm select2" required>
                                            @foreach ($countries as $key => $country)
                                                <option @selected($country->country == old('country'))
                                                        data-mobile_code="{{ $country->dial_code }}"
                                                        value="{{ $country->country }}" data-code="{{ $key }}">
                                                    {{ __($country->country) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">

                                        <label class="form--label">@lang('Mobile')</label>
                                        <div class="input-group">
                                            <span class="input-group-text mobile-code"></span>
                                            <input type="hidden" name="mobile_code">
                                            <input type="hidden" name="country_code">
                                            <input type="number"
                                                   class="form-control form--control form--control--sm checkUser"
                                                   name="mobile" value="{{ old('mobile') }}" required>
                                        </div>
                                        <small class="text-danger mobileExist"></small>

                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Address')</label>
                                        <input type="text" class="form-control form--control form--control--sm"
                                               name="address" value="{{ old('address') }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('State')</label>
                                        <input type="text" class="form-control form--control form--control--sm"
                                               name="state" value="{{ old('state') }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Zip Code')</label>
                                        <input type="text" class="form-control form--control form--control--sm"
                                               name="zip" value="{{ old('zip') }}">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('City')</label>
                                        <input type="text" class="form-control form--control form--control--sm"
                                               name="city" value="{{ old('city') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn--base btn--sm w-100">
                                    @lang('Submit')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('style')
    <style>
        .account::before {
            left: auto;
            right: -13%;
        }

        @media screen and (max-width: 1599px) {

            .account::before,
            .account::after {
                width: 870px;
                height: 870px;
            }
        }

        @media screen and (max-width: 1399px) {

            .account::before,
            .account::after {
                width: 770px;
                height: 770px;
            }
        }

        @media screen and (max-width: 1199px) {

            .account::before,
            .account::after {
                width: 670px;
                height: 670px;
            }
        }

        @media screen and (max-width: 991px) {

            .account::before,
            .account::after {
                width: 570px;
                height: 570px;
            }
        }

        @media screen and (max-width: 767px) {

            .account::before,
            .account::after {
                width: 470px;
                height: 470px;
            }
        }

        @media screen and (max-width: 575px) {

            .account::before,
            .account::after {
                width: 370px;
                height: 370px;
            }

            .account::before {
                top: auto;
                bottom: 0;
            }
        }

        @media screen and (max-width: 424px) {

            .account::before,
            .account::after {
                width: 270px;
                height: 270px;
            }
        }

        .select2-container--default .select2-selection--single {
            background-color: transparent;
            border-color: hsl(var(--border-color-dark)) !important;
        }

        .select2-container--focus .select2-selection--single,
        .select2-container--open .select2-selection--single,
        .select2-container--focus .select2-selection--multiple,
        .select2-container--open .select2-selection--multiple {
            border-color: hsl(var(--base)) !important;
            outline: none;
        }

        .input-group .input-group-text {
            color: hsl(var(--white));
            border-color: hsl(var(--base)) !important;
            background-color: hsl(var(--base));
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif


            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value, name);
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
