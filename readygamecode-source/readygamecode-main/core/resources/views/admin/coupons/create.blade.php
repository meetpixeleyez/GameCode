@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('admin.coupon.store', $coupon->id ?? 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Coupon Name')</label>
                                    <input type="text" class="form-control" name="name"
                                           value="{{ old('name', @$coupon->name) }}" placeholder="@lang('Type Here')..."
                                           required />
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Coupon Code')</label>
                                    <input type="text" class="form-control" name="code"
                                           value="{{ old('code', @$coupon->code) }}" placeholder="@lang('Type Here')..."
                                           required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Discount Type')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1"
                                            name="discount_type" required>
                                        <option value="">@lang('Select Discount Type')</option>
                                        <option value="1"
                                                {{ old('discount_type', @$coupon->discount_type) == '1' ? 'selected' : '' }}>
                                            @lang('Fixed')
                                        </option>
                                        <option value="2"
                                                {{ old('discount_type', @$coupon->discount_type) == '2' ? 'selected' : '' }}>
                                            @lang('Percentage')
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Amount')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="amount"
                                               value="{{ old('amount', getAmount(@$coupon->amount)) }}"
                                               placeholder="@lang('Type Here')..." required>
                                        <span class="input-group-text" id="discount_type_text">
                                            {{ @$coupon ? ($coupon->discount_type == 1 ? gs()->cur_text : '%') : gs()->cur_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Start Date')</label>
                                    <input type="text" name="start_date" class="pickupDate form-control start_date"
                                           data-language='en' data-format="yyyy-mm-dd" data-position='bottom left'
                                           value="{{ old('start_date', showDateTime(@$coupon->start_date, 'Y-m-d')) }}"
                                           placeholder="@lang('Select Date')" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('End Date')</label>
                                    <input type="text" name="end_date" class="pickupDate form-control end_date"
                                           data-language='en' data-format="yyyy-mm-dd" data-position='bottom left'
                                           value="{{ old('end_date', showDateTime(@$coupon->end_date, 'Y-m-d')) }}"
                                           placeholder="@lang('Select Date')" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="3">{{ old('description', @$coupon->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card my-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Usage Restrictions')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Minimum Spend')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="minimum_spend"
                                               value="{{ old('minimum_spend', getAmount(@$coupon->minimum_spend)) }}"
                                               placeholder="@lang('Type Here')..." min="0" required>
                                        <span class="input-group-text">{{ gs()->cur_text }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Usage Limit Per Coupon')</label>
                                    <input type="number" class="form-control" name="usage_limit_per_coupon"
                                           value="{{ old('usage_limit_per_coupon', @$coupon->usage_limit_per_coupon) }}"
                                           placeholder="@lang('Type Here')...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Usage Limit Per Customer')</label>
                                    <input type="number" class="form-control" name="usage_limit_per_customer"
                                           value="{{ old('usage_limit_per_customer', @$coupon->usage_limit_per_user) }}"
                                           placeholder="@lang('Type Here')...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.coupon.index') }}" />
@endpush

@push('style')
    <style>
        .datepicker {
            z-index: 9999;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('select[name="discount_type"]').on('change', function() {
                var discountType = $(this).val();
                var textElement = $('#discount_type_text');
                if (discountType == 1) {
                    textElement.text('{{ gs()->cur_text }}');
                } else if (discountType == 2) {
                    textElement.text('%');
                } else {
                    textElement.text('{{ gs()->cur_text }}');
                }
            });

            let today = new Date();

            $(".start_date").datepicker({
                language: "en",
                dateFormat: "yyyy-mm-dd",
                minDate: today,
                onSelect: function(selectedDate) {
                    $(".end_date").datepicker().data("datepicker").update("minDate", new Date(
                        selectedDate));
                },
            });

            $(".end_date").datepicker({
                language: "en",
                dateFormat: "yyyy-mm-dd",
                minDate: today,
            });

        })(jQuery);
    </script>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush
