@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Author / Product Title')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Type')</label>
                                <select name="is_free" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @selected(request()->is_free == '1')>@lang('Free')</option>
                                    <option value="0" @selected(request()->is_free == '0')>@lang('Paid')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Upload Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Product | Upload Date')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Price/Commercial Price')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb me-2">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . productFilePath($product, 'thumbnail')) }}"
                                                        alt="@lang('Product Image')">
                                                </div>
                                                <div>
                                                    <a
                                                        href="{{ route('product.details', $product->slug) }}">{{ __(strLimit($product->title, 20)) }}</a>
                                                    <br>
                                                    <span
                                                        class="text--small">{{ showDateTime($product->created_at) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$product->author->fullname) }}</span>
                                            <br>
                                            <span>
                                                <a
                                                    href="{{ route('admin.users.detail', $product->author->id) }}"><span>@</span>{{ $product->author->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                {{ __(@$product->subCategory->name) }} <br>
                                                <span>{{ __(@$product->category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if (!$product->is_free)
                                                {{ showAmount($product->price) }} /
                                                {{ showAmount($product->price_cl) }}
                                            @else
                                                <span class="badge badge--success">@lang('Free')</span>
                                            @endif
                                        </td>
                                        <td>@php echo $product->statusBadge @endphp</td>
                                        <td>
                                            @php echo displayRating($product->avg_rating) @endphp <br>
                                            @if ($product->isTrending()) <span class="badge badge--warning">@lang('Trending')</span> @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.product.details', $product->slug) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i>
                                                    @lang('Details')
                                                </a>
                                                <button
                                                    class="btn btn-outline--{{ $product->is_featured ? 'danger' : 'info' }} btn-sm confirmationBtn"
                                                    data-question="@lang('Are you sure to ' . ($product->is_featured ? 'unfeature' : 'feature') . ' this product?')"
                                                    data-action="{{ route('admin.product.feature.toggle', $product->id) }}"
                                                    type="button">
                                                    <i
                                                        class="las la-{{ $product->is_featured ? 'eye-slash' : 'eye' }}"></i>
                                                    @lang($product->is_featured ? 'Unfeature' : 'Feature')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($products->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($products) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush
