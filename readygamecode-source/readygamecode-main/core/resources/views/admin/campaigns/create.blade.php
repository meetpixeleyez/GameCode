@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('admin.campaign.store', $campaign->id ?? 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Campaign Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', @$campaign->name) }}" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Minimum discount percentage')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="discount_min" value="{{ old('discount_min', @$campaign->discount_min) }}" required>
                                        <span class="input-group-text" id="discount_type_text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Maximum discount percentage')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="discount_max" value="{{ old('discount_max', @$campaign->discount_max) }}" required>
                                        <span class="input-group-text" id="discount_type_text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Start Date')</label>
                                    <input type="text" name="start_date" class="date-event form-control" data-language='en' data-format="yyyy-mm-dd" data-position='bottom left' value="{{ old('start_date', showDateTime(@$campaign->start_date, 'Y-m-d')) }}" placeholder="@lang('Select Date')" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('End Date')</label>
                                    <input type="text" name="end_date" class="date-event form-control" data-language='en' data-format="yyyy-mm-dd" data-position='bottom left' value="{{ old('end_date', showDateTime(@$campaign->end_date, 'Y-m-d')) }}" placeholder="@lang('Select Date')" autocomplete="off" required>
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
    <x-back route="{{ route('admin.campaign.index') }}" />
@endpush

@push('style')
    <style>
        .datepicker {
            z-index: 9999;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let today = new Date();
            $('.date-event').datepicker({
                language: 'en',
                dateFormat: 'yyyy-mm-dd',
                minDate: today
            });
        })(jQuery);
    </script>
@endpush
