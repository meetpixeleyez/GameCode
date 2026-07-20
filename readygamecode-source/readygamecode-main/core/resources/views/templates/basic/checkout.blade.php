@extends($activeTemplate . 'layouts.frontend')
@section('meta_title', gs()->siteName(__('Checkout')))
@section('meta_description', __('Complete your purchase securely with our checkout page for Unity game source codes and mobile game templates.'))
@section('meta_robots', 'noindex, follow')

@section('content')
    <section class="checkout-page pt-60 pb-120">
        <div class="container">
            <div class="row">
            <!-- Left Column: Checkout Steps -->
                <div class="col-lg-8">
                <div id="checkoutApp" v-cloak>
                    <!-- Step 2: Sign In / Create Account Selection -->
                    <div v-show="currentStep === 1" class="checkout-step">
                        <!-- Sign In Card -->
                        <div class="info-card-modern signin-card-modern mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-sign-in-alt"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">@lang('Sign In')</h5>
                                </div>
                                <button @click="showRegister = true; currentStep = 2" class="info-card-edit-btn">
                                    <span>@lang('Create Account')</span>
                                </button>
                            </div>
                            <div class="info-card-body">

                        <!-- Social Login -->
                        @if (@$socialiteCredentials->google->status == \App\Constants\Status::ENABLE)
                        <div class="mb-4">
                            <a href="{{ route('user.social.login', 'google') }}?checkout=1" class="btn btn-outline-secondary w-100 mb-2 d-flex align-items-center justify-content-center gocs">
                                <svg width="18" height="18" viewBox="0 0 18 18" class="me-2">
                                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.96-2.184l-2.908-2.258c-.806.54-1.837.86-3.052.86-2.35 0-4.34-1.587-5.05-3.72H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                    <path fill="#FBBC05" d="M3.95 10.698c-.18-.54-.282-1.117-.282-1.698 0-.581.102-1.158.282-1.698V4.97H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.03l2.993-2.332z"/>
                                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.97L3.95 7.302C4.66 5.167 6.65 3.58 9 3.58z"/>
                                </svg>
                                @lang('Continue with Google')
                            </a>
                        </div>
                        @endif

                        <!-- Login Form -->
                        <form @submit.prevent="submitLogin" class="checkout-form">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Username or Email')</label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control" v-model="loginForm.username" required>
                                    <!--<a href="{{ route('user.password.request') }}" class="btn btn-link p-0 ms-2 small checreate">@lang('Remind me')</a>-->
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Password')</label>
                                <div class="d-flex align-items-center">
                                    <input type="password" class="form-control" v-model="loginForm.password" required>
                                </div>
                                <a href="{{ route('user.password.request') }}" class="btn btn-link p-0 mt-2 small checreate">@lang('Forgot Password?')</a>
                            </div>
                            <!-- Captcha -->
                            @include('partials.captcha')
                            
                            <!--<p class="text-muted small mb-3">@lang('By continuing, you confirm you are 18 or over and agree to our') <a href="#">@lang('Privacy Policy')</a> @lang('and') <a href="#">@lang('Terms of Use')</a>.</p>-->
                            <button type="submit" class="btn w-100 pocart" :disabled="loginLoading">
                                <span v-if="loginLoading" class="spinner-border spinner-border-sm me-2"></span>
                                @lang('Sign in & continue')
                            </button>
                        </form>
                        
                        <!-- Display Errors -->
                        @if($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                            </div>
                        </div>
                    </div>
                                    
                    <!-- Step 3: Create Account -->
                    <div v-show="currentStep === 2" class="checkout-step">
                        <!-- Create Account Card -->
                        <div class="info-card-modern register-card-modern mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-user-plus"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">@lang('Create Account')</h5>
                                </div>
                                <button @click="showRegister = false; currentStep = 1" class="info-card-edit-btn">
                                    <span>@lang('Sign In')</span>
                                </button>
                            </div>
                            <div class="info-card-body">
                                    
                        <!-- Social Login -->
                        @if (@$socialiteCredentials->google->status == \App\Constants\Status::ENABLE)
                        <div class="mb-4">
                            <a href="{{ route('user.social.login', 'google') }}?checkout=1" class="btn btn-outline-secondary w-100 mb-2 d-flex align-items-center justify-content-center gocs">
                                <svg width="18" height="18" viewBox="0 0 18 18" class="me-2">
                                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.96-2.184l-2.908-2.258c-.806.54-1.837.86-3.052.86-2.35 0-4.34-1.587-5.05-3.72H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                    <path fill="#FBBC05" d="M3.95 10.698c-.18-.54-.282-1.117-.282-1.698 0-.581.102-1.158.282-1.698V4.97H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.03l2.993-2.332z"/>
                                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.97L3.95 7.302C4.66 5.167 6.65 3.58 9 3.58z"/>
                                </svg>
                                @lang('Continue with Google')
                            </a>
                        </div>
                        @endif

                        <!-- Register Form -->
                        <form @submit.prevent="submitRegister" class="checkout-form">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">@lang('First name')</label>
                                    <input type="text" class="form-control" v-model="registerForm.firstname" required>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">@lang('Last name')</label>
                                    <input type="text" class="form-control" v-model="registerForm.lastname" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Email')</label>
                                <input type="email" class="form-control" v-model="registerForm.email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Password')</label>
                                <input type="password" class="form-control" v-model="registerForm.password" required>
                                <small class="text-muted">@lang('Use 8 or more characters with a mix of letters, numbers and symbols. Must not contain your name or username.')</small>
                            </div>
                            <div class="form-group mb-3">
                                        <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="registerForm.send_tips" id="sendTips">
                                    <label class="form-check-label" for="sendTips">
                                        @lang('Send me tips, trends, updates & offers.')
                                            </label>
                                        </div>
                                <small class="text-muted">@lang('You can unsubscribe at any time.')</small>
                            </div>
                            
                            @if(gs('agree'))
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="registerForm.agree" id="agree" :required="true">
                                    <label class="form-check-label" for="agree">@lang('I agree with') <a href="#" target="_blank">@lang('Terms and conditions')</a></label>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Captcha -->
                            @include('partials.captcha')
                            
                            <p class="text-muted small mb-3">@lang('By continuing, you confirm you are 18 or over and agree to our') <a href="#">@lang('Privacy Policy')</a> @lang('and') <a href="#">@lang('Terms of Use')</a>.</p>
                            <button type="submit" class="btn pocart w-100" :disabled="registerLoading">
                                <span v-if="registerLoading" class="spinner-border spinner-border-sm me-2"></span>
                                @lang('Create account & continue')
                            </button>
                        </form>
                        
                        <!-- Display Errors -->
                        @if($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Billing Address -->
                    <div v-show="currentStep === 3" class="checkout-step">
                        <!-- Account Info Display -->
                        <div class="info-card-modern account-card-modern mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-user-circle"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">@lang('Account')</h5>
                                </div>
                            </div>
                            <div class="info-card-body">
                                <div class="info-items-grid">
                                    <div class="info-item">
                                        <i class="las la-user info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Name')</span>
                                            <span class="info-item-value">@{{ userInfo.fullName }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="las la-envelope info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Email')</span>
                                            <span class="info-item-value">@{{ userInfo.email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted small mt-3 mb-0">@lang('Envato collects and uses personal data in accordance with our') <a href="#">@lang('Privacy Policy')</a>. @lang('By creating an account, you agree to our') <a href="#">@lang('Terms and Conditions')</a>.</p>
                            </div>
                        </div>

                        <!-- Billing Details Form Card -->
                        <div class="info-card-modern billing-form-card-modern mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-file-invoice-dollar"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">@lang('Billing Details')</h5>
                                </div>
                            </div>
                            <div class="info-card-body">
                        <form @submit.prevent="submitBilling" class="checkout-form">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">@lang('First Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="billingForm.firstname" required>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">@lang('Last Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="billingForm.lastname" required>
                                </div>
                            </div>
                            @if(auth()->check() && !auth()->user()->username)
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Username') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="billingForm.username" required>
                                <small class="text-muted">@lang('Username is required to complete your profile')</small>
                            </div>
                            @endif
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Company Name')</label>
                                <input type="text" class="form-control" v-model="billingForm.company">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Country') <span class="text-danger">*</span></label>
                                <select name="country" class="form-control billing-country-select" required>
                                    <option value="">@lang('Please select')</option>
                                    @php
                                        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
                                        $userCountry = auth()->check() && auth()->user()->country_name ? auth()->user()->country_name : old('country');
                                    @endphp
                                    @foreach($countries as $key => $country)
                                    <option value="{{ $country->country }}" 
                                            data-mobile_code="{{ $country->dial_code }}"
                                            data-code="{{ $key }}"
                                            @if($userCountry == $country->country) selected @endif>
                                        {{ __($country->country) }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="mobile_code" class="billing-mobile-code">
                                <input type="hidden" name="country_code" class="billing-country-code">
                            </div>
                            @if(auth()->check() && !auth()->user()->mobile)
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Mobile') <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text billing-mobile-code-display">
                                        @if(auth()->check() && auth()->user()->dial_code)
                                            +{{ auth()->user()->dial_code }}
                                        @else
                                            +91
                                        @endif
                                    </span>
                                    <input type="text" class="form-control" name="mobile" value="{{ old('mobile', auth()->check() ? auth()->user()->mobile : '') }}" required>
                                </div>
                            </div>
                            @endif
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Address line 1')</label>
                                <input type="text" class="form-control" v-model="billingForm.address1">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Address line 2')</label>
                                <input type="text" class="form-control" v-model="billingForm.address2">
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">@lang('City')</label>
                                    <input type="text" class="form-control" v-model="billingForm.city">
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">@lang('State / Province / Region')</label>
                                    <input type="text" class="form-control" v-model="billingForm.state">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Zip / Postal Code')</label>
                                <input type="text" class="form-control" v-model="billingForm.zip">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('GSTIN')</label>
                                <input type="text" class="form-control" v-model="billingForm.gstin">
                            </div>
                            <button type="submit" class="btn btn-success w-100" :disabled="billingLoading">
                                <span v-if="billingLoading" class="spinner-border spinner-border-sm me-2"></span>
                                @lang('Save and continue')
                            </button>
                        </form>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Payment Method -->
                    <div v-show="currentStep === 4" class="checkout-step">
                        <!-- Account Info -->
                        <div class="info-card-modern account-card-modern mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-user-circle"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">@lang('Account')</h5>
                                </div>
                            </div>
                            <div class="info-card-body">
                                <div class="info-items-grid">
                                    <div class="info-item">
                                        <i class="las la-user info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Name')</span>
                                            <span class="info-item-value">@{{ userInfo.fullName }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="las la-at info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Username')</span>
                                            <span class="info-item-value">@{{ userInfo.username }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="las la-envelope info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Email')</span>
                                            <span class="info-item-value">@{{ userInfo.email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Details -->
                        <div class="info-card-modern billing-card-modern mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-map-marker-alt"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">@lang('Billing Details')</h5>
                                </div>
                                <button @click="currentStep = 3" class="info-card-edit-btn">
                                    <i class="las la-pen"></i> @lang('Edit')
                                </button>
                            </div>
                            <div class="info-card-body">
                                <div class="info-items-grid">
                                    <div class="info-item">
                                        <i class="las la-user info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Name')</span>
                                            <span class="info-item-value">@{{ billingForm.firstname }} @{{ billingForm.lastname }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item" v-if="billingForm.address1">
                                        <i class="las la-home info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Address')</span>
                                            <span class="info-item-value">@{{ billingForm.address1 }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item" v-if="billingForm.city || billingForm.state">
                                        <i class="las la-city info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('City, State')</span>
                                            <span class="info-item-value">@{{ billingForm.city }}, @{{ billingForm.state }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item" v-if="billingForm.country">
                                        <i class="las la-flag info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('Country')</span>
                                            <span class="info-item-value">@{{ billingForm.country }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="las la-file-invoice info-item-icon"></i>
                                        <div class="info-item-content">
                                            <span class="info-item-label">@lang('GSTIN')</span>
                                            <span class="info-item-value">@{{ billingForm.gstin || '@lang(\'Not provided\')' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="info-card-modern payment-method-wrapper-card mb-4">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="las la-credit-card"></i>
                                </div>
                                <div class="info-card-title-wrapper">
                                    <h5 class="info-card-title">Select Payment Method</h5>
                                </div>
                            </div>
                            <div class="info-card-body">
                                <!-- {{-- Debug info (remove in production) --}}
                                @if(config('app.debug'))
                                <div class="alert alert-info small mb-3">
                                    Debug: PayPal Found: {{ $paypalGateway ? 'Yes (Code: ' . $paypalGateway->method_code . ')' : 'No' }} | 
                                    Razorpay Found: {{ $razorpayGateway ? 'Yes (Code: ' . $razorpayGateway->method_code . ')' : 'No' }}
                                </div>
                                @endif -->
                                
                                <div class="payment-methods-grid">
                                @if($razorpayGateway)
                                <div class="payment-method-card-modern" 
                                     :class="{'payment-card-selected': selectedGateway === '{{ $razorpayGateway->method_code }}'}" 
                                     @click="selectedGateway = '{{ $razorpayGateway->method_code }}'">
                                    <div class="payment-card-header">
                                        <div class="payment-radio-wrapper">
                                            <input class="payment-radio-input" type="radio" 
                                                   :checked="selectedGateway === '{{ $razorpayGateway->method_code }}'"
                                                   @click.stop>
                                            <span class="payment-radio-custom"></span>
                                        </div>
                                        <div class="payment-selected-badge">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="payment-card-body">
                                        <div class="payment-logo-wrapper">
                                            <img src="{{ asset('assets/images/gateway/razorpay.svg') }}" alt="Razorpay" class="payment-logo-img">
                                        </div>
                                        <!-- <div class="payment-card-title">Razorpay</div> -->
                                        <div class="payment-card-subtitle">@lang('Secure Payment Gateway')</div>
                                        <div class="payment-card-features">
                                            <span class="payment-feature-badge">
                                                <i class="las la-shield-alt"></i> @lang('Secure')
                                            </span>
                                            <span class="payment-feature-badge">
                                                <i class="las la-credit-card"></i> @lang('Cards')
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($paypalGateway)
                                <div class="payment-method-card-modern" 
                                     :class="{'payment-card-selected': selectedGateway === '{{ $paypalGateway->method_code }}'}" 
                                     @click="selectedGateway = '{{ $paypalGateway->method_code }}'">
                                    <div class="payment-card-header">
                                        <div class="payment-radio-wrapper">
                                            <input class="payment-radio-input" type="radio" 
                                                   :checked="selectedGateway === '{{ $paypalGateway->method_code }}'"
                                                   @click.stop>
                                            <span class="payment-radio-custom"></span>
                                        </div>
                                        <div class="payment-selected-badge">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="payment-card-body">
                                        <div class="payment-logo-wrapper">
                                            <img src="{{ asset('assets/images/gateway/paypal.png') }}" alt="PayPal" class="payment-logo-img">
                                        </div>
                                        <!-- <div class="payment-card-title">PayPal</div> -->
                                        <div class="payment-card-subtitle">@lang('Pay with PayPal Account')</div>
                                        <div class="payment-card-features">
                                            <span class="payment-feature-badge">
                                                <i class="las la-shield-alt"></i> @lang('Secure')
                                            </span>
                                            <span class="payment-feature-badge">
                                                <i class="las la-globe"></i> @lang('Worldwide')
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if(!$razorpayGateway && !$paypalGateway)
                            <div class="alert alert-warning">
                                @lang('No payment methods are currently available. Please contact support.')
                            </div>
                            @endif
                            </div>
                        </div>

                        <!-- <div class="order-breakdown-modern mb-4">
                            <div class="order-breakdown-header">
                                <div class="order-breakdown-icon">
                                    <i class="las la-shopping-bag"></i>
                                </div>
                                <h5 class="order-breakdown-title">@lang('Your Order')</h5>
                            </div>
                            <div class="order-breakdown-body">
                                <div class="order-breakdown-item">
                                    <div class="order-item-left">
                                        <i class="las la-shopping-cart order-item-icon"></i>
                                        <span class="order-item-name">@lang('Item')</span>
                                    </div>
                                    <span class="order-item-price-modern">{{ gs('cur_sym') }}{{ showAmount($subtotal, currencyFormat: false) }}</span>
                                </div>
                                <div class="order-breakdown-item">
                                    <div class="order-item-left">
                                        <i class="las la-hand-holding-usd order-item-icon"></i>
                                        <span class="order-item-name">@lang('Handling Fee')</span>
                                    </div>
                                    <span class="order-item-price-modern">{{ gs('cur_sym') }}0.00</span>
                                </div>
                                <div class="order-breakdown-item">
                                    <div class="order-item-left">
                                        <i class="las la-receipt order-item-icon"></i>
                                        <span class="order-item-name">@lang('GST')</span>
                                    </div>
                                    <span class="order-item-price-modern">{{ gs('cur_sym') }}0.00</span>
                                </div>
                                <div class="order-breakdown-divider"></div>
                                <div class="order-breakdown-total">
                                    <div class="order-total-left">
                                        <i class="las la-calculator order-total-icon"></i>
                                        <span class="order-total-label">@lang('Total')</span>
                                    </div>
                                    <span class="order-total-price">{{ gs('cur_sym') }}{{ showAmount($total, currencyFormat: false) }}</span>
                                </div>
                            </div>
                        </div> -->

                        <button @click="processPayment" class="btn-payment-modern w-100" :disabled="!selectedGateway || paymentLoading">
                            <span v-if="paymentLoading" class="payment-btn-spinner"></span>
                            <span v-else class="payment-btn-icon">
                                <i class="las la-lock"></i>
                            </span>
                            <span class="payment-btn-text">
                                <span v-if="selectedGateway === '{{ $razorpayGateway->method_code ?? '' }}'">@lang('Pay with Razorpay')</span>
                                <span v-else-if="selectedGateway === '{{ $paypalGateway->method_code ?? '' }}'">@lang('Pay with PayPal')</span>
                                <span v-else>@lang('Select Payment Method')</span>
                            </span>
                            <span class="payment-btn-arrow">
                                <i class="las la-arrow-right"></i>
                            </span>
                        </button>
                    </div>
                    </div>
                </div>
                
            <!-- Right Column: Order Summary -->
                <div class="col-lg-4">
                <div class="order-summary-sticky">
                    <div class="order-summary-card-modern">
                        <div class="order-summary-header">
                            <div class="order-summary-icon-wrapper">
                                <i class="las la-receipt"></i>
                            </div>
                            <h5 class="order-summary-title-modern">@lang('Order Summary')</h5>
                        </div>
                        <div class="order-summary-body">
                            <div class="order-items-modern">
                                @foreach ($cartItems as $cartItem)
                                    @php
                                        $additionalServicesPrice = ($cartItem->reskin_selected ? $cartItem->product->reskin_price : 0) + 
                                                                  ($cartItem->publish_selected ? $cartItem->product->publish_price : 0) + 
                                                                  ($cartItem->store_optimization_selected ? $cartItem->product->store_optimization_price : 0);
                                        $itemTotal = $cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + $additionalServicesPrice;
                                    @endphp
                                <div class="order-item-modern">
                                    <div class="order-item-content">
                                        <i class="las la-shopping-cart order-item-icon-modern"></i>
                                        <span class="order-item-name-modern">{{ Str::limit(@$cartItem->title ?? @$cartItem->product->title, 30) }}</span>
                                    </div>
                                    <span class="order-item-price-modern">{{ gs('cur_sym') }}{{ showAmount($itemTotal, currencyFormat: false) }}</span>
                                </div>
                                @endforeach
                                <div class="order-item-modern">
                                    <div class="order-item-content">
                                        <i class="las la-hand-holding-usd order-item-icon-modern"></i>
                                        <span class="order-item-name-modern">@lang('Handling Fee')</span>
                                    </div>
                                    <span class="order-item-price-modern">{{ gs('cur_sym') }}0.00</span>
                                </div>
                            </div>
                            <div class="order-summary-divider"></div>
                            <div class="order-total-modern">
                                <div class="order-total-content">
                                    <i class="las la-calculator order-total-icon-modern"></i>
                                    <strong class="order-total-label-modern">@lang('Total')</strong>
                                </div>
                                <strong class="order-total-price-modern">{{ gs('cur_sym') }}{{ showAmount($total, currencyFormat: false) }}</strong>
                            </div>
                        </div>
                        <div class="secure-checkout-modern">
                            <div class="secure-badge">
                                <i class="las la-shield-alt"></i>
                            </div>
                            <div class="secure-text">
                                <span class="secure-title">@lang('Secure checkout')</span>
                                <span class="secure-subtitle">@lang('Your payment is protected')</span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            currentStep: {{ auth()->check() ? (auth()->user()->profile_complete == \App\Constants\Status::YES ? 4 : 3) : 1 }},
            showRegister: {{ request()->has('register') || session()->has('show_register') ? 'true' : 'false' }},
            selectedGateway: '',
            loginLoading: false,
            registerLoading: false,
            billingLoading: false,
            paymentLoading: false,
            userInfo: {
                fullName: '{{ auth()->check() ? auth()->user()->firstname . " " . auth()->user()->lastname : "" }}',
                username: '{{ auth()->check() ? auth()->user()->username : "" }}',
                email: '{{ auth()->check() ? auth()->user()->email : "" }}'
            },
            loginForm: {
                username: '',
                password: '',
                remember: false
            },
            registerForm: {
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                password_confirmation: '',
                send_tips: true,
                agree: false
            },
            billingForm: {
                firstname: '{{ auth()->check() ? auth()->user()->firstname : "" }}',
                lastname: '{{ auth()->check() ? auth()->user()->lastname : "" }}',
                username: '{{ auth()->check() ? auth()->user()->username : "" }}',
                company: '',
                country: '{{ auth()->check() && auth()->user()->country_code ? auth()->user()->country_code : "" }}',
                country_name: '{{ auth()->check() && auth()->user()->country_name ? auth()->user()->country_name : "" }}',
                mobile: '{{ auth()->check() ? auth()->user()->mobile : "" }}',
                mobile_code: '{{ auth()->check() && auth()->user()->dial_code ? auth()->user()->dial_code : "" }}',
                address1: '{{ auth()->check() ? auth()->user()->address : "" }}',
                address2: '',
                city: '{{ auth()->check() ? auth()->user()->city : "" }}',
                state: '{{ auth()->check() ? auth()->user()->state : "" }}',
                zip: '{{ auth()->check() ? auth()->user()->zip : "" }}',
                gstin: ''
            }
        }
    },
    mounted() {
        // Initialize mobile code and country code on mount (like old form)
        this.$nextTick(() => {
            const select = document.querySelector('select.billing-country-select');
            if (select) {
                // Trigger change event to set initial values
                if (select.selectedIndex > 0) {
                    this.updateMobileCode();
                }
                
                // Add change event listener
                select.addEventListener('change', () => {
                    this.updateMobileCode();
                });
            }
        });
    },
    methods: {
        submitLogin() {
            this.loginLoading = true;
            
            // Get captcha values if present
            const captchaInput = document.querySelector('input[name="captcha"]');
            const captchaSecret = document.querySelector('input[name="captcha_secret"]');
            const gRecaptchaResponse = document.querySelector('textarea[name="g-recaptcha-response"]');
            
            // Create a proper form submission (not AJAX) to handle redirects properly
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("user.login") }}';
            form.classList.add('verify-gcaptcha');
            form.style.display = 'none';
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);
            
            const username = document.createElement('input');
            username.type = 'hidden';
            username.name = 'username';
            username.value = this.loginForm.username;
            form.appendChild(username);
            
            const password = document.createElement('input');
            password.type = 'hidden';
            password.name = 'password';
            password.value = this.loginForm.password;
            form.appendChild(password);
            
            // Add captcha if present
            if (captchaInput && captchaSecret) {
                const captcha = document.createElement('input');
                captcha.type = 'hidden';
                captcha.name = 'captcha';
                captcha.value = captchaInput.value;
                form.appendChild(captcha);
                
                const secret = document.createElement('input');
                secret.type = 'hidden';
                secret.name = 'captcha_secret';
                secret.value = captchaSecret.value;
                form.appendChild(secret);
            }
            
            // Add Google reCAPTCHA if present
            if (gRecaptchaResponse) {
                const recaptcha = document.createElement('textarea');
                recaptcha.name = 'g-recaptcha-response';
                recaptcha.style.display = 'none';
                if (typeof grecaptcha !== 'undefined') {
                    recaptcha.value = grecaptcha.getResponse();
                }
                form.appendChild(recaptcha);
            }
            
            document.body.appendChild(form);
            
            // Submit form - this will handle redirects properly
            form.submit();
        },
        submitRegister() {
            this.registerLoading = true;
            
            // Get captcha values if present
            const captchaInput = document.querySelector('input[name="captcha"]');
            const captchaSecret = document.querySelector('input[name="captcha_secret"]');
            const gRecaptchaResponse = document.querySelector('textarea[name="g-recaptcha-response"]');
            
            // Create a proper form submission (not AJAX) to handle redirects properly
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("user.register") }}';
            form.classList.add('verify-gcaptcha');
            form.style.display = 'none';
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);
            
            const firstname = document.createElement('input');
            firstname.type = 'hidden';
            firstname.name = 'firstname';
            firstname.value = this.registerForm.firstname;
            form.appendChild(firstname);
            
            const lastname = document.createElement('input');
            lastname.type = 'hidden';
            lastname.name = 'lastname';
            lastname.value = this.registerForm.lastname;
            form.appendChild(lastname);
            
            const email = document.createElement('input');
            email.type = 'hidden';
            email.name = 'email';
            email.value = this.registerForm.email;
            form.appendChild(email);
            
            const password = document.createElement('input');
            password.type = 'hidden';
            password.name = 'password';
            password.value = this.registerForm.password;
            form.appendChild(password);
            
            const passwordConf = document.createElement('input');
            passwordConf.type = 'hidden';
            passwordConf.name = 'password_confirmation';
            passwordConf.value = this.registerForm.password;
            form.appendChild(passwordConf);
            
            // Add agree checkbox if present and checked
            @if(gs('agree'))
            if (this.registerForm.agree) {
                const agree = document.createElement('input');
                agree.type = 'hidden';
                agree.name = 'agree';
                agree.value = '1';
                form.appendChild(agree);
            }
            @endif
            
            // Add captcha if present
            if (captchaInput && captchaSecret) {
                const captcha = document.createElement('input');
                captcha.type = 'hidden';
                captcha.name = 'captcha';
                captcha.value = captchaInput.value;
                form.appendChild(captcha);
                
                const secret = document.createElement('input');
                secret.type = 'hidden';
                secret.name = 'captcha_secret';
                secret.value = captchaSecret.value;
                form.appendChild(secret);
            }
            
            // Add Google reCAPTCHA if present
            if (gRecaptchaResponse) {
                const recaptcha = document.createElement('textarea');
                recaptcha.name = 'g-recaptcha-response';
                recaptcha.style.display = 'none';
                if (typeof grecaptcha !== 'undefined') {
                    recaptcha.value = grecaptcha.getResponse();
                }
                form.appendChild(recaptcha);
            }
            
            document.body.appendChild(form);
            
            // Submit form - this will handle redirects properly
            form.submit();
        },
        updateMobileCode() {
            // Use old form logic - jQuery style
            const select = document.querySelector('select.billing-country-select');
            if (select && select.selectedIndex > 0) {
                const selectedOption = select.options[select.selectedIndex];
                const mobileCode = selectedOption.getAttribute('data-mobile_code') || '';
                const countryCode = selectedOption.getAttribute('data-code') || '';
                const countryName = selectedOption.value || '';
                
                // Update hidden inputs
                const mobileCodeInput = document.querySelector('input.billing-mobile-code');
                const countryCodeInput = document.querySelector('input.billing-country-code');
                if (mobileCodeInput) mobileCodeInput.value = mobileCode;
                if (countryCodeInput) countryCodeInput.value = countryCode;
                
                // Update display
                const display = document.querySelector('.billing-mobile-code-display');
                if (display && mobileCode) {
                    display.textContent = '+' + mobileCode;
                }
                
                // Update Vue data for reference
                this.billingForm.mobile_code = mobileCode;
                this.billingForm.country_code = countryCode;
                this.billingForm.country_name = countryName;
            }
        },
        async submitBilling() {
            this.billingLoading = true;
            try {
                // Use old form logic - get values from form elements directly
                const select = document.querySelector('select.billing-country-select');
                if (!select || select.selectedIndex === 0) {
                    alert('Please select a country');
                    this.billingLoading = false;
                    return;
                }
                
                // Ensure mobile code and country code are updated
                this.updateMobileCode();
                
                // Get values from form elements (using old form approach)
                const countryName = select.value; // Country name is the value
                const mobileCodeInput = document.querySelector('input.billing-mobile-code');
                const countryCodeInput = document.querySelector('input.billing-country-code');
                const mobileInput = document.querySelector('input[name="mobile"]');
                
                const mobileCode = mobileCodeInput ? mobileCodeInput.value : '';
                const countryCode = countryCodeInput ? countryCodeInput.value : '';
                
                // Validate required values
                if (!countryName || !countryCode || !mobileCode) {
                    alert('Please select a valid country');
                    this.billingLoading = false;
                    return;
                }
                
                const formData = new FormData();
                
                // Only add username if user doesn't have one
                @if(auth()->check() && !auth()->user()->username)
                if (this.billingForm.username || this.userInfo.username) {
                    formData.append('username', this.billingForm.username || this.userInfo.username);
                }
                @endif
                
                // Always send country and country_code (required by backend) - using old form logic
                formData.append('country', countryName);
                formData.append('country_code', countryCode);
                formData.append('mobile_code', mobileCode);
                
                // Only add mobile if user doesn't have one
                @if(auth()->check() && !auth()->user()->mobile)
                if (mobileInput && mobileInput.value) {
                    formData.append('mobile', mobileInput.value);
                }
                @endif
                
                // Add address fields
                if (this.billingForm.address1) {
                    formData.append('address', this.billingForm.address1);
                }
                if (this.billingForm.city) {
                    formData.append('city', this.billingForm.city);
                }
                if (this.billingForm.state) {
                    formData.append('state', this.billingForm.state);
                }
                if (this.billingForm.zip) {
                    formData.append('zip', this.billingForm.zip);
                }
                
                formData.append('_token', '{{ csrf_token() }}');
                
                const response = await fetch('{{ route("user.data.submit") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                let result;
                try {
                    result = await response.json();
                } catch(e) {
                    // If response is not JSON (might be redirect), treat as success
                    if (response.ok || response.redirected) {
                        this.currentStep = 4;
                        return;
                    }
                    result = { message: 'Failed to save billing details' };
                }
                
                if (response.ok && result && result.status === 'success') {
                    // Profile updated successfully, go to payment step
                    this.currentStep = 4;
                } else if (response.status === 422 || (result && result.errors)) {
                    // Validation errors
                    let errorText = 'Please fix the following errors:\n';
                    if (result.errors) {
                        for (const [field, messages] of Object.entries(result.errors)) {
                            if (Array.isArray(messages)) {
                                errorText += `- ${messages.join(', ')}\n`;
                            } else {
                                errorText += `- ${messages}\n`;
                            }
                        }
                    } else if (result.message) {
                        errorText = result.message;
                    }
                    alert(errorText);
                } else {
                    const errorMsg = result?.message || 'Failed to save billing details. Please check your information.';
                    alert(errorMsg);
                }
            } catch (error) {
                console.error('Billing error:', error);
                alert('An error occurred while saving billing details');
            } finally {
                this.billingLoading = false;
            }
        },
        async processPayment() {
            if (!this.selectedGateway) {
                alert('Please select a payment method');
                return;
            }

            this.paymentLoading = true;
            try {
                const response = await fetch('{{ route("checkout.payment.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        gateway: this.selectedGateway
                    })
                });

                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    alert('Invalid response from server. Please try again.');
                    this.paymentLoading = false;
                    return;
                }

                if (response.ok && data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    const errorMsg = data.error || data.message || 'Payment processing failed';
                    console.error('Payment error:', errorMsg, data);
                    alert(errorMsg);
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('An error occurred during payment processing: ' + error.message);
            } finally {
                this.paymentLoading = false;
            }
        }
    }
}).mount('#checkoutApp');
</script>

@push('style')
    <style>
[v-cloak] { display: none; }
/* Color Variables - Orange & Black Variations */
:root {
    --orange-primary: #ff7c31;
    --orange-light: #ff8f4a;
    --orange-lighter: #ffa366;
    --orange-lightest: #ffb880;
    --orange-dark: #e65d1f;
    --orange-darker: #cc4a16;
    --orange-hover: #ff9047;
    --black-primary: #000000;
    --black-light: #1a1a1a;
    --black-lighter: #333333;
    --black-lightest: #4d4d4d;
    --black-gray: #666666;
    --black-gray-light: #808080;
    --black-gray-lighter: #999999;
}
.checkout-page { 
    background: #f8f9fa; 
    min-height: 70vh;
}
.checkout-step { 
            background: #fff;
    padding: 30px; 
            border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.checkout-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}
        .checkout-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--black-lighter);
}
.checkout-step-inactive { 
    opacity: 0.5; 
    margin-top: 20px;
}
.checkout-form .form-label {
    font-weight: 500;
    margin-bottom: 8px;
            color: var(--black-lighter);
        }
.checkout-form .form-control {
    border: 1px solid #ddd;
    padding: 10px 15px;
    border-radius: 6px;
}
.checkout-form .form-control:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 0.2rem rgba(255, 124, 49, 0.25);
}
.order-summary-sticky { 
    position: sticky; 
    top: 20px; 
}
.order-summary-card { 
            background: #fff;
    padding: 25px; 
            border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
.order-summary-title { 
    font-size: 20px; 
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}
.order-items {
    margin-bottom: 15px;
}
.order-item { 
    display: flex; 
    justify-content: space-between; 
    padding: 12px 0; 
    border-bottom: 1px solid #e9ecef; 
}
.order-item:last-child {
            border-bottom: none;
        }
.order-item-name { 
    flex: 1; 
    color: var(--black-gray);
    font-size: 14px;
}
.order-item-price { 
    font-weight: 600; 
    color: var(--black-lighter);
}
.order-total { 
    padding-top: 20px; 
    border-top: 2px solid var(--orange-primary);
    margin-top: 15px;
}
.order-total strong {
    font-size: 18px;
    color: var(--black-lighter);
}
/* Modern Payment Methods Design */
.payment-methods-wrapper {
    margin-top: 10px;
}
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}
.payment-method-card-modern {
    position: relative;
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 16px;
    padding: 0;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    min-height: 220px;
    display: flex;
    flex-direction: column;
}
.payment-method-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}
.payment-method-card-modern:hover {
    border-color: var(--orange-primary);
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(255, 124, 49, 0.15);
}
.payment-method-card-modern:hover::before {
    transform: scaleX(1);
}
.payment-card-selected {
    border-color: var(--orange-primary);
    background: linear-gradient(135deg, rgba(255, 124, 49, 0.03) 0%, rgba(230, 93, 31, 0.03) 100%);
    box-shadow: 0 12px 28px rgba(255, 124, 49, 0.2);
    transform: translateY(-2px);
}
.payment-card-selected::before {
    transform: scaleX(1);
}
.payment-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px 12px;
    border-bottom: 1px solid #f0f0f0;
}
.payment-radio-wrapper {
    position: relative;
    display: inline-block;
}
.payment-radio-input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    width: 24px;
    height: 24px;
    margin: 0;
}
.payment-radio-custom {
    position: absolute;
    top: 0;
    left: 0;
    width: 24px;
    height: 24px;
    border: 2px solid #ddd;
    border-radius: 50%;
    background: #fff;
    transition: all 0.3s ease;
}
.payment-radio-input:checked ~ .payment-radio-custom {
    border-color: var(--orange-primary);
    background: var(--orange-primary);
}
.payment-radio-input:checked ~ .payment-radio-custom::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #fff;
}
.payment-selected-badge {
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    color: var(--orange-primary);
    font-size: 24px;
}
.payment-card-selected .payment-selected-badge {
    opacity: 1;
    transform: scale(1);
}
.payment-card-body {
    padding: 24px 20px;
    text-align: center;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.payment-logo-wrapper {
    margin-bottom: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 50px;
}
.payment-logo-img {
    max-height: 45px;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    transition: transform 0.3s ease;
}
.payment-method-card-modern:hover .payment-logo-img {
    transform: scale(1.05);
}
.payment-card-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--black-lighter);
    margin-bottom: 6px;
    letter-spacing: -0.3px;
}
.payment-card-subtitle {
    font-size: 13px;
    color: var(--black-gray);
    margin-bottom: 16px;
    font-weight: 400;
}
.payment-card-features {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
}
.payment-feature-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 124, 49, 0.1);
    border: 1px solid rgba(255, 124, 49, 0.3);
    border-radius: 20px;
    font-size: 12px;
    color: var(--black-lightest);
    font-weight: 500;
    transition: all 0.3s ease;
}
.payment-card-selected .payment-feature-badge {
    background: rgba(255, 124, 49, 0.15);
    border-color: var(--orange-primary);
    color: var(--orange-dark);
}
.payment-feature-badge i {
    font-size: 14px;
}
/* Modern Info Cards (Account & Billing) */
.info-card-modern {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
}
.info-card-modern:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.account-card-modern {
    border-left: 4px solid var(--orange-primary);
}
.billing-card-modern {
    border-left: 4px solid var(--orange-dark);
}
.payment-method-wrapper-card {
    border-left: 4px solid var(--orange-primary);
}
.signin-card-modern {
    border-left: 4px solid var(--orange-primary);
}
.register-card-modern {
    border-left: 4px solid var(--orange-hover);
}
.billing-form-card-modern {
    border-left: 4px solid var(--orange-dark);
}
.info-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #e8eaed;
    position: relative;
}
.info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    flex-shrink: 0;
}
.account-card-modern .info-card-icon {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
}
.billing-card-modern .info-card-icon {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-darker) 100%);
}
.payment-method-wrapper-card .info-card-icon {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
}
.signin-card-modern .info-card-icon {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
}
.register-card-modern .info-card-icon {
    background: linear-gradient(135deg, var(--orange-hover) 0%, var(--orange-primary) 100%);
}
.billing-form-card-modern .info-card-icon {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-darker) 100%);
}
.info-card-title-wrapper {
    flex: 1;
}
.info-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--black-lighter);
    margin: 0;
    letter-spacing: -0.3px;
}
.info-card-edit-btn {
    padding: 8px 16px;
    background: transparent;
    border: 1.5px solid rgba(255, 124, 49, 0.3);
    border-radius: 8px;
    color: var(--black-lightest);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-card-edit-btn:hover {
    background: rgba(255, 124, 49, 0.1);
    border-color: var(--orange-primary);
    color: var(--orange-primary);
    transform: translateY(-1px);
}
.info-card-edit-btn i {
    font-size: 14px;
}
.info-card-body {
    padding: 20px 24px;
}
.info-items-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 20px;
}
.info-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #f0f2f5;
}
.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.info-item:first-child {
    padding-top: 0;
}
.info-item-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: var(--orange-primary);
    background: rgba(255, 124, 49, 0.1);
    flex-shrink: 0;
}
.info-item-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.info-item-label {
    font-size: 12px;
    color: var(--black-gray);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info-item-value {
    font-size: 15px;
    color: var(--black-lighter);
    font-weight: 600;
}

/* Modern Order Breakdown */
.order-breakdown-modern {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.order-breakdown-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #fff5e6 0%, #fff9f0 100%);
    border-bottom: 1px solid #e8eaed;
}
.order-breakdown-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
}
.order-breakdown-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--black-lighter);
    margin: 0;
    letter-spacing: -0.3px;
}
.order-breakdown-body {
    padding: 20px 24px;
}
.order-breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f0f2f5;
}
.order-breakdown-item:last-of-type {
    border-bottom: none;
}
.order-item-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}
.order-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--orange-primary);
    background: rgba(255, 124, 49, 0.1);
}
.order-item-name {
    font-size: 14px;
    color: var(--black-lightest);
    font-weight: 500;
}
.order-item-price-modern {
    font-size: 15px;
    color: var(--black-lighter);
    font-weight: 600;
}
.order-breakdown-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, #e8eaed 20%, #e8eaed 80%, transparent 100%);
    margin: 16px 0;
}
.order-breakdown-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0 0;
}
.order-total-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.order-total-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #fff;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
}
.order-total-label {
    font-size: 16px;
    color: var(--black-lighter);
    font-weight: 700;
}
.order-total-price {
    font-size: 22px;
    color: var(--orange-primary);
    font-weight: 700;
    letter-spacing: -0.5px;
}

