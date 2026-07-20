@php
    $productInCart = productInCart($product->id);

    $hasDiscount = $product->campaign_product_price;
    $hasCommercialDiscount = $product->campaign_product_commercial_price;
@endphp

<div class="common-sidebar__item">
    <h6 class="common-sidebar__title">
        @if ($product->is_free)
            @lang('Download')
        @else
            @lang('Add to Cart')
        @endif
    </h6>
    <div class="common-sidebar__content">
        <form data-in-cart="{{ intval($productInCart) }}" data-delete-route="{{ route('cart.delete', $product->id) }}"
              action="{{ route('cart.store') }}" method="POST" id="cartActionForm" onsubmit="return false;">
            @csrf
            <input type="hidden" name="product_id" value="{{ @$product->id }}">

            @if (!$product->is_free)
                <div class="common-sidebar__license">
                    <div class="common-sidebar__inner flex-between">
                        <div class="form--radio style-success flex-align">
                            <input class="form-check-input mt-0" type="radio" name="license" id="personalLicense"
                                   value="1" checked>
                            <label class="form-check-label w-auto" for="personalLicense"> @lang('Personal License') </label>
                            <a href="#" class="common-sidebar__info ms-1" data-bs-toggle="tooltip"
                               data-bs-placement="top"></a>
                        </div>
                        <span class="common-sidebar__price">
                            @if ($hasDiscount[0])
                                <del>{{ showAmount($product->productPrice('personal')) }}</del>
                                <span class="text-success">{{ showAmount($hasDiscount[1]) }}</span>
                            @else
                                {{ showAmount($product->productPrice('personal')) }}
                            @endif
                        </span>
                    </div>
                    <ul class="license-list">
                        @foreach (gs('personal_license_features') ?? [] as $feature)
                            <li class="license-list__item">
                                <span class="icon"><i class="icon-Bulet-Icon"></i></span>
                                {{ __($feature) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="common-sidebar__license">
                    <div class="common-sidebar__inner flex-between">
                        <div class="form--radio style-success flex-align">
                            <input class="form-check-input mt-0" type="radio" name="license" id="commercialLicense"
                                   value="2">
                            <label class="form-check-label w-auto" for="commercialLicense"> @lang('Commercial license') </label>
                            <a href="#" class="common-sidebar__info ms-1" data-bs-toggle="tooltip"
                               data-bs-placement="top"></a>
                        </div>
                        <span class="common-sidebar__price">
                            @if ($hasCommercialDiscount[0])
                                <del>{{ showAmount($product->productPrice('commercial')) }}</del>
                                <span class="text-success">{{ showAmount($hasCommercialDiscount[1]) }}</span>
                            @else
                                {{ showAmount($product->productPrice('commercial')) }}
                            @endif
                        </span>
                    </div>
                    <ul class="license-list">
                        @foreach (gs('commercial_license_features') ?? [] as $feature)
                            <li class="license-list__item">
                                <span class="icon"><i class="icon-Bulet-Icon"></i></span>
                                {{ __(@$feature) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="common-sidebar__license">
                    <div class="common-sidebar__inner flex-between">
                        <div class="form--radio style-success flex-align">
                            <input class="form-check-input mt-0" type="radio" name="license" id="personalLicense"
                                   value="1" checked>
                            <label class="form-check-label w-auto" for="personalLicense"> @lang('Personal License') </label>
                            <a href="#" class="common-sidebar__info ms-1" data-bs-toggle="tooltip"
                               data-bs-placement="top"></a>
                        </div>
                        <span class="common-sidebar__price">@lang('Free')</span>
                    </div>
                </div>
            @endif
            
            <!-- Additional Services Section -->
            @if (!$product->is_free)
                <div class="additional-services-section">
                    <h6 class="additional-services-title">@lang('Additional Services')</h6>
                    
                    <div class="service-option">
                        <div class="form--check">
                            <input class="form-check-input additional-service-checkbox" type="checkbox" 
                                   id="reskinService" name="reskin_selected" value="1" 
                                   data-service="reskin" data-price="{{ $product->reskin_price }}">
                            <label class="form-check-label" for="reskinService">
                                <span class="service-name">@lang('Reskin')</span>
                                <span class="service-price">+{{ showAmount($product->reskin_price) }}</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="service-option">
                        <div class="form--check">
                            <input class="form-check-input additional-service-checkbox" type="checkbox" 
                                   id="publishService" name="publish_selected" value="1" 
                                   data-service="publish" data-price="{{ $product->publish_price }}">
                            <label class="form-check-label" for="publishService">
                                <span class="service-name">@lang('Publish')</span>
                                <span class="service-price">+{{ showAmount($product->publish_price) }}</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="service-option">
                        <div class="form--check">
                            <input class="form-check-input additional-service-checkbox" type="checkbox" 
                                   id="storeOptimizationService" name="store_optimization_selected" value="1" 
                                   data-service="store_optimization" data-price="{{ $product->store_optimization_price }}">
                            <label class="form-check-label" for="storeOptimizationService">
                                <span class="service-name">@lang('Store Optimization')</span>
                                <span class="service-price">+{{ showAmount($product->store_optimization_price) }}</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="total-additional-price">
                        <span class="total-label">@lang('Total Additional Services'):</span>
                        <span class="total-amount" id="totalAdditionalPrice">{{ showAmount(0) }}</span>
                    </div>
                </div>
                
                <style>
                    .additional-services-section {
                        margin: 20px 0;
                        padding: 15px;
                        background: #f8f9fa;
                        border-radius: 8px;
                        border: 1px solid #e9ecef;
                    }
                    
                    .additional-services-title {
                        font-size: 16px;
                        font-weight: 600;
                        margin-bottom: 15px;
                        color: #333;
                        border-bottom: 2px solid #007bff;
                        padding-bottom: 8px;
                    }
                    
                    .service-option {
                        margin-bottom: 12px;
                        padding: 10px;
                        background: white;
                        border-radius: 6px;
                        border: 1px solid #dee2e6;
                        transition: all 0.3s ease;
                    }
                    
                    .service-option:hover {
                        border-color: #007bff;
                        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
                    }
                    
                    .service-option .form--check {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }
                    
                    .service-name {
                        font-weight: 500;
                        color: #495057;
                    }
                    
                    .service-price {
                        font-weight: 600;
                        color: #28a745;
                        font-size: 14px;
                    }
                    
                    .total-additional-price {
                        margin-top: 15px;
                        padding: 12px;
                        background: #e3f2fd;
                        border-radius: 6px;
                        border: 1px solid #bbdefb;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    
                    .total-label {
                        font-weight: 500;
                        color: #1976d2;
                    }
                    
                    .total-amount {
                        font-weight: 600;
                        color: #1976d2;
                        font-size: 16px;
                    }
                </style>
            @endif
            
            @if (!$product->is_free)
                <div class="common-sidebar__button">
                    <button type="submit"
                            class="cart_submit_btn btn btn--{{ $productInCart ? 'danger' : 'base' }} w-100">
                        <i class="fa fa-spinner d-none fa-spin"></i>
                        <span class="text-box">
                            <span class="icon">
                                <i class="icon-Add-to-Cart-Button"></i>
                            </span>
                            <span class="text">@lang($productInCart ? 'Remove from Cart' : 'Add to Cart')</span>
                        </span>
                    </button>
                </div>
        </form>
        @endif
        
        <!-- Contact Support Section -->
        <div class="contact-support-section">
            <a href="https://wa.me/919408212310?text=Hello%20there!%20I%20need%20help%20with%20{{ urlencode($product->title) }}" 
               target="_blank" class="contact-support-btn" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i>
                @lang('Need Help? Contact Support')
            </a>
        </div>
        
        <style>
            .contact-support-section {
                margin-top: 20px;
                text-align: center;
            }
            
            .contact-support-btn {
                display: inline-block;
                padding: 12px 20px;
                background: linear-gradient(135deg, #25d366, #128c7e);
                color: white !important;
                font-weight: 600;
                font-size: 14px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.3s ease;
                width: 100%;
                text-align: center;
                box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            }
            
            .contact-support-btn:hover {
                background: linear-gradient(135deg, #128c7e, #0d6b5a);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
                color: white !important;
            }
            
            .contact-support-btn i {
                margin-right: 8px;
                font-size: 16px;
            }
        </style>
        
        @if ($product->is_free)
            <div class="common-sidebar__button">
                <a href="{{ route('user.author.product.free.download', $product->slug) }}?time={{ time() }}"
                   class="cart_submit_btn download_btn btn btn--base w-100">
                    <i class="fa fa-spinner d-none fa-spin"></i>
                    <span class="text-box">
                        <span class="icon">
                            <i class="las la-download"></i>
                        </span>
                        <span class="text">@lang('download')</span>
                    </span>
                </a>
            </div>
        @endif
    </div>
</div>


@push('script')
    <script>
        "use strict";

        // Additional services functionality
        let totalAdditionalPrice = 0;
        
        $('.additional-service-checkbox').on('change', function() {
            const checkbox = $(this);
            const service = checkbox.data('service');
            const price = parseFloat(checkbox.data('price')) || 0;
            const isChecked = checkbox.is(':checked');
            
            if (isChecked) {
                totalAdditionalPrice += price;
            } else {
                totalAdditionalPrice -= price;
            }
            
            // Update total display
            $('#totalAdditionalPrice').text('{{ gs("cur_sym") }}' + totalAdditionalPrice.toFixed(2));
            
            // Update form data
            if (isChecked) {
                checkbox.val('1');
            } else {
                checkbox.val('0');
            }
        });

        $('#cartActionForm').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            $('.fa-spinner').removeClass('d-none');
            $('.text-box').addClass('d-none');

            const form = $(this);
            const deleteRoute = form.data('delete-route');
            let url = form.attr('action');
            let productInCart = +form.data('in-cart');
            url = productInCart ? deleteRoute : url;
            const type = productInCart ? 'DELETE' : 'POST';

            $.ajax({
                type,
                url,
                data: form.serialize(),
                dataType: 'json',
                async: true,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response){
                    console.log('AJAX Success:', response);
                    
                    // Handle both object and destructured response
                    const result = response || {};
                    const status = result.status || 'error';
                    const message = result.message || 'Operation completed';
                    const cartQty = result.cartQty || 0;

                    if (status === 'success') {
                        form.data('in-cart', productInCart === 0 ? 1 : 0);
                        if ($('.cart-button__qty').length) {
                            $('.cart-button__qty').text(cartQty);
                        }

                        $('.fa-spinner').addClass('d-none');
                        $('.text-box').removeClass('d-none');

                        const cartSubmitBtn = $('.cart_submit_btn');
                        if (productInCart === 0) {
                            if (typeof incCartQty === 'function') {
                                incCartQty();
                            }
                            cartSubmitBtn
                                .removeClass('btn--base')
                                .addClass('btn--danger')
                                .find('.text')
                                .text("@lang('Remove from cart')");
                            
                            // Show add to cart popup
                            setTimeout(function() {
                                showAddToCartPopup();
                            }, 100);
                        } else {
                            if (typeof decCartQty === 'function') {
                                decCartQty();
                            }
                            cartSubmitBtn
                                .removeClass('btn--danger')
                                .addClass('btn--base')
                                .find('.text')
                                .text("@lang('Add to cart')");
                        }
                    } else {
                        $('.fa-spinner').addClass('d-none');
                        $('.text-box').removeClass('d-none');
                    }

                    if (typeof notify === 'function') {
                        notify(status, message);
                    }
                },
                error: function(xhr, status, error) {
                    $('.fa-spinner').addClass('d-none');
                    $('.text-box').removeClass('d-none');
                    
                    let errorMessage = '@lang("An error occurred. Please try again.")';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch(e) {
                            // Not JSON, use default message
                        }
                    }
                    
                    notify('error', errorMessage);
                }
            });
            
            return false;
        });
        
        // Function to show add to cart popup
        function showAddToCartPopup() {
            console.log('Showing add to cart popup');
            
            const productTitle = "{{ $product->title }}";
            const productImage = "{{ getImage(getFilePath('productThumbnail') . productFilePath($product, 'thumbnail')) }}";
            const sellerName = "{{ @$product->author->fullname ?? 'Unknown' }}";
            
            // Get selected license price dynamically
            let productPrice = '';
            
            // Check for radio buttons first (current implementation)
            const selectedLicense = $('input[name="license"]:checked');
            if (selectedLicense.length) {
                const licenseType = selectedLicense.val();
                const licensePriceElement = selectedLicense.closest('.common-sidebar__license').find('.common-sidebar__price');
                
                if (licensePriceElement.length) {
                    // Get price from the price element (this will include HTML like del tags)
                    productPrice = licensePriceElement.html();
                } else {
                    // Fallback: Calculate price based on license type
                    const personalPrice = {{ $hasDiscount[0] ? $hasDiscount[1] : $product->productPrice('personal') }};
                    const personalOriginalPrice = {{ $product->productPrice('personal') }};
                    const personalHasDiscount = {{ $hasDiscount[0] ? 'true' : 'false' }};
                    
                    const commercialPrice = {{ $hasCommercialDiscount[0] ? $hasCommercialDiscount[1] : $product->productPrice('commercial') }};
                    const commercialOriginalPrice = {{ $product->productPrice('commercial') }};
                    const commercialHasDiscount = {{ $hasCommercialDiscount[0] ? 'true' : 'false' }};
                    
                    if (licenseType === '2') {
                        // Commercial License
                        if (commercialHasDiscount && commercialPrice < commercialOriginalPrice) {
                            productPrice = '<del>' + formatPrice(commercialOriginalPrice) + '</del> <span class="text-success">' + formatPrice(commercialPrice) + '</span>';
                        } else {
                            productPrice = formatPrice(commercialPrice);
                        }
                    } else {
                        // Personal License
                        if (personalHasDiscount && personalPrice < personalOriginalPrice) {
                            productPrice = '<del>' + formatPrice(personalOriginalPrice) + '</del> <span class="text-success">' + formatPrice(personalPrice) + '</span>';
                        } else {
                            productPrice = formatPrice(personalPrice);
                        }
                    }
                }
            } else {
                // Check for dropdown (if implemented)
                const licenseSelect = $('#licenseSelect');
                if (licenseSelect.length && licenseSelect.val()) {
                    const selectedOption = licenseSelect.find('option:selected');
                    const price = parseFloat(selectedOption.data('price'));
                    const originalPrice = parseFloat(selectedOption.data('original-price'));
                    const hasDiscount = selectedOption.data('has-discount') === '1';
                    
                    if (hasDiscount && price < originalPrice) {
                        productPrice = formatPrice(originalPrice) + ' <span style="text-decoration: line-through; opacity: 0.7;">' + formatPrice(price) + '</span>';
                    } else {
                        productPrice = formatPrice(price);
                    }
                } else {
                    // Fallback to personal license price
                    productPrice = "{{ showAmount($product->productPrice('personal')) }}";
                }
            }
            
            function formatPrice(price) {
                return '{{ gs("cur_sym") }}' + price.toFixed(2);
            }
            
            const popupHtml = `
                        <div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered cart-popup-dialog">
                                <div class="modal-content cart-popup-modal">
                                    <button type="button" class="cart-popup-close" data-bs-dismiss="modal" aria-label="Close">
                                        <i class="las la-times"></i>
                                    </button>
                                    <div class="cart-popup-header">
                                        <div class="cart-popup-success-icon">
                                            <div class="success-checkmark">
                                                <i class="las la-check"></i>
                                            </div>
                                        </div>
                                        <h4 class="cart-popup-title">@lang('Item added to your cart')</h4>
                                    </div>
                                    <div class="cart-popup-body">
                                        <div class="cart-popup-item-modern">
                                            <div class="cart-popup-item-image-wrapper">
                                                <div class="cart-popup-image-badge">
                                                    <i class="las la-shopping-cart"></i>
                                                </div>
                                                <img src="${productImage}" alt="${productTitle}" class="cart-popup-item-image">
                                            </div>
                                            <div class="cart-popup-item-content">
                                                <h5 class="cart-popup-item-title">${productTitle}</h5>
                                                <p class="cart-popup-item-author">
                                                    <i class="las la-user"></i>
                                                    <span>@lang('by') ${sellerName}</span>
                                                </p>
                                                <div class="cart-popup-item-price">
                                                    <i class="las la-tag"></i>
                                                    <span>${productPrice}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="cart-popup-divider"></div>
                                        
                                        <div class="cart-popup-actions-modern">
                                            <button type="button" class="cart-popup-btn-secondary" data-bs-dismiss="modal">
                                                <i class="las la-arrow-left"></i>
                                                <span>@lang('Keep Browsing')</span>
                                            </button>
                                            <a href="{{ route('checkout.index') }}" class="cart-popup-btn-primary">
                                                <span>@lang('Go to Checkout')</span>
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                        .cart-popup-dialog {
                            max-width: 500px;
                            width: 100%;
                            margin: 0 auto;
                        }
                        .cart-popup-modal {
                            border: none;
                            border-radius: 20px;
                            overflow: hidden;
                            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                            width: 100%;
                        }
                        .cart-popup-close {
                            position: absolute;
                            top: 15px;
                            right: 15px;
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            background: rgba(0, 0, 0, 0.05);
                            border: none;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: #666;
                            font-size: 18px;
                            z-index: 10;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        }
                        .cart-popup-close:hover {
                            background: rgba(255, 124, 49, 0.1);
                            color: #ff7c31;
                            transform: rotate(90deg);
                        }
                        .cart-popup-header {
                            background: linear-gradient(135deg, #ff7c31 0%, #e65d1f 100%);
                            padding: 40px 30px 30px;
                            text-align: center;
                            position: relative;
                        }
                        .cart-popup-success-icon {
                            margin-bottom: 20px;
                            display: flex;
                            justify-content: center;
                        }
                        .success-checkmark {
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            background: rgba(255, 255, 255, 0.2);
                            backdrop-filter: blur(10px);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border: 3px solid rgba(255, 255, 255, 0.3);
                            animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                        }
                        .success-checkmark i {
                            font-size: 40px;
                            color: #fff;
                            animation: checkmark 0.6s ease 0.3s both;
                        }
                        @keyframes scaleIn {
                            from {
                                transform: scale(0);
                            }
                            to {
                                transform: scale(1);
                            }
                        }
                        @keyframes checkmark {
                            0% {
                                transform: scale(0) rotate(-45deg);
                                opacity: 0;
                            }
                            50% {
                                transform: scale(1.2) rotate(10deg);
                            }
                            100% {
                                transform: scale(1) rotate(0deg);
                                opacity: 1;
                            }
                        }
                        .cart-popup-title {
                            color: #fff;
                            font-size: 24px;
                            font-weight: 700;
                            margin: 0;
                            letter-spacing: -0.5px;
                        }
                        .cart-popup-body {
                            padding: 30px;
                            background: #fff;
                        }
                        .cart-popup-item-modern {
                            display: flex;
                            align-items: flex-start;
                            gap: 20px;
                            padding: 20px;
                            background: linear-gradient(135deg, rgba(255, 124, 49, 0.05) 0%, rgba(255, 124, 49, 0.02) 100%);
                            border-radius: 16px;
                            border: 1px solid rgba(255, 124, 49, 0.2);
                            margin-bottom: 20px;
                        }
                        .cart-popup-item-image-wrapper {
                            position: relative;
                            flex-shrink: 0;
                        }
                        .cart-popup-image-badge {
                            position: absolute;
                            top: -8px;
                            right: -8px;
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            background: linear-gradient(135deg, #ff7c31 0%, #e65d1f 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: #fff;
                            font-size: 14px;
                            box-shadow: 0 4px 12px rgba(255, 124, 49, 0.4);
                            z-index: 1;
                        }
                        .cart-popup-item-image {
                            width: 100px;
                            height: 100px;
                            border-radius: 12px;
                            object-fit: cover;
                            border: 3px solid #fff;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                        }
                        .cart-popup-item-content {
                            flex: 1;
                        }
                        .cart-popup-item-title {
                            font-size: 18px;
                            font-weight: 700;
                            color: #1a1a1a;
                            margin: 0 0 10px 0;
                            line-height: 1.4;
                        }
                        .cart-popup-item-author {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            color: #666;
                            font-size: 14px;
                            margin: 0 0 12px 0;
                        }
                        .cart-popup-item-author i {
                            color: #ff7c31;
                            font-size: 16px;
                        }
                        .cart-popup-item-price {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            color: #ff7c31;
                            font-size: 20px;
                            font-weight: 700;
                        }
                        .cart-popup-item-price i {
                            font-size: 18px;
                        }
                        .cart-popup-divider {
                            height: 1px;
                            background: linear-gradient(90deg, transparent 0%, rgba(255, 124, 49, 0.2) 50%, transparent 100%);
                            margin: 25px 0;
                        }
                        .cart-popup-actions-modern {
                            display: flex;
                            gap: 12px;
                            flex-wrap: wrap;
                        }
                        .cart-popup-btn-secondary,
                        .cart-popup-btn-primary {
                            flex: 1;
                            min-width: 150px;
                            padding: 14px 24px;
                            border-radius: 12px;
                            font-weight: 600;
                            font-size: 15px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 8px;
                            transition: all 0.3s ease;
                            text-decoration: none;
                            border: none;
                            cursor: pointer;
                        }
                        .cart-popup-btn-secondary {
                            background: #fff;
                            border: 2px solid rgba(255, 124, 49, 0.3);
                            color: #ff7c31;
                        }
                        .cart-popup-btn-secondary:hover {
                            background: rgba(255, 124, 49, 0.1);
                            border-color: #ff7c31;
                            transform: translateY(-2px);
                            box-shadow: 0 4px 12px rgba(255, 124, 49, 0.2);
                            color: #ff7c31;
                        }
                        .cart-popup-btn-primary {
                            background: linear-gradient(135deg, #ff7c31 0%, #e65d1f 100%);
                            color: #fff;
                            box-shadow: 0 4px 12px rgba(255, 124, 49, 0.3);
                        }
                        .cart-popup-btn-primary:hover {
                            background: linear-gradient(135deg, #ff8f4a 0%, #ff7c31 100%);
                            transform: translateY(-2px);
                            box-shadow: 0 6px 20px rgba(255, 124, 49, 0.4);
                            color: #fff;
                        }
                        .cart-popup-btn-secondary i,
                        .cart-popup-btn-primary i {
                            font-size: 16px;
                        }
                        /* Tablet Responsive */
                        @media (max-width: 768px) {
                            .cart-popup-dialog {
                                max-width: 90%;
                                margin: 20px auto;
                            }
                            .cart-popup-modal {
                                width: 100%;
                            }
                            .cart-popup-header {
                                padding: 35px 25px 25px;
                            }
                            .success-checkmark {
                                width: 70px;
                                height: 70px;
                            }
                            .success-checkmark i {
                                font-size: 35px;
                            }
                            .cart-popup-title {
                                font-size: 22px;
                            }
                            .cart-popup-body {
                                padding: 25px;
                            }
                        }
                        /* Mobile Responsive */
                        @media (max-width: 576px) {
                            .cart-popup-dialog {
                                margin: 0;
                                max-width: 100%;
                                height: 100%;
                                display: flex;
                                align-items: flex-end;
                                padding: 0;
                            }
                            .cart-popup-modal {
                                margin: 0;
                                border-radius: 20px 20px 0 0;
                                width: 100%;
                                max-width: 100%;
                                height: auto;
                                max-height: 90vh;
                                overflow-y: auto;
                                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
                            }
                            .cart-popup-close {
                                top: 12px;
                                right: 12px;
                                width: 32px;
                                height: 32px;
                                font-size: 16px;
                            }
                            .cart-popup-header {
                                padding: 25px 20px 20px;
                            }
                            .cart-popup-success-icon {
                                margin-bottom: 15px;
                            }
                            .success-checkmark {
                                width: 60px;
                                height: 60px;
                            }
                            .success-checkmark i {
                                font-size: 28px;
                            }
                            .cart-popup-title {
                                font-size: 20px;
                                line-height: 1.3;
                            }
                            .cart-popup-body {
                                padding: 20px 16px;
                            }
                            .cart-popup-item-modern {
                                flex-direction: column;
                                align-items: center;
                                text-align: center;
                                gap: 15px;
                                padding: 16px;
                                margin-bottom: 16px;
                            }
                            .cart-popup-item-image-wrapper {
                                margin: 0 auto;
                            }
                            .cart-popup-image-badge {
                                top: -6px;
                                right: -6px;
                                width: 28px;
                                height: 28px;
                                font-size: 12px;
                            }
                            .cart-popup-item-image {
                                width: 90px;
                                height: 90px;
                                margin: 0 auto;
                            }
                            .cart-popup-item-content {
                                width: 100%;
                                text-align: center;
                            }
                            .cart-popup-item-title {
                                font-size: 16px;
                                margin-bottom: 8px;
                                line-height: 1.3;
                            }
                            .cart-popup-item-author {
                                justify-content: center;
                                font-size: 13px;
                                margin-bottom: 10px;
                            }
                            .cart-popup-item-price {
                                justify-content: center;
                                font-size: 18px;
                            }
                            .cart-popup-divider {
                                margin: 20px 0;
                            }
                            .cart-popup-actions-modern {
                                flex-direction: column;
                                gap: 10px;
                                width: 100%;
                            }
                            .cart-popup-btn-secondary,
                            .cart-popup-btn-primary {
                                width: 100%;
                                min-width: auto;
                                padding: 14px 20px;
                                font-size: 14px;
                            }
                        }
                        /* Small Mobile */
                        @media (max-width: 375px) {
                            .cart-popup-header {
                                padding: 20px 16px 18px;
                            }
                            .success-checkmark {
                                width: 55px;
                                height: 55px;
                            }
                            .success-checkmark i {
                                font-size: 24px;
                            }
                            .cart-popup-title {
                                font-size: 18px;
                            }
                            .cart-popup-body {
                                padding: 16px 12px;
                            }
                            .cart-popup-item-modern {
                                padding: 14px;
                            }
                            .cart-popup-item-image {
                                width: 80px;
                                height: 80px;
                            }
                            .cart-popup-item-title {
                                font-size: 15px;
                            }
                            .cart-popup-item-author,
                            .cart-popup-item-price {
                                font-size: 13px;
                            }
                            .cart-popup-btn-secondary,
                            .cart-popup-btn-primary {
                                padding: 12px 18px;
                                font-size: 13px;
                            }
                        }
                        </style>
                    `;
                    
            // Remove existing modal if any
            $('#addToCartModal').remove();
            
            // Add modal to body
            $('body').append(popupHtml);
            
            // Wait for modal to be added to DOM, then show
            setTimeout(function() {
                const modal = $('#addToCartModal');
                if (modal.length) {
                    // Use Bootstrap 5 modal if available
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = new bootstrap.Modal(modal[0]);
                        bsModal.show();
                    } else if (typeof $.fn.modal !== 'undefined') {
                        // Fallback to Bootstrap 4/jQuery modal
                        modal.modal('show');
                    } else {
                        // Fallback: show modal manually
                        modal.css('display', 'block');
                        modal.addClass('show');
                        $('body').addClass('modal-open');
                        $('body').append('<div class="modal-backdrop fade show"></div>');
                    }
                    
                    console.log('Modal should be visible now');
                } else {
                    console.error('Modal element not found');
                }
            }, 50);
            
            // Auto-hide after 8 seconds
            setTimeout(function() {
                const modal = $('#addToCartModal');
                if (modal.length) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal[0]);
                        if (bsModal) bsModal.hide();
                    } else if (typeof $.fn.modal !== 'undefined') {
                        modal.modal('hide');
                    } else {
                        modal.remove();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }
                }
            }, 8000);
        }
    </script>
@endpush
