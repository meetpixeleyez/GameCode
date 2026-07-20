@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <h6 class="mb-0">{{ __($pageTitle) }}</h6>
                <a href="{{ route('user.author.download') }}"
                    class="btn btn-outline--base btn--sm">
                    <i class="la la-rotate-left"></i> @lang('Back')
                </a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="order-items-list">
                    <div class="order-item p-3">
                        <ul class="license-details-list">
                            <li>
                                <span>@lang('Purchase Code'):</span>
                                <span>{{ $purchasedDetails->purchase_code }}</span>
                            </li>
                            <li>
                                <span>@lang('Product Name'):</span>
                                <a href="{{ route('product.details', @$purchasedDetails->product->slug) }}">{{ __(@$purchasedDetails->product->title) }}</a>
                            </li>
                            <li>
                                <span>@lang('Author\'s Name'):</span>
                                <a href="{{ route('user.profile', @$purchasedDetails->product->author->username) }}">{{ __(@$purchasedDetails->product->author->username) }}</a>
                            </li>
                            <li>
                                <span>@lang('Author\'s Email'):</span>
                                <span>{{ @$purchasedDetails->product->author->email }}</span>
                            </li>
                            <li>
                                <span>@lang('Quantity'):</span>
                                <span>{{ $purchasedDetails->quantity }}</span>
                            </li>
                            <li>
                                <span>@lang('License Type'):</span>
                                <span>{{ $purchasedDetails->license == Status::PERSONAL_LICENSE ? 'Personal' : 'Commercial' }}</span>
                            </li>
                            <li>
                                <span>@lang('Price'):</span>
                                <span>{{ gs('cur_sym') . showAmount($purchasedDetails->product_price + $purchasedDetails->buyer_fee + $purchasedDetails->extended_amount, currencyFormat: false) }}</span>
                            </li>
                            <li>
                                <span>@lang('Purchased At'):</span>
                                <span>{{ showDateTime($purchasedDetails->created_at) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
