@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-md-6">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="card-title">@lang('Razorpay')</h6>
                </div>
                <div class="card-body p-5">
                    <ul class="list-group text-center">
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('You have to pay '):
                            <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                {{ __($deposit->method_currency) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('You will get '):
                            <strong>{{ showAmount($deposit->amount) }}</strong>
                        </li>
                    </ul>
                    <form action="{{ $data->url }}" method="{{ $data->method }}" id="razorpay-form">
                        <input type="hidden" custom="{{ $data->custom }}" name="hidden">
                        <script src="{{ $data->checkout_js }}"></script>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function() {
            "use strict";
            
            // Auto-open Razorpay payment window
            var options = {
                @foreach($data->val as $key => $value)
                "{{ $key }}": "{{ $value }}",
                @endforeach
                "handler": function (response){
                    // Create form to submit payment details
                    var form = document.getElementById('razorpay-form');
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'razorpay_payment_id';
                    input.value = response.razorpay_payment_id;
                    form.appendChild(input);
                    
                    var input2 = document.createElement('input');
                    input2.type = 'hidden';
                    input2.name = 'razorpay_order_id';
                    input2.value = response.razorpay_order_id;
                    form.appendChild(input2);
                    
                    var input3 = document.createElement('input');
                    input3.type = 'hidden';
                    input3.name = 'razorpay_signature';
                    input3.value = response.razorpay_signature;
                    form.appendChild(input3);
                    
                    form.submit();
                },
                "modal": {
                    "ondismiss": function(){
                        // If user closes the popup, redirect back
                        window.location.href = "{{ $deposit->failed_url }}";
                    }
                }
            };
            
            // Wait for Razorpay script to load, then open payment
            if (typeof Razorpay !== 'undefined') {
                var razorpayCheckout = new Razorpay(options);
                razorpayCheckout.open();
            } else {
                // If script hasn't loaded yet, wait for it
                window.addEventListener('load', function() {
                    if (typeof Razorpay !== 'undefined') {
                        var razorpayCheckout = new Razorpay(options);
                        razorpayCheckout.open();
                    }
                });
                
                // Fallback: trigger after short delay
                setTimeout(function() {
                    if (typeof Razorpay !== 'undefined') {
                        var razorpayCheckout = new Razorpay(options);
                        razorpayCheckout.open();
                    }
                }, 500);
            }
        })();
    </script>
@endpush
