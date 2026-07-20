@extends($activeTemplate . 'layouts.frontend')
@section('meta_title', gs()->siteName(__('Shopping Cart')))
@section('meta_description', __('Review your selected Unity game source code items and proceed to checkout for secure purchase.'))
@section('meta_robots', 'noindex, follow')
@section('content')
    <section class="shopping-cart-page pt-60 pb-120">
        <div class="container">
            <form action="{{ route('checkout.index') }}" method="GET">
                <div class="row gy-4">
                    <div class="col-lg-8">
                        <div class="shopping-cart-wrapper">
                            @foreach ($cartItems as $cartItem)
                                @php
                                    $isCampaignProduct = $cartItem->product->campaignProduct?->exists();
                                @endphp
                                <div class="shopping-cart" data-id="{{ $cartItem->id }}">
                                    <div class="shopping-cart__inner">
                                        <div class="shopping-cart__thumb">
                                            <a href="{{ route('product.details', $cartItem->product->slug) }}"
                                                class="link">
                                                <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($cartItem->product, 'thumbnail')) }}"
                                                    alt="@lang('Cart Item')" class="fit-image" loading="lazy" />
                                            </a>
                                        </div>
                                        <div class="shopping-cart__content">
                                            <h6 class="shopping-cart__title">
                                                <a href="{{ route('product.details', $cartItem->product->slug) }}"
                                                    class="link">{{ @$cartItem->title }}</a>
                                            </h6>
                                            <span class="shopping-cart__category">
                                                @lang('Category') : <a
                                                    href="{{ route('products', ['category' => $cartItem->product->category_id]) }}">
                                                    {{ @$cartItem->category }}</a>
                                            </span>
                                            <div class="form--check form--check--sm">
                                                <input class="form-check-input extended" type="checkbox"
                                                    id="extend-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}"
                                                    data-product="{{ @$cartItem->product }}"
                                                    data-extended-price="{{ $cartItem->product?->twelveMonthExtendedFee() ?? 0 }}"
                                                    @checked($cartItem->is_extended)>
                                                <label class="form-check-label" for="extend-{{ $cartItem->id }}">
                                                    @lang('Extend support to 12 months').+{{ showAmount($cartItem->product->twelveMonthExtendedFee()) }}
                                                </label>
                                            </div>
                                            
                                            <!-- Additional Services Checkboxes in Cart -->
                                            @if($cartItem->product->reskin_price > 0 || $cartItem->product->publish_price > 0 || $cartItem->product->store_optimization_price > 0)
                                                <div class="additional-services-checkboxes">
                                                    @if($cartItem->product->reskin_price > 0)
                                                        <div class="form--check form--check--sm">
                                                            <input class="form-check-input additional-service-checkbox" type="checkbox"
                                                                id="reskin-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}"
                                                                data-service="reskin" data-price="{{ $cartItem->product->reskin_price }}"
                                                                @checked($cartItem->reskin_selected)>
                                                            <label class="form-check-label" for="reskin-{{ $cartItem->id }}">
                                                                @lang('Reskin').+{{ showAmount($cartItem->product->reskin_price) }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($cartItem->product->publish_price > 0)
                                                        <div class="form--check form--check--sm">
                                                            <input class="form-check-input additional-service-checkbox" type="checkbox"
                                                                id="publish-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}"
                                                                data-service="publish" data-price="{{ $cartItem->product->publish_price }}"
                                                                @checked($cartItem->publish_selected)>
                                                            <label class="form-check-label" for="publish-{{ $cartItem->id }}">
                                                                @lang('Publish').+{{ showAmount($cartItem->product->publish_price) }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($cartItem->product->store_optimization_price > 0)
                                                        <div class="form--check form--check--sm">
                                                            <input class="form-check-input additional-service-checkbox" type="checkbox"
                                                                id="store-opt-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}"
                                                                data-service="store_optimization" data-price="{{ $cartItem->product->store_optimization_price }}"
                                                                @checked($cartItem->store_optimization_selected)>
                                                            <label class="form-check-label" for="store-opt-{{ $cartItem->id }}">
                                                                @lang('Store Optimization').+{{ showAmount($cartItem->product->store_optimization_price) }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- Additional Services in Cart -->
                                            @if($cartItem->product->reskin_price > 0 || $cartItem->product->publish_price > 0 || $cartItem->product->store_optimization_price > 0)
                                                <div class="additional-services-cart">
                                                    <h6 class="services-title">@lang('Additional Services'):</h6>
                                                    
                                                    @if($cartItem->reskin_selected && $cartItem->product->reskin_price > 0)
                                                        <div class="service-item">
                                                            <span class="service-name">@lang('Reskin')</span>
                                                            <span class="service-price">+{{ showAmount($cartItem->product->reskin_price) }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($cartItem->publish_selected && $cartItem->product->publish_price > 0)
                                                        <div class="service-item">
                                                            <span class="service-name">@lang('Publish')</span>
                                                            <span class="service-price">+{{ showAmount($cartItem->product->publish_price) }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($cartItem->store_optimization_selected && $cartItem->product->store_optimization_price > 0)
                                                        <div class="service-item">
                                                            <span class="service-name">@lang('Store Optimization')</span>
                                                            <span class="service-price">+{{ showAmount($cartItem->product->store_optimization_price) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="cart-action">
                                                <button type="button" class="cart-action__item text--danger deleteCartItem"
                                                    data-question="@lang('Are you sure to remove this item ? ')"
                                                    data-action="{{ route('cart.delete', $cartItem->product->id) }}"
                                                    data-id="{{ $cartItem->id }}">
                                                    <span class="icon"><i class="icon-deletee"></i></span>
                                                    <span class="text">@lang('Remove')</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shopping-cart__price">
                                        <span class="text-dark">{{ gs('cur_sym') }}
                                            <span
                                                class="item-price">{{ showAmount($cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + ($cartItem->reskin_selected ? $cartItem->product->reskin_price : 0) + ($cartItem->publish_selected ? $cartItem->product->publish_price : 0) + ($cartItem->store_optimization_selected ? $cartItem->product->store_optimization_price : 0), currencyFormat: false) }}</span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="empty-text {{ count($cartItems) == 0 ? '' : 'd-none' }}">
                                <x-empty-list title="No Items In Cart" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <div class="order-summary__inner padding">
                                <h5 class="order-summary__title">@lang('Order Summary')</h5>
                                <ul class="order-summary__list">
                                    @php
                                        $subtotal = 0;
                                    @endphp
                                    @foreach ($cartItems as $cartItem)
                                        @php
                                            $additionalServicesPrice = ($cartItem->reskin_selected ? $cartItem->product->reskin_price : 0) + 
                                                                      ($cartItem->publish_selected ? $cartItem->product->publish_price : 0) + 
                                                                      ($cartItem->store_optimization_selected ? $cartItem->product->store_optimization_price : 0);
                                            $subtotal += $cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + $additionalServicesPrice;
                                        @endphp
                                        <li class="order-summary__item product flex-between">
                                            <span class="text">{{ @$cartItem->title }}</span>
                                            <span class="price">
                                                {{ gs('cur_sym') }}<span id="cart-item-data-{{ $cartItem->id }}">
                                                    {{ showAmount($cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + $additionalServicesPrice, currencyFormat: false) }}</span>
                                            </span>
                                        </li>
                                    @endforeach
                                    <li class="order-summary__item flex-between">
                                        <span class="text"> @lang('Subtotal')</span>
                                        <span class="price subtotal">
                                            {{ gs('cur_sym') }}{{ showAmount($subtotal, currencyFormat: false) }}</span>
                                    </li>
                                    @if ($isCoupon)
                                        <li class="order-summary__item flex-between">
                                            <span class="text">@lang('Discount')</span>
                                            <span class="discount_amount">{{ gs('cur_sym') }}0</span>
                                        </li>
                                    @endif
                                </ul>
                                @if ($isCoupon)
                                    <div class="coupon-section mt-2">
                                        <input type="text" class="form--control form--control--sm coupon_code mb-2"
                                            placeholder="@lang('Enter Coupon Code')" />
                                        <button type="submit"
                                            class="btn btn--base btn--sm apply_coupon">@lang('Apply')</button>
                                    </div>
                                @endif
                            </div>

                            <div class="order-summary__total flex-between padding py-3">
                                <h6 class="mb-0">@lang('Total')</h6>
                                <h6 class="mb-0 total">
                                    {{ gs('cur_sym') }}{{ showAmount($subtotal, currencyFormat: false) }}</h6>
                            </div>
                            <div class="order-summary__button padding">
                                <input type="hidden" name="amount_after_discount" id="amount_after_discount"
                                    value="{{ $subtotal }}">
                                <button type="submit" class="btn btn--base btn--sm w-100 checkout-btn"
                                    @disabled(count($cartItems) == 0)>@lang('Checkout')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <x-confirmation-modal frontend="true" />
@endsection

@push('style')
    <style>
        .fit-image {
            height: 72px !important;
            width: 72px !important;
        }

        .coupon-section {
            position: relative;
        }

        .coupon-section input {
            padding-right: 65px !important;
        }

        .coupon-section button {
            position: absolute;
            right: 4px;
            top: 4px;
            padding: 7.5px 8px;
            font-size: 12px;
        }

        /* Additional Services in Cart Styles */
        .additional-services-cart {
            margin: 15px 0;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .services-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: 13px;
        }

        .service-name {
            color: #6c757d;
            font-weight: 500;
        }

        .service-price {
            color: #28a745;
            font-weight: 600;
        }
        
        /* Additional Services Checkboxes Styles */
        .additional-services-checkboxes {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        
        .additional-services-checkboxes .form--check {
            margin-bottom: 8px;
        }
        
        .additional-services-checkboxes .form--check:last-child {
            margin-bottom: 0;
        }
        
        .additional-services-checkboxes .form-check-label {
            font-size: 13px;
            color: #495057;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            let subtotal = Number(`{{ $subtotal }}`);

            $(document).on('change', '.extended', function() {

                let product = $(this).data('product');

                if (!product) {
                    return;
                }

                let extendedPrice = parseFloat($(this).data('extended-price')) ?? 0;

                let productId = $(this).data('id');
                let url = "{{ route('cart.extended.toggle', ':productId') }}";
                url = url.replace(':productId', productId);

                let productPrice = +Number($(this).closest('.shopping-cart')
                    .find('.shopping-cart__price .item-price')
                    .text().replace(",", ""));

                let price = 0;
                if ($(this).is(':checked')) {
                    price = productPrice + extendedPrice;
                } else {
                    price = productPrice - extendedPrice;
                }

                $(this).closest('.shopping-cart')
                    .find('.shopping-cart__price .item-price')
                    .text(`${price.toFixed(2)}`);

                $(`#cart-item-data-${productId}`).text(price.toFixed(2));

                $.ajax({
                    type: 'GET',
                    url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        message
                    }) {
                        calculate();
                    }
                });
            });

            // Handle additional services toggle
            $(document).on('change', '.additional-service-checkbox', function() {
                let checkbox = $(this);
                let cartId = checkbox.closest('.shopping-cart').data('id');
                let service = checkbox.data('service');
                let selected = checkbox.is(':checked') ? 1 : 0;
                
                let url = "{{ route('cart.additional.service.toggle', ':cartId') }}";
                url = url.replace(':cartId', cartId);
                
                $.ajax({
                    type: 'POST',
                    url,
                    data: {
                        service: service,
                        selected: selected,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update the cart item price display
                            let cartItem = checkbox.closest('.shopping-cart');
                            let priceElement = cartItem.find('.shopping-cart__price .item-price');
                            let cartItemData = $(`#cart-item-data-${cartId}`);
                            
                            // Update the price display
                            priceElement.text(response.total_price.toFixed(2));
                            cartItemData.text(response.total_price.toFixed(2));
                            
                            // Recalculate totals
                            calculate();
                            notify(response.status, response.message);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        // Revert checkbox state on error
                        checkbox.prop('checked', !selected);
                        notify('error', 'Failed to update service selection');
                    }
                });
            });

            function calculate() {
                subtotal = 0;
                let curSym = "{{ @gs('cur_sym') }}";

                $('.shopping-cart__price .item-price').each(function() {
                    let price = +Number($(this).text().replace(",", ""));
                    subtotal += price;
                });

                $('.discount_amount').text(`${curSym} 0`);
                $('.coupon_code').val('')

                $('.subtotal').text(curSym + subtotal.toFixed(2));
                updateTotal(subtotal);
            }

            function updateTotal(subtotal, discount = 0) {
                let curSym = "{{ @gs('cur_sym') }}";
                const total = subtotal - discount;
                $('.total').text(curSym + total.toFixed(2));
            }

            $('.apply_coupon').on('click', function(e) {
                e.preventDefault();
                let curSym = "{{ @gs('cur_sym') }}";
                let couponCode = $('.coupon_code').val();

                let discount = parseFloat($('.discount_amount').text().replace(",", "")) || 0;
                let total = subtotal - discount;
                $('.total').text(curSym + total.toFixed(2));

                const cartIds = [];
                $('.shopping-cart').each(function() {
                    const id = $(this).attr('data-id');
                    if (id) {
                        cartIds.push(parseInt(id, 10));
                    }
                });

                $.ajax({
                    url: "{{ route('cart.apply.coupon') }}",
                    type: "GET",
                    data: {
                        coupon_code: couponCode,
                        subtotal: subtotal,
                        cart_ids: cartIds
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.discount_amount').text(curSym + response.discount.toFixed(2));
                            const discount = parseFloat(response.discount) || 0;
                            const total = subtotal - discount;
                            $('.total').text(curSym + total.toFixed(2));
                            notify('success', response.message);
                        } else {
                            notify('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        notify('error', "@lang('An error occurred while applying the coupon.')");
                    }
                });

            });

            $('.deleteCartItem').on('click', function(e) {
                e.preventDefault();

                let url = $(this).data('action');
                let id = $(this).data('id');
                let clickEl = $(this);
                $.ajax({
                    type: 'DELETE',
                    url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function({
                        status,
                        message
                    }) {
                        $(clickEl).closest('.shopping-cart').remove();
                        $(`#cart-item-data-${id}`).closest('.order-summary__item').remove();

                        let cartLength = +Number($('.cart-button__qty').first().text());

                        cartLength--;
                        $('.cart-button__qty').text(cartLength);
                        if (cartLength == 0) {
                            $('.checkout-btn').attr('disabled', true);
                            $('.empty-text').removeClass('d-none');
                        }

                        calculate();
                        notify(status, message);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
