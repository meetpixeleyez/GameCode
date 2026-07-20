@extends($activeTemplate . 'layouts.frontend')
@section('meta_title', gs()->siteName(__('Order Confirmation')))
@section('meta_description', __('Thank you for your purchase. Your order confirmation and receipt information are available on this page.'))
@section('meta_robots', 'noindex, follow')

@section('content')
<section class="checkout-thank-you-page pt-60 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="thank-you-wrapper">
                    <div class="thank-you-content text-center">
                        <div class="success-icon mb-4">
                            <i class="las la-check-circle"></i>
                        </div>
                        <h1 class="success-title mb-3">@lang('Thank You for Your Purchase!')</h1>
                        <p class="success-message mb-4">
                            @lang('Your order has been successfully processed and you will receive an email confirmation shortly.')
                        </p>
                        
                        @if ($order)
                            <div class="order-details mb-4">
                                <h5 class="mb-3">@lang('Order Details')</h5>
                                <div class="order-info bg-light p-4 rounded">
                                    <div class="row text-start">
                                        <div class="col-md-6 mb-2">
                                            <strong>@lang('Order Number'):</strong>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            {{ $order->trx }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>@lang('Order Date'):</strong>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            {{ $order->created_at->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>@lang('Total Amount'):</strong>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            {{ gs('cur_sym') }}{{ showAmount($order->amount) }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>@lang('Payment Status'):</strong>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            @if($order->payment_status == \App\Constants\Status::PAYMENT_SUCCESS)
                                                <span class="badge bg-success">@lang('Paid')</span>
                                            @else
                                                <span class="badge bg-warning">@lang('Pending')</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if(count($order->orderItems) > 0)
                                    <div class="order-items mt-4">
                                        <h6 class="mb-3">@lang('Order Items')</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('Product')</th>
                                                        <th>@lang('Price')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->orderItems as $item)
                                                    <tr>
                                                        <td>{{ $item->product->title ?? $item->title }}</td>
                                                        <td>{{ gs('cur_sym') }}{{ showAmount($item->product_price + $item->extended_amount) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="action-buttons">
                            <a href="{{ route('user.order.list') }}" class="btn btn--base btn--lg me-3 mb-2">
                                <i class="las la-list me-2"></i>
                                @lang('View My Orders')
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary btn--lg mb-2">
                                <i class="las la-shopping-bag me-2"></i>
                                @lang('Continue Shopping')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>
.checkout-thank-you-page {
    background: #f8f9fa;
    min-height: 70vh;
}

.thank-you-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.success-icon i {
    font-size: 80px;
    color: #28a745;
}

.success-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
}

.success-message {
    font-size: 16px;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.order-details {
    text-align: left;
}

.order-details h5 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
}

.order-info {
    font-size: 14px;
}

.order-items h6 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.action-buttons {
    margin-top: 30px;
}

.action-buttons .btn {
    min-width: 180px;
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .thank-you-wrapper {
        padding: 30px 20px;
    }
    
    .success-title {
        font-size: 24px;
    }
    
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 15px;
    }
    
    .action-buttons .btn:last-child {
        margin-bottom: 0;
    }
}
</style>
@endpush

