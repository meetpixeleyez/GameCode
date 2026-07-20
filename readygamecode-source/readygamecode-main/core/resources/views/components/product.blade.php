<div class="product-card h-100 @if ($product->audio_temp_file) audio-card @endif ">
    <div class="product-card__thumb">
        @if ($product->audio_temp_file && in_array('mp3', $product->category->preview_file_types))
            <div class="audio-player-wrapper">
                <div class="d-flex align-items-center gap-1 audio-player-left">
                    <button id="play-button-{{ $product->id }}" class="play-button">
                        <i class="fas fa-play"></i>
                    </button>
                    <span id="current-time-{{ $product->id }}">00:00</span>
                </div>

                <div class="audio-player-middle" data-file-path="{{ asset(getFilePath('previewFile')) . '/' . productFilePath($product, 'temp_audio_file') . '/' . $product->audio_temp_file }}" id="waveform-{{ $product->id }}"></div>

                <div class="audio-player-time">
                    <span id="total-time-{{ $product->id }}">00:00</span>
                </div>
            </div>
        @else
             <a href="{{ route('product.details', $product->slug) }}" class="link" title="{{ __($product->title) }}">
             <img src="{{ getImage(getFilePath('productInlinePreview') . productFilePath($product, 'inline_preview_image'), getFileSize('productInlinePreview')) }}"
                 alt="{{ __($product->title) }}" class="product-image" fetchpriority="high">
            </a>
        @endif


        @if ($product->isTrending())
            <span class="icon">
                @php
                    $trendingIconPath = base_path('../assets/images/trending.svg');
                @endphp
                {!! file_exists($trendingIconPath) ? file_get_contents($trendingIconPath) : '' !!}
            </span>
        @endif
        <div class="collection-list">
            <x-product-save :product="$product" />
        </div>
    </div>
    <div class="product-card__content h-100">
        <div class="product-card__content-inner">
            <div class="product-card__top d-flex w-100 justify-content-between ">
                <div class="product-card-title-wrapper">
                    <h6 class="product-card__title">
                        <a href="{{ route('product.details', $product->slug) }}" class="link border-effect">
                            {{ __($product->title) }}
                        </a>
                    </h6>
                    <span class="product-card__author">@lang('by')
                        <a href="{{ route('user.profile', $product->author->username) }}"
                           class="link">{{ __($product->author->fullname) }}</a>
                    </span>
                </div>
                @if ($product->is_free)
                    <span class="product-card__price">@lang('Free')</span>
                @else
                    @php
                        $hasDiscount = $product->campaign_product_price;
                    @endphp

                    <span class="product-card__price">
                        @if ($hasDiscount[0])
                            <del>{{ showAmount($product->productPrice('personal')) }}</del>
                            <span class="text-success">{{ showAmount($hasDiscount[1]) }}</span>
                        @else
                            {{ showAmount($product->productPrice('personal')) }}
                        @endif
                    </span>
                @endif

            </div>
            <div class="collection-list list-style">
                <x-product-save :product="$product" />

            </div>
        </div>
        <div class="flex-between align-items-center">
            <div class="product-card__rating">
                <div class="rating-list">
                    @php
                        $maxStars = 5;
                        $rating = round($product->avg_rating ?? 0);
                        $total_review = $product->total_review ?? 0;
                    @endphp
        
                    @for ($i = 1; $i <= $maxStars; $i++)
                        @if ($total_review > 0 && $i <= $rating)
                            <i class="rating-list__item la la-star"></i> {{-- filled star --}}
                        @else
                            <i class="emptyrating la la-star"></i> {{-- empty star --}}
                        @endif
                    @endfor
                </div>
                @if ($product->is_free)
                    <span class="product-card__sales">{{ $product->download_count ?: 0 }}
                        {{ __(str()->plural('Download', $product->download_count ?: 0)) }}</span>
                @else
                    <span class="product-card__sales">{{ $product->total_sold }}
                        {{ __(str()->plural('Sale', $product->total_sold)) }}</span>
                @endif
            </div>

            <div class="product-card__actions d-flex align-items-center gap-2">
                @if (!$product->is_free)
                    @php
                        $productTitle = addslashes($product->title);
                        $productPrice = showAmount($product->productPrice('personal'));
                        $productImage = getImage(getFilePath('productThumbnail') . productFilePath($product, 'thumbnail'));
                        $sellerName = addslashes($product->author->fullname ?? 'Unknown');
                    @endphp
                    <button type="button" 
                            class="product-card__add-cart-btn" 
                            data-product-id="{{ $product->id }}"
                            data-product-title="{{ $productTitle }}"
                            data-product-price="{{ $productPrice }}"
                            data-product-image="{{ $productImage }}"
                            data-seller-name="{{ $sellerName }}"
                            title="@lang('Add to cart')">
                        <i class="las la-shopping-cart"></i>
                    </button>
                @endif
                @if ($product->demo_url)
                    <a href="{{ @$product->demo_url }}" target="_blank"
                       class="product-card__live-preview-btn btn btn-outline--light btn--sm mt-1">@lang('Live Preview')</a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    "use strict";
    
    $(document).ready(function() {
        // Handle add to cart button click on product cards
        $(document).on('click', '.product-card__add-cart-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = $(this);
            
            // Prevent multiple clicks during request
            if (btn.prop('disabled') || btn.data('processing')) {
                return false;
            }
            
            const productId = btn.data('product-id');
            const productTitle = btn.data('product-title') || '';
            const productPrice = btn.data('product-price') || '{{ gs("cur_sym") }}0.00';
            const productImage = btn.data('product-image') || '';
            const sellerName = btn.data('seller-name') || 'Unknown';
            
            // Mark as processing and disable button during request
            btn.data('processing', true);
            btn.prop('disabled', true);
            
            // Add loading state
            const originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                type: 'POST',
                url: '{{ route("cart.store") }}',
                data: {
                    product_id: productId,
                    license: 1, // Default to personal license
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                async: true,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    const result = response || {};
                    const status = result.status || 'error';
                    const message = result.message || 'Operation completed';
                    const cartQty = result.cartQty || 0;
                    
                    if (status === 'success') {
                        // Update cart quantity in header
                        if ($('.cart-button__qty').length) {
                            $('.cart-button__qty').text(cartQty);
                        }
                        
                        if (typeof incCartQty === 'function') {
                            incCartQty();
                        }
                        
                        // Show popup
                        setTimeout(function() {
                            showAddToCartPopup(productId, productTitle, productPrice, productImage, sellerName);
                        }, 100);
                    } else {
                        // Only show error if it's not "already in cart"
                        if (!message.toLowerCase().includes('already added') && 
                            !message.toLowerCase().includes('already in cart')) {
                            if (typeof notify === 'function') {
                                notify('error', message);
                            }
                        }
                    }
                    
                    btn.data('processing', false);
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                },
                error: function(xhr, status, error) {
                    let errorMessage = '@lang("An error occurred. Please try again.")';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    // Only show error if it's not "already in cart"
                    if (!errorMessage.toLowerCase().includes('already added') && 
                        !errorMessage.toLowerCase().includes('already in cart')) {
                        if (typeof notify === 'function') {
                            notify('error', errorMessage);
                        }
                    }
                    
                    btn.data('processing', false);
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                }
            });
            
            return false;
        });
        
        // Function to show add to cart popup - Same as product details page
        function showAddToCartPopup(productId, productTitle, productPrice, productImage, sellerName) {
            // Escape HTML to prevent XSS
            const escapeHtml = (text) => {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            };
            
            const escapedTitle = escapeHtml(productTitle);
            const escapedPrice = escapeHtml(productPrice);
            const escapedImage = escapeHtml(productImage);
            const escapedSeller = escapeHtml(sellerName);
            
            const popupHtml = `
                <div class="modal fade" id="addToCartModal-${productId}" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
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
                                        <img src="${escapedImage}" alt="${escapedTitle}" class="cart-popup-item-image" onerror="this.src='{{ asset('assets/images/default.png') }}'">
                                    </div>
                                    <div class="cart-popup-item-content">
                                        <h5 class="cart-popup-item-title">${escapedTitle}</h5>
                                        <p class="cart-popup-item-author">
                                            <i class="las la-user"></i>
                                            <span>@lang('by') ${escapedSeller}</span>
                                        </p>
                                        <div class="cart-popup-item-price">
                                            <i class="las la-tag"></i>
                                            <span>${escapedPrice}</span>
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
            `;
            
            // Remove existing modal if any
            $(`#addToCartModal-${productId}`).remove();
            
            // Add modal to body
            $('body').append(popupHtml);
            
            // Show modal
            setTimeout(function() {
                const modal = $(`#addToCartModal-${productId}`);
                if (modal.length) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = new bootstrap.Modal(modal[0]);
                        bsModal.show();
                    } else if (typeof $.fn.modal !== 'undefined') {
                        modal.modal('show');
                    }
                }
            }, 50);
            
            // Auto-hide after 8 seconds
            setTimeout(function() {
                const modal = $(`#addToCartModal-${productId}`);
                if (modal.length) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal[0]);
                        if (bsModal) bsModal.hide();
                    } else if (typeof $.fn.modal !== 'undefined') {
                        modal.modal('hide');
                    }
                }
            }, 8000);
        }
    });
