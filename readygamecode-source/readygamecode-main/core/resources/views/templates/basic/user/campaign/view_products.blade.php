@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-3 dashboard-row-wrapper">
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($campaignProducts->count() == 0)
                        <x-empty-list title="No product found" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Product | Date')</th>
                                        <th>@lang('Discount Percentage')</th>
                                        <th class="text-start text-md-center">@lang('Price')</th>
                                        <th class="text-start text-md-center">@lang('Discounted Price')</th>
                                        <th>@lang('Status')</th>
                                        <th class="text-start text-md-center">@lang('Submitted At')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($campaignProducts as $campaignProduct)
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="@$campaignProduct->product" />
                                                    </div>

                                                    <div class="table-product__content">
                                                        @if (@$campaignProduct->product)
                                                            <a href="{{ route('product.details', @$campaignProduct->product->slug) }}"
                                                                class="table-product__name">
                                                                {{ __(strLimit(@$campaignProduct->product->title, 15)) }}
                                                            </a>
                                                        @endif
                                                        {{ showDateTime($campaignProduct->created_at) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td> {{ getAmount($campaignProduct->discount_percentage) }}% </td>
                                            <td>
                                                <div class="text-center">
                                                    @lang('Personal - '){{ showAmount(@$campaignProduct->product->price + @$campaignProduct->personalBuyerFee()) }} <br>
                                                    @lang('Commercial - '){{ showAmount(@$campaignProduct->product->price_cl + @$campaignProduct->commercialBuyerFee()) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    {{ showAmount(@$campaignProduct->product->price + @$campaignProduct->personalBuyerFee() - (@($campaignProduct->product->price + @$campaignProduct->personalBuyerFee()) * $campaignProduct->discount_percentage) / 100) }} <br>
                                                    {{ showAmount(@$campaignProduct->product->price_cl + @$campaignProduct->commercialBuyerFee() - (@($campaignProduct->product->price_cl + @$campaignProduct->commercialBuyerFee()) * $campaignProduct->discount_percentage) / 100) }}
                                                </div>
                                            </td>
                                            <td>@php echo $campaignProduct->statusBadge; @endphp</td>
                                            <td> {{ showDateTime($campaignProduct->created_at) }} </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="pt-30">
                {{ paginateLinks($campaignProducts) }}
            </div>
        </div>
    </div>
@endsection
