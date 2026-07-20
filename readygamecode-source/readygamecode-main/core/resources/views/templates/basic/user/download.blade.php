@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="mb-0">{{ __($pageTitle) }}</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm"
                    placeholder="Search..." />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($purchasedItems->count() == 0)
                        <x-empty-list title="No purchase data found" />
                    @else
                        <table class="table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('Product | Date')</th>
                                    <th>@lang('Purchase Code')</th>
                                    <th>@lang('License')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchasedItems as $purchasedItem)
                                    <tr>
                                        <td>
                                            <div class="table-product flex-align">
                                                <div class="table-product__thumb">
                                                    <x-product-thumbnail :product="@$purchasedItem->product" />
                                                </div>
                                                @if (@$purchasedItem->product)
                                                    <div class="table-product__content">
                                                        <a href="{{ route('product.details', @$purchasedItem->product->slug) }}"
                                                            class="table-product__name">
                                                            {{ __(strLimit(@$purchasedItem->product->title, 20)) }}
                                                        </a>
                                                        {{ showDateTime($purchasedItem->created_at) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td> {{ $purchasedItem->purchase_code }} </td>
                                        <td>@php echo $purchasedItem->licenseBadge; @endphp</td>
                                        <td>
                                            <div class="dropdown-action">
                                                <span class="dropdown-action-btn"><i class="las la-angle-down"></i><span
                                                        class="mx-1">@lang('More')</span></span>
                                                <ul class="action-list">
                                                    <li class="action-list__item">
                                                        <a href="{{ route('user.author.product.download', $purchasedItem->purchase_code) }}?time={{ time() }}"
                                                            class="btn btn-outline--primary btn--sm w-100">
                                                            <i class="la la-download"></i> @lang('Download')
                                                        </a>
                                                    </li>
                                                    <li class="action-list__item">
                                                        <button class="btn btn-outline--success btn--sm w-100 review_button"
                                                            data-purchase-code="{{ $purchasedItem->purchase_code }}"
                                                            data-product_id="{{ $purchasedItem->product_id }}"
                                                            data-review="{{ optional($purchasedItem->product->reviews->first())->review }}"
                                                            data-rating="{{ optional($purchasedItem->product->reviews->first())->rating }}"
                                                            data-category_id="{{ optional($purchasedItem->product->reviews->first())->review_category_id }}">
                                                            <i class="la la-star"></i> @lang('Review')
                                                        </button>
                                                    </li>
                                                    <li class="action-list__item">
                                                        <button class="btn btn-outline--warning btn--sm w-100 refund-btn"
                                                            data-purchase_code="{{ $purchasedItem->purchase_code }}">
                                                            <i class="la la-rotate-left"></i> @lang('Refund')
                                                        </button>
                                                    </li>
                                                    <li class="action-list__item">
                                                        <a href="{{ route('user.purchase.details', ['username' => @$purchasedItem->buyer->username, 'product_id' => @$purchasedItem->product_id, 'order_item_id' => @$purchasedItem->id]) }}"
                                                            class="btn btn-outline--info btn--sm w-100">
                                                            <i class="las la-info-circle"></i> @lang('Details')
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($purchasedItems->hasPages())
                            <div class="card-footer">
                                <div class="pt-30">
                                    {{ paginateLinks($purchasedItems) }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @lang('Review this Item')
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <label class="form-label me-2" for="rating">@lang('Your Rating')</label>
                                    <div id="star"></div>
                                    <input type="hidden" name="rating" required>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <input type="hidden" name="purchase_code">
                                    <label class="form-label">@lang('Rating Category')</label>
                                    <select name="review_category" class="form--control" required>
                                        <option value="">@lang('Select a category')</option>
                                        @foreach ($reviewCategories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="review" class="form-label">@lang('Review')</label>
                                    <textarea name="review" id="review" class="form--control" placeholder="@lang('Your Review')" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REFUND MODAL --}}
    <div id="refundRequestModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <div class="modal-title">
                        <h5 class="m-0">@lang('Refund Item')</h5>
                        <p class="product-title m-0"></p>
                    </div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" class="refund-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reason" class="form--label">@lang('Reason')</label>
                            <textarea name="reason" id="reason" class="form--control" placeholder="@lang('Please describe the reason for your refund..')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/jquery.raty.css') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.raty.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#order_by').on('change', function() {
                const orderBy = $(this).val();
                const url = location.toString().replace(location.search, "");
                window.location.href = `${url}?order_by=${orderBy}`;
            })

            let initRaty = function(score) {
                $('#star').raty({
                    starHalf: "{{ asset('assets/images/raty/star-half.png') }}",
                    starOff: "{{ asset('assets/images/raty/star-off.png') }}",
                    starOn: "{{ asset('assets/images/raty/star-on.png') }}",
                    score: score || 0,
                    click: function(score, e) {
                        $('[name="rating"]').val(score);
                    }
                });
            };

            let modal = $('#reviewModal');

            $('.review_button').on('click', function() {
                let data = $(this).data();
                let purchaseCode = data.purchaseCode;
                let route = `{{ route('user.author.review.store', ':id') }}`;
                route = route.replace(':id', data.product_id);
                modal.find('form').attr('action', route);
                modal.find('form').find('[name="purchase_code"]').val(purchaseCode);

                if (data.review) {
                    $('[name="review"]').val(data.review);
                    $('[name="rating"]').val(data.rating);
                    $('[name="review_category"]').val(data.category_id);
                } else {
                    $('[name="review"]').val('');
                    $('[name="rating"]').val('');
                    $('[name="review_category"]').val('');
                }

                initRaty(data.rating || 0);

                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
                $('[name="rating"]').val('');
                $('#star').raty('destroy');
                initRaty(0);
                $('#star').html('')
            });

            $('.refund-btn').on('click', function(e) {
                const modal = $('#refundRequestModal');
                const purchaseCode = $(this).data('purchase_code');
                modal.modal('show');
                let url = "{{ route('user.author.refund.request', ':purchase_code') }}";
                url = url.replace(':purchase_code', purchaseCode);
                modal.find('.refund-form').attr('action', url);
            });
        })(jQuery);
    </script>
    <script>
        // ========================= Dropdown Actions Js Start =====================
        $('.dropdown-action-btn').on('click', function(e) {
            e.stopPropagation();
            $('.action-list').not($(this).siblings('.action-list')).removeClass('show');
            $(this).siblings('.action-list').toggleClass('show');
        });

        $('.action-list').on('click', '.action-list__item', function(e) {
            $('.action-list').removeClass('show');
        });
        $(document).on('click', function(event) {
            var target = $(event.target);
            if (!target.closest('.dropdown-action-btn').length && !target.closest('.action-list').length) {
                $('.action-list').removeClass('show');
            }
        });
        // ========================= Dropdown Actions Js End =====================
    </script>
@endpush

@push('style')
    <style>
        .dropdown-action {
            position: relative;
            display: inline-block;
        }

        .dropdown-action .dropdown-action-btn {
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            font-size: 0.813rem;
            line-height: 1;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            -webkit-transition: 0.2s linear;
            transition: 0.2s linear;
            background: hsl(var(--base) / 0.1);
            border: 1px solid hsl(var(--base) / 0.15);
            color: hsl(var(--base));
        }

        .dropdown-action .dropdown-action-btn:hover {
            border-color: hsl(var(--base));
        }

        .dropdown-action .action-list {
            width: 124px;
            padding: 10px 8px;
            background-color: hsl(var(--white));
            -webkit-box-shadow: var(--box-shadow);
            box-shadow: var(--box-shadow);
            border: 1px solid hsl(var(--border-color-light)/0.45);
            border-radius: 5px;
            overflow: hidden;
            position: absolute;
            right: calc(100% + 3px);
            top: 50%;
            z-index: 9999;
            -webkit-transition: 0.15s linear;
            transition: 0.15s linear;
            -webkit-transform: scale(0.95) translateY(-50%);
            transform: scale(0.95) translateY(-50%);
            visibility: hidden;
            opacity: 0;
        }

        .dropdown-action .action-list.show {
            visibility: visible;
            opacity: 1;
            -webkit-transform: scale(1) translateY(-50%);
            transform: scale(1) translateY(-50%);
        }

        table tbody tr:first-child td .dropdown-action .action-list {
            -webkit-transform: scale(1);
            transform: scale(1);
            top: 0;
        }

        table tbody tr:last-child td .dropdown-action .action-list {
            -webkit-transform: scale(1);
            transform: scale(1);
            bottom: 0;
            top: unset;
        }

        @media (max-width: 991px) {

            table tbody tr td .dropdown-action .action-list,
            table tbody tr:first-child td .dropdown-action .action-list {
                -webkit-transform: scale(1);
                transform: scale(1);
                bottom: 0;
                top: unset;
            }

            table tbody tr td .dropdown-action .action-list.show {
                -webkit-transform: scale(1);
                transform: scale(1);
            }
        }

        .dropdown-action .action-list__item {
            width: 100%;
            margin-bottom: 5px;
            color: hsl(var(--body-color));
            text-align: left;
            cursor: pointer;
            -webkit-transition: 0.15s linear;
            transition: 0.15s linear;
        }

        .dropdown-action .action-list__item:last-child {
            margin-bottom: 0;
        }

        .dropdown-action .action-list__item .btn {
            padding: 7px 10px !important;
        }

        .table--responsive--lg {
            overflow: visible !important;
        }
    </style>
@endpush
