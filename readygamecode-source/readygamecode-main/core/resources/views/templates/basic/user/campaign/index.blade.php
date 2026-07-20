@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3 dashboard-row-wrapper">
        <div class="col-12">
            <div class="card custom--card campaign--card">
                <div class="card-body">
                    <div class="campaign-content">
                        <h3 class="campaign__title">{{ $campaign->name }}</h3>
                        <ul class="campaign-date">
                            <li class="campaign-date__item">
                                <div>
                                    <span class="label">@lang('Start'):</span>
                                    <span class="value">{{ showDateTime($campaign->start_date, 'F j, Y') }}</span>
                                </div>
                            </li>
                            <li class="campaign-date__item">
                                <div>
                                    <span class="label">@lang('End'):</span>
                                    <span class="value">{{ showDateTime($campaign->end_date, 'F j, Y') }}</span>
                                </div>
                            </li>
                            <li class="campaign-date__item">
                                <div>
                                    <span class="label">@lang('Discount'):</span>
                                    <span class="value">{{ getAmount($campaign->discount_min) }}% -
                                        {{ getAmount($campaign->discount_max) }}%</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex align-items justify-content-between flex-wrap gap-2">
                <h6>{{ __($pageTitle) }}</h6>
                <a href="{{ route('user.author.campaign.view.products') }}" class="btn btn-outline--primary btn-sm">
                    <i class="las la-server"></i> @lang('See Submitted Products')
                </a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($products->count() == 0)
                        <x-empty-list title="No product found" />
                    @else
                        <form action="{{ route('user.author.campaign.submit.products') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                                <input type="hidden" name="selected_products" value="">
                                <table class="table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Product | Date')</th>
                                            <th>@lang('Discount percentage')</th>
                                            <th>@lang('Select')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            @php
                                                $submittedProduct = $submittedProducts[$product->id] ?? null;
                                                $discount = $submittedProduct->discount_percentage ?? old('discount_percentage.' . $product->id, '');
                                                $isChecked = $submittedProduct ? 'checked' : (in_array($product->id, old('selected_products', [])) ? 'checked' : '');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="table-product flex-align">
                                                        <div class="table-product__thumb">
                                                            <x-product-thumbnail :product="@$product" />
                                                        </div>

                                                        <div class="table-product__content">
                                                            <a href="{{ route('product.details', @$product->slug) }}"
                                                               class="table-product__name">
                                                                {{ __(strLimit(@$product->title, 15)) }}
                                                            </a>
                                                            {{ showDateTime($product->created_at) }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input class="form-control form--control form--control--sm"
                                                               type="number" name="discount_percentage[{{ $product->id }}]"
                                                               min="{{ $campaign->discount_min }}"
                                                               max="{{ $campaign->discount_max }}" step="1"
                                                               value="{{ $discount }}">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form--check justify-content-end">
                                                        <input class="form-check-input product-checkbox" type="checkbox"
                                                               name="selected_products[]" value="{{ $product->id }}"
                                                               {{ $isChecked }}>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn--base btn--md w-100 submitButton"
                                    type="submit">@lang('Submit for Campaign')</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="pt-30">
                {{ paginateLinks($products) }}
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            const toggleSubmitButton = () => {
                $('.submitButton').prop('disabled', $('.product-checkbox:checked').length === 0);
            };
            $('.product-checkbox').on('change', toggleSubmitButton);
            toggleSubmitButton();
        })(jQuery);
    </script>
@endpush
