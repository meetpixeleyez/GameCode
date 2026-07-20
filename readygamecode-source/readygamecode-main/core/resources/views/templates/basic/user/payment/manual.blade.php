@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-md-8">
            <div class="card custom--card">
                <div class="card-header card-header-bg">
                    <h6 class="card-title">{{ __($pageTitle) }}</h6>
                </div>
                <div class="card-body  ">
                    <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-primary">
                                    <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting')
                                        <b>{{ showAmount($data['amount']) }}</b> @lang('to deposit.') @lang('Please pay')
                                        <b>{{ showAmount($data['final_amount'], currencyFormat: false) . ' ' . $data['method_currency'] }}
                                        </b> @lang('for successful payment.')
                                    </p>
                                </div>
                                <div class="mb-3" style="text-align: center; width: 300px;">@php echo  $data->gateway->description @endphp</div>
                            </div>

                            <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100">@lang('Pay Now')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