/* Modern Payment Button */
.btn-payment-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 18px 24px;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border: none;
    border-radius: 12px;
    color: var(--black-lighter);
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 4px 12px rgba(255, 124, 49, 0.3);
    position: relative;
    overflow: hidden;
}
.btn-payment-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}
.btn-payment-modern:hover::before {
    left: 100%;
}
.btn-payment-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 124, 49, 0.4);
    background: linear-gradient(135deg, var(--orange-hover) 0%, var(--orange-primary) 100%);
}
.btn-payment-modern:active {
    transform: translateY(0);
}
.btn-payment-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
.payment-btn-icon {
    display: flex;
    align-items: center;
    font-size: 18px;
}
.payment-btn-text {
    flex: 1;
    text-align: center;
}
.payment-btn-arrow {
    display: flex;
    align-items: center;
    font-size: 18px;
    transition: transform 0.3s ease;
}
.btn-payment-modern:hover .payment-btn-arrow {
    transform: translateX(4px);
}
.payment-btn-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(0, 0, 0, 0.3);
    border-top-color: var(--black-lighter);
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Modern Order Summary Card */
.order-summary-card-modern {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}
.order-summary-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 24px;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: #fff;
}
.order-summary-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    backdrop-filter: blur(10px);
}
.order-summary-title-modern {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    letter-spacing: -0.3px;
}
.order-summary-body {
    padding: 24px;
}
.order-items-modern {
    margin-bottom: 16px;
}
.order-item-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f0f2f5;
}
.order-item-modern:last-of-type {
    border-bottom: none;
}
.order-item-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}
.order-item-icon-modern {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--orange-primary);
    background: rgba(255, 124, 49, 0.1);
    flex-shrink: 0;
}
.order-item-name-modern {
    font-size: 14px;
    color: var(--black-lightest);
    font-weight: 500;
}
.order-summary-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, #e8eaed 20%, #e8eaed 80%, transparent 100%);
    margin: 20px 0;
}
.order-total-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0 0;
}
.order-total-content {
    display: flex;
    align-items: center;
    gap: 12px;
}
.order-total-icon-modern {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
}
.order-total-label-modern {
    font-size: 18px;
    color: var(--black-lighter);
    font-weight: 700;
}
.order-total-price-modern {
    font-size: 24px;
    color: var(--orange-primary);
    font-weight: 700;
    letter-spacing: -0.5px;
}
.secure-checkout-modern {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #f0fdf4 0%, #f7fef7 100%);
    border-top: 1px solid #e8eaed;
}
.secure-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
    flex-shrink: 0;
}
.secure-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.secure-title {
    font-size: 14px;
    color: var(--black-lighter);
    font-weight: 700;
}
.secure-subtitle {
    font-size: 12px;
    color: var(--black-gray);
}
.btn-success {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    border-color: var(--orange-primary);
    color: var(--black-lighter);
    font-weight: 600;
}
.btn-success:hover {
    background: linear-gradient(135deg, var(--orange-hover) 0%, var(--orange-primary) 100%);
    border-color: var(--orange-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 124, 49, 0.3);
}
.btn-warning {
    background: #ffc107;
    border-color: #ffc107;
    color: #333;
}
.btn-warning:hover {
    background: #e0a800;
    border-color: #d39e00;
}
.secure-checkout { 
    text-align: center; 
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
    margin-top: 15px;
}
.secure-checkout i {
    color: #28a745;
    margin-right: 5px;
}
.success-icon { 
    font-size: 48px; 
    color: #28a745; 
}

