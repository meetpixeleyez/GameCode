<div class="table-responsive">
    <table class="table table--responsive--lg">
        <thead>
            <tr>
                <th>@lang('Product | Date')</th>
                <th>@lang('Purchase Code')</th>
                <th>@lang('License')</th>
                <th class="text-start text-md-center">@lang('Amount')</th>
                <th class="text-start text-md-center">@lang('Refunded')</th>
                <th>@lang('Earning')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>
                        <div class="table-product flex-align">
                            <div class="table-product__thumb">
                                <x-product-thumbnail :product="@$sale->product" />
                            </div>

                            <div class="table-product__content">
                                @if (@$sale->product)
                                    <a href="{{ route('product.details', @$sale->product->slug) }}" class="table-product__name">
                                        {{ __(strLimit(@$sale->product->title, 15)) }}
                                    </a>
                                @endif
                                {{ showDateTime($sale->created_at) }}
                            </div>
                        </div>
                    </td>
                    <td> {{ $sale->purchase_code }} </td>
                    <td>@php echo $sale->licenseBadge; @endphp</td>
                    <td>
                        <div class="text-end text-md-center">
                            <span>{{ showAmount($sale->product_price) }}+</span>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('Extended Amount')" class="text--success">{{ showAmount($sale->extended_amount) }}</span>
                            <br>
                            {{ showAmount($sale->product_price + $sale->extended_amount) }}

                            @if ($sale->discount > 0)
                                <span class="text--danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('Discount Amount')"> - {{ showAmount($sale->discount) }}</span>
                            @endif

                        </div>
                    </td>
                    <td class="text-end text-md-center">@php echo $sale->refundedBadge; @endphp</td>
                    <td> {{ showAmount($sale->seller_earning) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
