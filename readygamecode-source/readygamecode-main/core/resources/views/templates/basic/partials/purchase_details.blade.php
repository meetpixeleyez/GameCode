<div class="card custom--card">
    <div class="order-items-list">
        <div class="order-item p-3">
            <h6>@lang('License Details')</h6>
            <ul class="license-details-list">
                <li>
                    <span>@lang('Purchase Code'):</span>
                    <span>{{ $orderItem->purchase_code }}</span>
                </li>
                <li>
                    <span>@lang('Product Name'):</span>
                    <a href="{{ route('product.details', $orderItem->product->slug) }}">{{ __($orderItem->product->title) }}</a>
                </li>
                <li>
                    <span>@lang('Buyer Name'):</span>
                    <a href="{{ route('user.profile', $orderItem->buyer->username) }}">{{ __($orderItem->buyer->fullname) }}</a>
                </li>
                <li>
                    <span>@lang('Buyer Email'):</span>
                    <span>{{ $orderItem->buyer->email }}</span>
                </li>
                <li>
                    <span>@lang('Quantity'):</span>
                    <span>{{ $orderItem->quantity }}</span>
                </li>
                <li>
                    <span>@lang('License Type'):</span>
                    <span>{{ $orderItem->license == Status::PERSONAL_LICENSE ? 'Personal' : 'Commercial' }}</span>
                </li>
                <li>
                    <span>@lang('Price'):</span>
                    <span>{{ gs('cur_sym') . showAmount($orderItem->product_price, currencyFormat: false) }}</span>
                </li>
                <li>
                    <span>@lang('Purchased At'):</span>
                    <span>{{ showDateTime($orderItem->created_at) }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
