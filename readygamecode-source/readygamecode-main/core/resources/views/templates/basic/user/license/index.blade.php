@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-6">
            <div class="common-sidebar">
                <div class="common-sidebar__item">
                    <h6 class="common-sidebar__title text-center">@lang('Verify License')</h6>
                    <div class="common-sidebar__content">
                        <form class="searchPurchaseForm" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Purchase Code')</label>
                                <input type="text" class="form--control form--control--sm" name="purchase_code"
                                       value="{{ old('purchase_code') }}" placeholder="@lang('Enter Purchase Code....')" required>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn--base btn--sm searchPurchase">
                                    @lang('Verify')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-6">
            <div class="mt-3 resultArea"></div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.searchPurchaseForm').on('submit', function(e) {
                e.preventDefault();

                const purchaseCode = $('input[name=purchase_code]').val();
                const csrfToken = $('input[name=_token]').val();

                if (!purchaseCode) {
                    alert('Please enter a purchase code.');
                    return;
                }

                submitRequest(purchaseCode, csrfToken);
            });

            $('.searchPurchase').on('click', function(e) {
                e.preventDefault();

                const purchaseCode = $('input[name=purchase_code]').val();
                const csrfToken = $('input[name=_token]').val();

                if (!purchaseCode) {
                    alert('Please enter a purchase code.');
                    return;
                }

                submitRequest(purchaseCode, csrfToken);
            });


            function submitRequest(purchaseCode, csrfToken) {
                $.ajax({
                    url: "{{ route('user.author.license.verify') }}",
                    type: "GET",
                    data: {
                        purchase_code: purchaseCode,
                        _token: csrfToken
                    },
                    beforeSend: function() {
                        $('.searchPurchase').prop('disabled', true).text('Verifying...');
                    },
                    success: function(response) {
                        $('.searchPurchase').prop('disabled', false).text('Verify');
                        if (response.error) {
                            $('.resultArea').html(
                                `<div class="alert alert-danger">${response.error}</div>`
                            );
                        } else {
                            $('.resultArea').html(response.html);
                        }
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