</script>
@endpush

@push('style')
<style>
/* Enhanced Product Card Styles */
.product-card {
    border-radius: 16px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.product-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-4px);
    border-color: rgba(255, 124, 49, 0.2);
}

.product-card__thumb {
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
    width: 100%;
    aspect-ratio: 16/9;
    max-height: none !important;
}

.product-card__thumb .link {
    width: 100%;
    height: 100%;
    display: block;
    position: relative;
}

.product-card__thumb img {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.product-card:hover .product-card__thumb img {
    transform: scale(1.05);
}

.product-card__content {
    padding: 20px 18px 18px !important;
    background: #fff;
    border-top: none !important;
    border-radius: 0 0 16px 16px !important;
}

.product-card__title {
    font-size: 15px !important;
    font-weight: 600 !important;
    line-height: 1.4;
    margin-bottom: 6px !important;
    color: #1a1a1a;
}

.product-card__title .link {
    color: #1a1a1a;
    transition: color 0.2s ease;
}

.product-card__title .link:hover {
    color: #ff7c31 !important;
}

.product-card__author {
    font-size: 12px !important;
    color: #666;
    margin-top: 4px;
}

.product-card__author .link {
    color: #666;
    transition: color 0.2s ease;
}

.product-card__author .link:hover {
    color: #ff7c31;
}

.product-card__price {
    background: linear-gradient(135deg, #ff7c31 0%, #ff9a5c 100%) !important;
    color: #fff !important;
    padding: 6px 12px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    white-space: nowrap;
    box-shadow: 0 2px 6px rgba(255, 124, 49, 0.3);
    min-width: auto !important;
}

.product-card__price del {
    color: rgba(255, 255, 255, 0.7);
    font-size: 11px;
    margin-right: 4px;
}

.product-card__price .text-success {
    color: #fff !important;
    font-weight: 600;
}

.product-card__rating {
    margin-top: 12px !important;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.rating-list__item {
    color: #ffc107 !important;
    font-size: 14px;
}

.emptyrating {
    color: #ddd !important;
    font-size: 14px;
}

.product-card__sales {
    font-size: 12px !important;
    color: #666;
    font-weight: 500;
}

/* Add to Cart Button - Icon Only */
.product-card__actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
}

.product-card__add-cart-btn {
    width: 42px;
    height: 42px;
    min-width: 42px;
    min-height: 42px;
    border-radius: 10px;
    border: 2px solid #e8e8e8;
    background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0;
    margin: 0;
    font-size: 18px;
    flex-shrink: 0;
    box-sizing: border-box;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.product-card__add-cart-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #fff 0%, #fff5f0 100%);
    border-color: #ff7c31;
    color: #ff7c31;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 124, 49, 0.25);
}

.product-card__add-cart-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

.product-card__add-cart-btn i {
    font-size: 18px;
}

/* Live Preview Button - Attractive & Highlighted */
.product-card__live-preview-btn {
    border-radius: 8px !important;
    padding: 10px 18px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    border: 1.5px solid #ff7c31 !important;
    color: #ffffff !important;
    background: #ff7c31 !important;
    transition: all 0.25s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    position: relative;
    z-index: 1;
}

.product-card__live-preview-btn:hover {
    background: #ffffff !important;
    color: #ff7c31 !important;
    border-color: #ff7c31 !important;
    box-shadow: 0 4px 12px rgba(255, 124, 49, 0.3);
    transform: translateY(-1px);
}

.product-card__live-preview-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(255, 124, 49, 0.25);
}

/* Max-width 1200px optimization */
@media (max-width: 1200px) {
    .product-card__live-preview-btn {
        padding: 9px 16px !important;
        font-size: 12px !important;
    }
}

/* Cart Popup Styles - Same as product details page */
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
    from { transform: scale(0); }
    to { transform: scale(1); }
}
@keyframes checkmark {
    0% { transform: scale(0) rotate(-45deg); opacity: 0; }
    50% { transform: scale(1.2) rotate(10deg); }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
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
@media (max-width: 768px) {
    .cart-popup-dialog {
        max-width: 90%;
        margin: 20px auto;
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
@media (max-width: 576px) {
    .product-card {
        border-radius: 12px !important;
    }
    
    .product-card__thumb {
        aspect-ratio: 16/9;
        max-height: none !important;
    }
    
    .product-card__content {
        padding: 14px 12px 12px !important;
    }
    
    .product-card__title {
        font-size: 13px !important;
        margin-bottom: 5px !important;
    }
    
    .product-card__author {
        font-size: 11px !important;
        margin-top: 3px;
    }
    
    .product-card__price {
        padding: 5px 10px !important;
        font-size: 12px !important;
    }
    
    .product-card__rating {
        margin-top: 10px !important;
        gap: 6px;
    }
    
    .rating-list__item,
    .emptyrating {
        font-size: 12px !important;
    }
    
    .product-card__sales {
        font-size: 11px !important;
    }
    
    .product-card__actions {
        margin-top: 10px !important;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .product-card__add-cart-btn {
        width: 36px;
        height: 36px;
        min-width: 36px;
        min-height: 36px;
        font-size: 15px;
        border-radius: 8px;
    }
    
    .product-card__add-cart-btn i {
        font-size: 15px;
    }
    
    .product-card__live-preview-btn {
        padding: 8px 14px !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        border-radius: 7px !important;
        flex: 1;
        min-width: 110px;
        border-width: 1.5px !important;
    }
    
    /* Footer layout optimization for mobile */
    /* .flex-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
     */
    .product-card__rating {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .rating-list {
        display: flex;
        gap: 3px;
    }
    
    .product-card__actions {
        width: 100%;
        justify-content: space-between;
        flex-wrap: nowrap;
    }
    
    .product-card__live-preview-btn {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
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
    .cart-popup-item-modern {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 15px;
        padding: 16px;
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
    }
}
</style>
@endpush
