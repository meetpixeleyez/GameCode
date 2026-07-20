@extends($activeTemplate . 'layouts.frontend')
@section('meta_title', gs()->siteName(__('Payment Information')))
@section('meta_description', __('Enter your payment details securely to complete the purchase of your selected game source code products.'))
@section('meta_robots', 'noindex, follow')

@section('content')
    <section class="checkout-payment-page pt-60 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="payment-wrapper">
                        <h4 class="payment-title">@lang('Payment Information')</h4>
                        
                        <form action="{{ route('checkout.payment.process') }}" method="POST">
                            @csrf
                            
                            <div class="payment-methods">
                                <h5 class="mb-3">@lang('Select Payment Method')</h5>
                                
                                @foreach ($gatewayCurrency as $gateway)
                                    <div class="payment-method">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gateway" id="gateway_{{ $gateway->id }}" value="{{ $gateway->method_code }}" required>
                                            <label class="form-check-label" for="gateway_{{ $gateway->id }}">
                                                <div class="method-content">
                                                    <div class="method-info">
                                                        <h6>{{ $gateway->name }}</h6>
                                                        <p class="text-muted">{{ $gateway->method->name }}</p>
                                                    </div>
                                                    <div class="method-details">
                                                        @if ($gateway->fixed_charge > 0)
                                                            <span class="charge">@lang('Fixed Charge'): {{ showAmount($gateway->fixed_charge) }}</span>
                                                        @endif
                                                        @if ($gateway->percent_charge > 0)
                                                            <span class="charge">@lang('Percent Charge'): {{ $gateway->percent_charge }}%</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                
                                {{-- Wallet payment disabled per request
                                <div class="payment-method">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gateway" id="gateway_wallet" value="wallet" required>
                                        <label class="form-check-label" for="gateway_wallet">
                                            <div class="method-content">
                                                <div class="method-info">
                                                    <h6>@lang('Wallet Balance')</h6>
                                                    <p class="text-muted">@lang('Pay using your wallet balance')</p>
                                                </div>
                                                <div class="method-details">
                                                    <span class="balance">@lang('Available Balance'): {{ showAmount(auth()->user()->balance ?? 0) }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                --}}
                            </div>
                            
                            <div class="payment-actions mt-4">
                                <button type="submit" class="btn btn--base btn--lg w-100">
                                    @lang('Complete Payment')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="order-summary">
                        <div class="order-summary__inner padding">
                            <h5 class="order-summary__title">@lang('Order Summary')</h5>
                            <ul class="order-summary__list">
                                @foreach ($cartItems as $cartItem)
                                    @php
                                        $additionalServicesPrice = ($cartItem->reskin_selected ? $cartItem->product->reskin_price : 0) + 
                                                                  ($cartItem->publish_selected ? $cartItem->product->publish_price : 0) + 
                                                                  ($cartItem->store_optimization_selected ? $cartItem->product->store_optimization_price : 0);
                                        $itemTotal = $cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + $additionalServicesPrice;
                                    @endphp
                                    <li class="order-summary__item product flex-between">
                                        <span class="text">{{ @$cartItem->title }}</span>
                                        <span class="price">
                                            {{ gs('cur_sym') }}{{ showAmount($itemTotal, currencyFormat: false) }}
                                        </span>
                                    </li>
                                    
                                    @if ($cartItem->is_extended)
                                        <li class="order-summary__item flex-between">
                                            <span class="text">@lang('Extended Support')</span>
                                            <span class="price">
                                                {{ gs('cur_sym') }}{{ showAmount($cartItem->extended_amount, currencyFormat: false) }}
                                            </span>
                                        </li>
                                    @endif
                                    
                                    @if ($cartItem->reskin_selected && $cartItem->product->reskin_price > 0)
                                        <li class="order-summary__item flex-between">
                                            <span class="text">@lang('Reskin Service')</span>
                                            <span class="price">
                                                {{ gs('cur_sym') }}{{ showAmount($cartItem->product->reskin_price, currencyFormat: false) }}
                                            </span>
                                        </li>
                                    @endif
                                    
                                    @if ($cartItem->publish_selected && $cartItem->product->publish_price > 0)
                                        <li class="order-summary__item flex-between">
                                            <span class="text">@lang('Publish Service')</span>
                                            <span class="price">
                                                {{ gs('cur_sym') }}{{ showAmount($cartItem->product->publish_price, currencyFormat: false) }}
                                            </span>
                                        </li>
                                    @endif
                                    
                                    @if ($cartItem->store_optimization_selected && $cartItem->product->store_optimization_price > 0)
                                        <li class="order-summary__item flex-between">
                                            <span class="text">@lang('Store Optimization Service')</span>
                                            <span class="price">
                                                {{ gs('cur_sym') }}{{ showAmount($cartItem->product->store_optimization_price, currencyFormat: false) }}
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                                
                                <li class="order-summary__item flex-between">
                                    <span class="text">@lang('Subtotal')</span>
                                    <span class="price subtotal">
                                        {{ gs('cur_sym') }}{{ showAmount($subtotal, currencyFormat: false) }}
                                    </span>
                                </li>
                                
                                @if ($discount > 0)
                                    <li class="order-summary__item flex-between">
                                        <span class="text">@lang('Discount')</span>
                                        <span class="price text-success">
                                            -{{ gs('cur_sym') }}{{ showAmount($discount, currencyFormat: false) }}
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="order-summary__total flex-between padding py-3">
                            <h6 class="mb-0">@lang('Total')</h6>
                            <h6 class="mb-0 total">
                                {{ gs('cur_sym') }}{{ showAmount($total, currencyFormat: false) }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .payment-wrapper {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .payment-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .payment-methods {
            margin-bottom: 30px;
        }
        
        .payment-method {
            margin-bottom: 15px;
            padding: 20px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #007bff;
            background: #f8f9fa;
        }
        
        .method-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .method-info h6 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .method-info p {
            font-size: 14px;
            margin-bottom: 0;
            color: #6c757d;
        }
        
        .method-details {
            text-align: right;
        }
        
        .method-details .charge,
        .method-details .balance {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 2px;
        }
        
        .order-summary {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .order-summary__title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .order-summary__item {
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
            font-size: 14px;
        }
        
        .order-summary__item:last-child {
            border-bottom: none;
        }
        
        .order-summary__total {
            background: #f8f9fa;
            border-top: 2px solid #007bff;
        }
        
        .order-summary__total h6 {
            font-size: 18px;
            font-weight: 600;
        }
    </style>
@endpush
