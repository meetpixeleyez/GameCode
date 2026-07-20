@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $register = getContent('register.content', true);
        $socialLogin = getContent('social_login.content', true);
        $policyPages = getContent('policy_pages.element', false, null, true);
    @endphp

    <section class="account py-120">
        <div class="account-inner">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8">
                        <div class="account-form">
                            <div class="text-center mb--4">
                                <h5 class="account-form__title mb-2">{{ __(@$register->data_values->title) }}</h5>
                                <p>{{ __(@$register->data_values->subtitle) }}</p>
                            </div>
                            @php
                                $credentials = gs('socialite_credentials');
                            @endphp
                            @if ($credentials->google->status == Status::ENABLE || $credentials->facebook->status == Status::ENABLE || $credentials->linkedin->status == Status::ENABLE)
                                <div class="mb-4">
                                    <ul class="social-login-list d-flex gap-3 flex-wrap">
                                        @if ($credentials->facebook->status == Status::ENABLE)
                                            <li class="social-login-list__item facebook flex-fill">
                                                <a href="{{ route('user.social.login', 'facebook') }}"
                                                   class="social-login-list__link">
                                                    <span class="icon"><i class="icon-Fackbook"></i></span>
                                                    @lang('Facebook')
                                                </a>
                                            </li>
                                        @endif

                                        @if ($credentials->google->status == Status::ENABLE)
                                            <li class="social-login-list__item google flex-fill">
                                                <a href="{{ route('user.social.login', 'google') }}"
                                                   class="social-login-list__link">
                                                    <span class="icon"><i class="icon-google-1"></i></span>
                                                    @lang('Google')
                                                </a>
                                            </li>
                                        @endif

                                        @if ($credentials->linkedin->status == Status::ENABLE)
                                            <li class="social-login-list__item linkedin flex-fill">
                                                <a href="{{ route('user.social.login', 'linkedin') }}"
                                                   class="social-login-list__link">
                                                    <span class="icon"><i class="fab fa-linkedin"></i></span>
                                                    @lang('Linkedin')
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="mb-4">
                                    <div class="another-login text-center">
                                        <hr class="bar">
                                        <span class="another-login__text">@lang('OR')</span>
                                        <hr class="bar">
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
                                @csrf
                                <div class="row">
                                    @if (session()->get('reference'))
                                        @php
                                            $user = App\Models\User::where('username', session()->get('reference'))->first();
                                        @endphp
                                        <div class="col-12 mb-5">
                                            <div class="referral-info-box">
                                                <span class="referral-icon"><i class="la la-user-friends"></i></span>
                                                <span class="referral-text">
                                                    <strong>@lang('You were referred by:')</strong> {{ $user->fullName }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('First Name')</label>
                                            <input type="text" class="form-control form--control form--control--sm"
                                                   name="firstname" value="{{ old('firstname', @$user->firstname) }}"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">

                                            <label class="form--label">@lang('Last Name')</label>
                                            <input type="text" class="form-control form--control form--control--sm"
                                                   name="lastname" value="{{ old('lastname', @$user->lastname) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-xl-12">
                                        <div class="form-group">
                                            <label class="form--label">@lang('E-Mail Address')</label>
                                            <input type="email" class="form--control form--control--sm checkUser"
                                                   name="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Password')</label>
                                            <div class="position-relative">
                                                <input type="password"
                                                       class="form-control form--control form--control--sm @if (gs('secure_password')) secure-password @endif"
                                                       name="password" required>
                                                <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                      id="#password"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Confirm Password')</label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control form--control form--control--sm"
                                                       name="password_confirmation" required>
                                                <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                                      id="#password_confirmation"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <x-captcha />
                                    </div>
                                    @if (gs('agree'))
                                        <div class="form-group form--checks">
                                            <input type="checkbox" id="agree" @checked(old('agree'))
                                                   name="agree" class="form-check-input" required>
                                            <label for="agree" class="form-check-label"> @lang('I agree with')</label>
                                            <span>
                                                @foreach ($policyPages as $policy)
                                                    <a class="fw-500 forgot-pass fs-14"
                                                       href="{{ route('policy.pages', $policy->slug) }}"
                                                       target="_blank">{{ __($policy->data_values->title) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span>
                                        </div>
                                    @endif
                                    <div class="col-sm-12">
                                        <div class="form-group mt-2">
                                            <button class="btn btn--base btn--md w-100"
                                                    id="recaptcha">@lang('Sign Up')</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="have-account">
                                            <p class="have-account__text">@lang('Have an account?') <a
                                                   href="{{ route('user.login') }}"
                                                   class="have-account__link  fw-500">@lang('Sign In')</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal custom--modal register fade custom--modal" id="existModalCenter">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-0 fs-16 fw-500">@lang('You already have an account please') <a href="{{ route('user.login') }}"
                           class="">@lang('Login')</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm"
                            data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('style')
    <style>
        /* Enhanced Register Page UI */
        .account {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            padding: 60px 0;
        }

        .account-inner {
            width: 100%;
        }

        .account-form {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 124, 49, 0.1);
            max-width: 520px;
            margin: 0 auto;
        }

        .account-form__title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .account-form p {
            color: #666;
            font-size: 0.9375rem;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        /* Form Control Styling */
        .account .form--control {
            border: 1.5px solid #e8e8e8;
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            background: #fff;
            width: 100%;
        }
        
        .account .form-group {
            margin-bottom: 20px;
        }

        .account .form--control:focus {
            border-color: #ff7c31;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255, 124, 49, 0.1);
            outline: none;
        }

        .account .form--label {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-size: 0.875rem;
        }

        /* Password Field */
        .account .position-relative {
            position: relative;
        }

        .password-show-hide {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-show-hide:hover {
            color: #ff7c31;
        }

        /* Button Styling */
        .account .btn--base {
            background: linear-gradient(135deg, #ff7c31 0%, #ff9a5c 100%);
            border: none;
            border-radius: 8px;
            padding: 14px 24px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(255, 124, 49, 0.3);
            transition: all 0.3s ease;
        }

        .account .btn--base:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 124, 49, 0.4);
        }

        .account .btn--base:active {
            transform: translateY(0);
        }

        /* Social Login Buttons */
        .social-login-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .social-login-list__item {
            margin-bottom: 12px;
        }

        .social-login-list__link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1.5px solid #e8e8e8;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-login-list__link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* OR Divider */
        .another-login {
            margin: 24px 0;
        }

        .another-login .bar {
            background: #e8e8e8;
            height: 1px;
            flex: 1;
        }

        .another-login__text {
            padding: 0 16px;
            color: #666;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Checkbox Styling */
        .account .form-check-input {
            width: 18px;
            height: 18px;
            border: 1.5px solid #e8e8e8;
            border-radius: 4px;
            cursor: pointer;
        }

        .account .form-check-input:checked {
            background-color: #ff7c31;
            border-color: #ff7c31;
        }

        .account .form-check-label {
            color: #666;
            font-size: 0.875rem;
            cursor: pointer;
        }

        /* Have Account Link */
        .have-account {
            text-align: center;
            margin-top: 24px;
        }

        .have-account__text {
            color: #666;
            font-size: 0.9375rem;
            margin: 0;
        }

        .have-account__link {
            color: #ff7c31;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .have-account__link:hover {
            color: #ff9a5c;
            text-decoration: underline;
        }

        /* Referral Info Box */
        .referral-info-box {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #fff5f0 0%, #ffe8dc 100%);
            border-left: 4px solid #ff7c31;
            border-radius: 8px;
            padding: 16px;
            margin-top: 15px;
            box-shadow: 0 2px 8px rgba(255, 124, 49, 0.15);
        }

        .referral-icon {
            font-size: 24px;
            color: #ff7c31;
            margin-right: 12px;
        }

        .referral-text {
            font-size: 0.9375rem;
            color: #1a1a1a;
            font-weight: 600;
        }

        .referral-text strong {
            color: #ff7c31;
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .account-form {
                padding: 32px;
            }
        }

        @media (max-width: 767px) {
            .account {
                padding: 24px 0;
            }

            .account-form {
                padding: 28px 24px;
                border-radius: 12px;
            }

            .account-form__title {
                font-size: 1.5rem;
            }

            .social-login-list__item {
                width: 100%;
            }

            .social-login-list__link {
                width: 100%;
            }
        }

        @media (max-width: 575px) {
            .account-form {
                padding: 24px 20px;
            }

            .account-form__title {
                font-size: 1.375rem;
            }

            .account .form--control {
                padding: 10px 14px;
                font-size: 0.875rem;
            }

            .account .btn--base {
                padding: 12px 20px;
                font-size: 0.9375rem;
            }

            .referral-info-box {
                padding: 12px;
            }

            .referral-icon {
                font-size: 20px;
                margin-right: 10px;
            }

            .referral-text {
                font-size: 0.875rem;
            }
        }
    </style>
@endpush
@push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