@media (max-width: 768px) {
    .checkout-step {
        padding: 20px;
    }
    .checkout-header {
        flex-direction: column;
        align-items: flex-start;
    }
    /* Payment Methods Responsive */
    .payment-methods-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .payment-method-card-modern {
        min-height: 200px;
    }
    .payment-card-body {
        padding: 20px 16px;
    }
    .payment-card-title {
        font-size: 18px;
    }
    .payment-card-subtitle {
        font-size: 12px;
    }
    .payment-feature-badge {
        font-size: 11px;
        padding: 5px 10px;
    }
    /* Info Cards Responsive */
    .info-card-header {
        padding: 16px 20px;
        flex-wrap: wrap;
    }
    .info-card-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    .info-card-title {
        font-size: 16px;
    }
    .info-card-edit-btn {
        padding: 6px 12px;
        font-size: 12px;
        margin-top: 8px;
        width: 100%;
        justify-content: center;
    }
    .info-card-body {
        padding: 16px 20px;
    }
    .info-items-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .info-item {
        gap: 12px;
        padding: 12px 0;
    }
    .info-item-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    .info-item-label {
        font-size: 11px;
    }
    .info-item-value {
        font-size: 14px;
    }
    /* Order Breakdown Responsive */
    .order-breakdown-header {
        padding: 16px 20px;
    }
    .order-breakdown-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    .order-breakdown-title {
        font-size: 16px;
    }
    .order-breakdown-body {
        padding: 16px 20px;
    }
    .order-breakdown-item {
        padding: 12px 0;
        flex-wrap: wrap;
        gap: 8px;
    }
    .order-item-icon {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    .order-item-name {
        font-size: 13px;
    }
    .order-item-price-modern {
        font-size: 14px;
        width: 100%;
        text-align: right;
        padding-top: 4px;
    }
    .order-total-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    .order-total-label {
        font-size: 15px;
    }
    .order-total-price {
        font-size: 20px;
    }
    /* Payment Button Responsive */
    .btn-payment-modern {
        padding: 16px 20px;
        font-size: 15px;
        gap: 10px;
    }
    .payment-btn-icon {
        font-size: 16px;
    }
    .payment-btn-arrow {
        font-size: 16px;
    }
    /* Order Summary Responsive */
    .order-summary-header {
        padding: 20px;
    }
    .order-summary-icon-wrapper {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    .order-summary-title-modern {
        font-size: 18px;
    }
    .order-summary-body {
        padding: 20px;
    }
    .order-item-modern {
        padding: 12px 0;
        flex-wrap: wrap;
        gap: 8px;
    }
    .order-item-icon-modern {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    .order-item-name-modern {
        font-size: 13px;
    }
    .order-item-price-modern {
        font-size: 14px;
        width: 100%;
        text-align: right;
        padding-top: 4px;
    }
    .order-total-icon-modern {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
    .order-total-label-modern {
        font-size: 16px;
    }
    .order-total-price-modern {
        font-size: 20px;
    }
    .secure-checkout-modern {
        padding: 16px 20px;
    }
    .secure-badge {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    .secure-title {
        font-size: 13px;
    }
    .secure-subtitle {
        font-size: 11px;
    }
        }
    </style>
@endpush
@endsection